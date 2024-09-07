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

        // List of blacklisted words
        $blacklisted_words = ['planolitix', 'gighq', 'waggingtail', 'skillsnacks', 'vetty', 'sellcodes', 'inisev', 'fonefix'];

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
