<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestartMySQL extends Command
{
    // The name and signature of the console command
    protected $signature = 'mysql:restart';

    // The console command description
    protected $description = 'Restart the MySQL service';

    // Create a new command instance
    public function __construct()
    {
        parent::__construct();
    }

    // Execute the console command
    public function handle()
    {
        
        // Check if mysql connection is working
        try {
            DB::connection()->getPdo();
            return;
        } catch (\Exception $e) {
            $this->error('Failed to connect to MySQL database.');
            $this->error($e->getMessage());
        }
        
        // Check if the script is running with root/sudo privileges
        if (posix_geteuid() !== 0) {
            $this->error('You need to run this command as root or with sudo privileges.');
            return 1;
        }

        

        // Execute the command to restart MySQL service
        $this->info('Restarting MySQL service...');

        // Using shell_exec to run the system command
        $output = shell_exec('service mysql restart 2>&1');

        // Check for errors and give feedback
        if (strpos($output, 'failed') !== false) {
            $this->error('Failed to restart MySQL service.');
            $this->error($output);
            return 1;
        }

        $this->info('MySQL service restarted successfully.');
        return 0;
    }
}
