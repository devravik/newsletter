<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use League\Csv\Reader;

class ImportContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:contacts {csv_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports contacts from a CSV file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $csvFile = $this->argument('csv_file');

        try {
            $reader = Reader::createFromPath($csvFile);
            // Use the first row as headers
            $reader->setHeaderOffset(0);

            $header = $reader->getHeader();

            $chunkSize = 1000; // Adjust the chunk size as needed
            $offset = 0;
            $totalRecords = $reader->count();

            $this->info("Importing $totalRecords contacts...");

            $records = $reader->getRecords($header);

            $this->info('Importing contacts...'. $totalRecords);

            foreach ($records as $record) {
                $contact = new Contact();

                // if column name in csv file is in fillable array in Contact model then add value or add that as meta data

                $metaData = [];
                foreach ($record as $key => $value) {
                    $key = strtolower($key);
                    if (in_array($key, $contact->getFillable())) {
                        $contact->$key = $value;
                    } else {
                        // Add contact meta data
                        if (!empty($value)) {
                            $metaData[] = [
                                'key' => $key,
                                'value' => $value,
                            ];
                        }
                    }
                }

                if (empty($contact->email)) {
                    // $this->error('Email is required for contact');
                    continue;
                }

                if (!$this->is_valid_email($contact->email)) {
                    $this->error('Invalid email address: ' . $contact->email);
                    continue;
                }

                //check if contact already exists
                // $existingContact = Contact::where('email', $contact->email)->count();
                // if($existingContact){
                //     $this->error('Contact with email ' . $contact->email . ' already exists');
                //     continue;
                // }
                try {
                    $contact->save();
                    if (!empty($metaData)) {
                        $contact->metas()->createMany($metaData);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            $offset += $chunkSize;
            $this->info("Imported chunk $offset - $totalRecords");

            $this->info('Contacts imported successfully!');
        } catch (\Exception $e) {
            $this->error('Error importing contacts: ' . $e->getMessage());
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
