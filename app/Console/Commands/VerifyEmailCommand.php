<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Services\EmailVerificationService;
use Illuminate\Console\Command;

class VerifyEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-email-command';

    public $emailVerificationService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    function __construct(EmailVerificationService $emailVerificationService)
    {
        parent::__construct();
        $this->emailVerificationService = $emailVerificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Contact::where(['status'=>1])->oldest()->chunk(1000, function ($contacts) {
            foreach ($contacts as $contact) {
                $delete = false;
                if($this->emailVerificationService->verifyEmail($contact->email)) {
                    $delete = true;
                }
                if ($delete) {
                    $this->error("invalid email: $contact->email");
                    $contact->update(['status' => 3]);
                } else {
                    $contact->update(['status' => 2]);
                }
                $this->info("Processed contact with email: $contact->email");
            }
        });
    }
}
