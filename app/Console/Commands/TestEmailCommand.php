<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\CampaignMail;
use App\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email-command {template=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to a contact';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

        $template = $this->argument('template');

        $fromName = 'Happendesk';
        $fromEmail = 'noreply@happendesk.com';
        $replyTo = 'noreply@happendesk.com';

        // if ses enabled
        if(config('mail.default') == 'ses') {
            $fromName = 'Sellcodes';
            $fromEmail = 'noreply@sellcodes.com';
            $replyTo = 'noreply@sellcodes.com';
        }
        
        // Add a test campaign
        $campaign = Campaign::create([
            'name' => 'Test Campaign',
            'subject' => 'Test Subject',
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'reply_to' => $replyTo,
            'template' => $template,
            'status' => 'sent',
            'sent_at' => now(),
            'contact_filters' => [
                'email' => 'rk822827@gmail.com'
            ],
        ]);       
        
        // Artisan::call('app:process-campaign-command');

        $contact = Contact::where('email', 'rk822827@gmail.com')->first();

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
        $campaignMail->scheduled_at = now();
        $campaignMail->status = 'sent';
        $campaignMail->save();

        
        $contact = $campaignMail->contact;
        $email = $contact->email;
        $subject = $campaignMail->subject;
        $fromName = $campaignMail->from_name;
        $fromEmail = $campaignMail->from_email;
        $replyTo = $campaignMail->reply_to;

        $template = 'emails.' . $campaignMail->template;

        // Send email template
        Mail::send($template, ['contact' => $contact, 'campaign' => $campaign, 'campaignMail' => $campaignMail], function ($message) use ($email, $subject, $fromName, $fromEmail, $replyTo) {
            $message->to($email);
            $message->subject($subject);
            $message->from($fromEmail, $fromName);
            $message->replyTo($replyTo);
        });
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
