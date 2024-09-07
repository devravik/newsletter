<?php

namespace App\Console\Commands;

use App\Jobs\AddMailCampaignToQueueJob;
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
            $contacts = Contact::latest();

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
            $count = 0;
            $contacts = $contacts->chunk(100, function ($contacts) use ($campaign, &$count) {
                foreach ($contacts as $contact) {
                    $count++;
                    dispatch(new AddMailCampaignToQueueJob($campaign, $contact));
                }
            });


            // add contact count to the campaign report
            $campaign->report = [
                'contact_count' => $count,
            ];

            // Update the campaign status to 'processing'

            $campaign->status = 'processing';
            $campaign->save();
        }
    }
   
}
