<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class CampaignMailQueueJob implements ShouldQueue
{
    use Queueable;

    public $campaignMail;

    /**
     * Create a new job instance.
     */
    public function __construct(CampaignMail $campaignMail)
    {
        $this->campaignMail = $campaignMail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaignMail = $this->campaignMail;
        // Send email to contact
        $contact = $campaignMail->contact;
        $campaign = $campaignMail->campaign;
        $email = $contact->email;
        $subject = $campaignMail->subject;
        $body = $campaignMail->body;
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

        // Update sent_at timestamp
        $campaignMail->sent_at = now();
        $campaignMail->save();
    }
}
