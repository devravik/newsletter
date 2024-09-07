<?php

namespace App\Jobs;

use App\Models\CampaignMail;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddMailCampaignToQueueJob implements ShouldQueue
{
    use Queueable;

    public $campaign;
    public $contact;

    /**
     * Create a new job instance.
     */
    public function __construct($campaign, $contact)
    {
        $this->campaign = $campaign;
        $this->contact = $contact;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // check if the contact is already added to the campaign mail queue
        $campaignMailExists = CampaignMail::where('campaign_id', $this->campaign->id)
            ->where('contact_id', $this->contact->id)
            ->count();

        if ($campaignMailExists) {
            return;
        }

        // Add the contact to the campaign mail queue
        $campaign = $this->campaign;
        $contact = $this->contact;
        $campaignMail = new CampaignMail();
        $campaignMail->campaign_id = $campaign->id;
        $campaignMail->contact_id = $contact->id;
        $campaignMail->email = $contact->email;
        $campaignMail->subject = $campaign->subject;
        $campaignMail->from_name = $campaign->from_name;
        $campaignMail->from_email = $campaign->from_email;
        $campaignMail->template = $campaign->template;
        $campaignMail->reply_to = $campaign->reply_to;
        $campaignMail->scheduled_at = $this->getNextScheduledDate();
        $campaignMail->status = 'pending';
        $campaignMail->save();
    }

    function getNextScheduledDate()
    {
        $limitPerDay = env('EMAILS_PER_DAY', 30);
        $lastCampaignMail = CampaignMail::latest()->first();
        if (!$lastCampaignMail) {
            return now();
        }
        $lastScheduledAt = $lastCampaignMail->scheduled_at ?? now();
        $nextScheduledAt = Carbon::parse($lastScheduledAt)->addMinutes(24 * 60 / $limitPerDay);
        return $nextScheduledAt;
    }

    
}
