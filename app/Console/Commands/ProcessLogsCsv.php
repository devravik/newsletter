<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use App\Models\Unsubscribe;
use League\Csv\Reader;

class ImportContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:logs {csv_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and process logs from a CSV file';

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

            $totalRecords = $reader->count();

            $records = $reader->getRecords($header);

            $this->info('cleaning contacts...'. $totalRecords);

            $unsubscribedEventTypes = ['Bounced', 'WaitingToRetry', 'Suppressed', 'Complaint', 'AbuseReport', 'Unsubscribed', 'Rejected'];

            foreach ($records as $record) {
                try {
                    $this->info('Processing record: ' . $record['to'].' - '.$record['eventtype']);
                    if (in_array($record['eventtype'], $unsubscribedEventTypes)) {
                        Unsubscribe::create([
                            'email' => $record['to'],
                        ]);
                        $contact = Contact::where('email', $record['to'])->delete();                        
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            $this->info('Contacts Cleaned successfully!');
        } catch (\Exception $e) {
            $this->error('Error cleaning contacts: ' . $e->getMessage());
        }
    }

}
