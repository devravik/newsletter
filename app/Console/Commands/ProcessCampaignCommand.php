<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\CampaignMail;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessCampaignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-campaign-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process a campaign by sending adding contacts to the campaign mail queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get all campaigns that are not yet sent
        $campaigns = Campaign::where([
            ['status', 'draft'],
            ['sent_at', '<', now()],
        ])->get();

        foreach ($campaigns as $campaign) {

            // Get all contacts that are not yet added to the campaignMails
            $contacts = Contact::whereDoesntHave('campaignMails', function ($query) use ($campaign) {
                $query->where('campaign_id', $campaign->id);
            });

            // Filter contacts by contact_filters
            $contactFilters = $campaign->contact_filters;
            if (!empty($contactFilters)) {
                $contacts = $contacts->where(function ($query) use ($contactFilters) {
                    foreach ($contactFilters as $key => $value) {
                        // Check if the value is meta. If so, filter by meta data
                        if (strpos($key, 'meta.') === 0) {
                            $key = str_replace('meta.', '', $key);
                            $query->whereHas('meta', function ($query) use ($key, $value) {
                                $query->where('key', $key)->where('value', $value);
                            });
                        } else {
                            $query->where($key, $value);
                        }
                    }
                });
            }

            $contacts = $contacts->get();

            foreach ($contacts as $contact) {
                // Add the contact to the campaign mail queue
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

            // add contact count to the campaign report
            $campaign->report = [
                'contact_count' => $contacts->count(),
            ];

            // Update the campaign status to 'processing'

            $campaign->status = 'processing';
            $campaign->save();
        }
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
