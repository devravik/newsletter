<?php

namespace App\Console\Commands;

use App\Jobs\CampaignMailQueueJob;
use App\Models\CampaignMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-mail-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process mail queue by sending emails to contacts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all campaign mails that are not yet sent
        $campaignMails = CampaignMail::whereNull('sent_at')->where('scheduled_at','<',now())->get();

        foreach ($campaignMails as $campaignMail) {

            // Send CampaignMailQueueJob
            dispatch(new CampaignMailQueueJob($campaignMail));

            
        }
    }
}
