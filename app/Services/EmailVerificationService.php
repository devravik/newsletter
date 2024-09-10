<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class EmailVerificationService
{
    protected $cacheTime = 60; // Cache time in minutes


    function evaluateEmailEngagement($email)
    {
        // Check if the email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 0; // Invalid email
        }

        // Extract the local part of the email
        $localPart = substr($email, 0, strpos($email, '@'));

        // Engagement levels
        $engagementScore = 5;

        // List of common throwaway patterns
        $commonPatterns = [
            '/^[0-9]{1,4}$/', // Too short and numeric (e.g., "1234")
            '/^[0-9]{5,}$/',  // Long numeric (e.g., "123456789")
            '/^[a-z0-9]+$/i', // Plain letters or numbers without dots or symbols (e.g., "user123")
        ];

        // Check if the local part matches common patterns of low-engagement emails
        foreach ($commonPatterns as $pattern) {
            if (preg_match($pattern, $localPart)) {
                $engagementScore -= 3; // Reduce score for generic/randomized patterns
                break;
            }
        }

        // Detect if the local part contains meaningful names or words
        if (preg_match('/[a-zA-Z]{3,}/', $localPart)) {
            $engagementScore += 2; // Bonus for meaningful words (e.g., first or last names)
        }

        // Check if the local part has more than 3 digits in a row
        if (preg_match('/[0-9]{3,}/', $localPart)) {
            $engagementScore -= 1; // Reduce score if there are too many digits
        }

        // Check if it looks like a temporary or disposable email address
        if (preg_match('/^test|temp|dummy|fake|throwaway/i', $localPart)) {
            return 0; // These are clear throwaway emails, return lowest engagement
        }

        // Check if the local part has a professional structure (e.g., name.surname)
        if (preg_match('/^[a-zA-Z]+(\.[a-zA-Z]+)+$/', $localPart)) {
            $engagementScore += 2; // Bonus for structured/professional local parts like name.surname
        }

        // Ensure the score is within the valid range (0 to 5)
        $engagementScore = max(0, min(5, $engagementScore));

        return $engagementScore;
    }



    /**
     * Verify if the given email address is valid via SMTP.
     * @param string $email
     * @return bool
     */
    public function verifyEmail($email)
    {
        if (!$this->is_valid_email($email)) {
            return false;
        }

        // Check the cache first
        $cacheKey = 'email_verification_' . md5($email);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $isValid = $this->verifyEmailSmtp($email);

        // Cache the result
        Cache::put($cacheKey, $isValid, $this->cacheTime);

        return $isValid;
    }

    /**
     * Perform SMTP verification of the email address.
     * @param string $email
     * @return bool
     */
    protected function verifyEmailSmtp($email)
    {

        $domain = substr(strrchr($email, '@'), 1);
        $hostname = gethostbyname($domain);

        if ($hostname === $domain) {
            return false;
        }

        // List of trusted domains to skip MX check (e.g. known providers)
        $trusted_domains = [
            'gmail.com',
            'yahoo.com',
            'outlook.com',
            'hotmail.com',
            'aol.com',
            'icloud.com',
            'protonmail.com',
            'gmx.com',
            'mail.com',
            'zoho.com',
            'yandex.com',
            'fastmail.com',
            'me.com',
            'live.com',
            'msn.com',
            'rocketmail.com',
            'mac.com',
            'qq.com',
            '163.com',
            '126.com',
            'sina.com',
            'rediffmail.com',
            'btinternet.com',
            'comcast.net',
            'verizon.net',
            'att.net',
            't-online.de',
            'web.de',
            'mail.ru',
            'inbox.com',
            'libero.it',
            'orange.fr',
            'wanadoo.fr'
        ];

        if (in_array(strtolower($domain), $trusted_domains)) {
            return true;
        }

        $mxRecords = [];
        if (getmxrr($domain, $mxRecords)) {
            foreach ($mxRecords as $mxHost) {
                $connection = @fsockopen($mxHost, 25, $errno, $errstr, 5);

                if ($connection) {
                    fwrite($connection, "HELO happendesk.com\r\n");
                    fwrite($connection, "MAIL FROM:<owner@happendesk.com>\r\n");
                    fwrite($connection, "RCPT TO:<$email>\r\n");
                    $response = fgets($connection);

                    if (strpos($response, '250') === 0) {
                        fclose($connection);
                        return true;
                    }

                    fclose($connection);
                }
            }
        }

        return false;
    }


    public function is_valid_email($email)
    {
        // Regex to match valid email addresses excluding the plus (+) sign in the local part
        $valid_email_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        // Trim any whitespace characters from the email
        $email = trim($email);

        // List of blacklisted words (case-insensitive)
        $blacklisted_words = ['planolitix', 'gighq', 'waggingtail', 'skillsnacks', 'vetty', 'sellcodes', 'inisev', 'fonefix', 'newstly'];

        // Merged list of blacklisted and disposable domains
        $blacklisted_domains = [
            'sample.com',
            'example.com',
            'mailinator.com',
            '10minutemail.com',
            'yopmail.com',
            'guerrillamail.com',
            'dispostable.com',
            'tempmail.com',
            'throwawaymail.com',
            'getnada.com',
            'sharklasers.com',
            'spambog.com',
            'trashmail.com',
            'mytrashmail.com',
            'fakeinbox.com',
            'maildrop.cc',
            'mintemail.com',
            'trbvm.com',
            'emailondeck.com',
            'yopmail.net',
            'mailcatch.com',
            'mailnesia.com',
            'spamex.com',
            'boun.cr',
            'discard.email',
            'temporary-mail.net',
            'mail-temporaire.fr',
            'tmail.com',
            'jetable.org',
            'zoemail.org',
            'fakemailgenerator.com',
            'onet.pl'
        ];

        // Expanded list of role-based email prefixes
        $role_based_prefixes = [
            'support',
            'contact',
            'webmaster',
            'sales',
            'marketing',
            'postmaster',
            'billing',
            'helpdesk',
            'noreply',
            'no-reply',
            'abuse',
            'team',
            'privacy',
            'security',
            'complaints',
            'hr',
            'jobs',
            'recruit',
            'legal',
            'service',
            'services',
            'root',
            'sysadmin',
            'feedback',
            'customerservice',
            'operations',
            'office',
            'registrar',
            'hostmaster',
            'mis',
            'network',
            'adminstrator',
            'it',
            'emailadmin',
            'enquiries',
            'kontakt'
        ];

        // List of trusted domains to skip MX check (e.g. known providers)
        $trusted_domains = [
            'gmail.com',
            'yahoo.com',
            'outlook.com',
            'hotmail.com',
            'aol.com',
            'icloud.com',
            'protonmail.com',
            'gmx.com',
            'mail.com',
            'zoho.com',
            'yandex.com',
            'fastmail.com',
            'me.com',
            'live.com',
            'msn.com',
            'rocketmail.com',
            'mac.com',
            'qq.com',
            '163.com',
            '126.com',
            'sina.com',
            'rediffmail.com',
            'btinternet.com',
            'comcast.net',
            'verizon.net',
            'att.net',
            't-online.de',
            'web.de',
            'mail.ru',
            'inbox.com',
            'libero.it',
            'orange.fr',
            'wanadoo.fr'
        ];

        // List of known spam traps (emails or patterns)
        $spam_trap_emails = [
            'trap@example.com',
            'spamtrap@mailinator.com',
            'nospam@example.com',
            'abuse@example.com',
            'test@spamtrap.com',
            'blackhole@example.com',
            'null@example.com',
            'bounce@spamtrap.com',
            'spamtrap@spamcop.net',
            'trapme@spamtrap.com',
            'report@spamhaus.org'
        ];

        // List of known spam trap domains
        $spam_trap_domains = [
            'spamtrapdomain.com',
            'trap.com',
            'spamtrap.com',
            'example.net',
            'testmail.com',
            'antispam.com',
            'spamcop.net',
            'spamhaus.org',
            'bounceme.com',
            'blackhole.com',
            'spambounce.com',
            'dnsbl.com',
            'bademail.com',
            'trapmail.com',
            'honeypot.com',
            'bouncer.mail.com'
        ];



        // Step 1: Check if the email matches the valid regex and does not contain the "+" character
        if (preg_match($valid_email_regex, $email) && strpos($email, '+') === false) {
            // Extract local part (prefix) and domain part of the email
            $prefix = substr($email, 0, strpos($email, '@'));
            $domain = substr(strrchr($email, '@'), 1);

            // Step 2: Check for suspicious characters in the email prefix
            if (!preg_match('/^[-_#]/', $prefix)) {

                // Step 3: Check if the email contains any blacklisted word
                foreach ($blacklisted_words as $word) {
                    if (stripos($email, $word) !== false) {
                        return false; // Invalid if blacklisted word found
                    }
                }

                // Step 4: Check if the domain is in the blacklist (including disposable domains)
                if (in_array(strtolower($domain), $blacklisted_domains)) {
                    return false; // Invalid if domain is blacklisted or disposable
                }

                // Step 5: Check if the prefix is role-based
                if (in_array(strtolower($prefix), $role_based_prefixes)) {
                    return false; // Invalid if role-based email address
                }

                // Step 6: Check if the email or domain is a known spam trap
                if (in_array(strtolower($email), $spam_trap_emails) || in_array(strtolower($domain), $spam_trap_domains)) {
                    return false; // Invalid if email or domain matches known spam traps
                }

                // Step 7: Skip MX check for trusted domains
                if (!in_array(strtolower($domain), $trusted_domains)) {
                    // Step 7: Check if the domain has valid MX records (this ensures the domain can receive emails)
                    if (!checkdnsrr($domain, 'MX')) {
                        return false; // Invalid if no valid MX records are found
                    }
                }

                return true; // Email passed all checks
            }
        }

        return false; // Email didn't pass validation
    }
}
