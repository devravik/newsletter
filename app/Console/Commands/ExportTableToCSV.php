<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportTableToCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:csv 
                            {db_host} 
                            {db_name} 
                            {db_username} 
                            {db_password} 
                            {table_name} 
                            {columns} 
                            {--output=export.csv : The name of the output CSV file}
                            {--chunk=1000 : Number of rows to process in each chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export data from a table to a CSV file with specific columns in chunks to handle large tables.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Gather inputs
        $dbHost = $this->argument('db_host');
        $dbName = $this->argument('db_name');
        $dbUsername = $this->argument('db_username');
        $dbPassword = $this->argument('db_password');
        $tableName = $this->argument('table_name');
        $columns = $this->argument('columns');
        $outputFile = $this->option('output');
        $chunkSize = (int) $this->option('chunk'); // Number of rows to process per chunk

        // Establish temporary database connection
        config([
            'database.connections.custom_mysql' => [
                'driver' => 'mysql',
                'host' => $dbHost,
                'database' => $dbName,
                'username' => $dbUsername,
                'password' => $dbPassword,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
            ]
        ]);

        DB::purge('custom_mysql');
        DB::reconnect('custom_mysql');

        // Convert columns to an array
        // $columnsArray = explode(',', $columns);
        $columnsArray = [
            'first_name',
            'last_name',
            'email',
            'phone',
            'city',
            'country',
            'suburb',
            'postal_code',
            'address',
            'source',
        ];
        $emails = [];

        try {
            // Open the file for writing
            $file = fopen($outputFile, 'w');

            // Write the header to the CSV
            fputcsv($file, $columnsArray);

            // Process the table in chunks
            DB::connection('custom_mysql')->table($tableName)
                // ->select($columnsArray)
                ->orderBy('id') // Ensure predictable order
                ->chunk($chunkSize, function ($rows) use ($file, &$emails) {
                    foreach ($rows as $row) {
                        if(in_array($row->email, $emails) || empty($row->email)){
                            continue;
                        }
                        $data = [
                            'first_name' => $row->first_name ?? null,
                            'last_name' => $row->last_name ?? null,
                            'email' => $row->email ?? null,
                            'phone' => $row->contact_number ?? null,
                            'city' => $row->city ?? null,
                            'country' => 'New Zealand',
                            'suburb' => $row->suburb ?? null,
                            'postal_code' => $row->postal_code ?? null,                            
                            'address' => implode(', ',[$row->street_address , $row->city , $row->suburb , $row->postal_code , $row->country]),
                            'source' => 'Vetty',
                        ];
                        fputcsv($file, $data);
                        $emails[] = $row->email;                        
                    }
                });

            fclose($file); // Close the file after writing

            $this->info("CSV file generated successfully in chunks: {$outputFile}");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
