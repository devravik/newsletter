<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignMail;
use App\Models\Unsubscribe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CampaignMailQueueJob implements ShouldQueue
{
    use Queueable;

    public $campaignMail;

    /**
     * Create a new job instance.
     */
    public function __construct(CampaignMail $campaignMail)
    {
        $this->campaignMail = $campaignMail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaignMail = $this->campaignMail;
        $contact = $campaignMail->contact;

        if(!$contact) {
            $campaignMail->status = 'removed';
            $campaignMail->save();
            return;
        }

        // add check if contact is unsubscribed
        $unsubscribed = Unsubscribe::where('email', $contact->email)->count();
        if($unsubscribed) {
            $campaignMail->status = 'unsubscribed';
            $campaignMail->save();
            return;
        }

        if(!$this->is_valid_email($contact->email)) {
            $campaignMail->status = 'invalid';
            $campaignMail->save();

            // delete contact
            $contact->delete();
            return;
        }


        // Send email to contact
        $campaign = $campaignMail->campaign;
        $email = $contact->email;
        $subject = $campaignMail->subject;
        $body = $campaignMail->body;
        $fromName = $campaignMail->from_name;
        $fromEmail = $campaignMail->from_email;
        $replyTo = $campaignMail->reply_to;


        $template = 'emails.' . $campaignMail->template;

        // Send email template
        Mail::send($template, ['contact' => $contact, 'campaign' => $campaign, 'campaignMail' => $campaignMail], function ($message) use ($email, $subject, $fromName, $fromEmail, $replyTo) {
            $message->to($email);
            $message->subject($subject);
            $message->from($fromEmail, $fromName);
            $message->replyTo($replyTo);
        });

        // Update sent_at timestamp
        $campaignMail->sent_at = now();
        $campaignMail->status = 'sent';
        $campaignMail->save();

        // Update campaign status
        $this->updateCampaignStatus($campaign);
    }

    function updateCampaignStatus($campaign) {
        // get unsent campaign mails count
        $unsentCampaignMailsCount = CampaignMail::where('campaign_id', $campaign->id)->where('sent_at', null)->count();
        if($unsentCampaignMailsCount == 0) {
            // update campaign status to sent
            $campaign->status = 'sent';
            $campaign->save();
        }        
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

                // Step 5: Check if the email or domain is a known spam trap
                if (in_array(strtolower($email), $spam_trap_emails) || in_array(strtolower($domain), $spam_trap_domains)) {
                    return false; // Invalid if email or domain matches known spam traps
                }

                // Step 6: Skip MX check for trusted domains
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
