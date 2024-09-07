<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;

class CleanContactEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-contact-emails-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean contact emails by removing invalid emails and duplicates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Contact::oldest()->chunk(1000, function ($contacts) {
            foreach ($contacts as $contact) {
                $delete = false;
                if (empty($contact->email)) {
                    $delete = true;
                }
                if (!$this->is_valid_email($contact->email)) {
                    $delete = true;
                }
                if ($delete) {
                    $contact->delete();
                    $this->error("Deleted contact with invalid email: $contact->email");
                }

                // $this->info("Processed contact with email: $contact->email");

            }
        });
    }

    public function is_valid_email($email)
    {
        // Regex to match valid email addresses excluding the plus (+) sign in the local part
        $valid_email_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        // Trim any whitespace characters from the email
        $email = trim($email);

        // List of blacklisted words
        $blacklisted_words = ['planolitix', 'gighq', 'waggingtail', 'skillsnacks', 'vetty', 'sellcodes', 'inisev','fonefix','newstly'];

        // List of blacklisted domains
        $blacklisted_domains = ['sample.com', 'example.com'];

        // Check if the email matches the valid email regex and does not contain the "+" character
        if (preg_match($valid_email_regex, $email) && strpos($email, '+') === false) {
            // Additional check for suspicious characters in the email prefix
            $prefix = substr($email, 0, strpos($email, '@'));
            if (!preg_match('/^[-_#]/', $prefix)) {
                // Check if any blacklisted word is present in the email (case-insensitive)
                foreach ($blacklisted_words as $word) {
                    if (stripos($email, $word) !== false) {
                        return false; // Invalid if blacklisted word is found
                    }
                }

                // Extract the domain part from the email
                $domain = substr(strrchr($email, "@"), 1);

                // Check if the domain is in the blacklist
                if (in_array(strtolower($domain), $blacklisted_domains)) {
                    return false; // Invalid if domain is blacklisted
                }

                return true;
            }
        }

        return false;
    }
}
