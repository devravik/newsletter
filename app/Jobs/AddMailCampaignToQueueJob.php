<?php

namespace App\Jobs;

use App\Models\CampaignMail;
use App\Models\Unsubscribe;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

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
        // add check if contact is unsubscribed
        $unsubscribed = Unsubscribe::where('email', $this->contact->email)->count();
        if ($unsubscribed) {
            return;
        }

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

        // Check if we already have the last scheduled date in cache
        $lastScheduledAt = Cache::get('last_scheduled_at');

        if (!$lastScheduledAt) {
            $lastCampaignMail = CampaignMail::latest()->first();
            $lastScheduledAt = $lastCampaignMail->scheduled_at ?? now();

            // Cache the last scheduled at for a few seconds to avoid multiple queries
            Cache::put('last_scheduled_at', $lastScheduledAt, 60);
        }

        // Calculate next scheduled time
        $nextScheduledAt = Carbon::parse($lastScheduledAt)->addMinutes(24 * 60 / $limitPerDay);

        // Update cache with the new scheduled time
        Cache::put('last_scheduled_at', $nextScheduledAt, 60);

        return $nextScheduledAt;
    }

    
}
