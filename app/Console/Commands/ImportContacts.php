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

            while ($offset < $totalRecords) {
                $records = $reader->getRecords($header, $chunkSize);

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
                            $metaData[] = [
                                'key' => $key,
                                'value' => $value,
                            ];                            
                        }
                    }

                    $contact->save();
                   
                    if (!empty($metaData)) {
                        $contact->metas()->createMany($metaData);
                    }
                }

                $offset += $chunkSize;
                $this->info("Imported chunk $offset - $totalRecords");
            }

            $this->info('Contacts imported successfully!');
        } catch (\Exception $e) {
            $this->error('Error importing contacts: ' . $e->getMessage());
        }
    }
}