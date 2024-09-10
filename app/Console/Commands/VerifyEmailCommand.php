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
        Contact::oldest()->chunk(1000, function ($contacts) {
            foreach ($contacts as $contact) {
                $engScore = $this->emailVerificationService->evaluateEmailEngagement($contact->email);
                $contact->update(['eng_score' => $engScore]);
                $this->info("Updated contact with email: $contact->email with engagement score: $engScore");
            }
        });
    }
}
