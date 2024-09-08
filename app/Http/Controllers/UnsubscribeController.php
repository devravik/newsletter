<?php

namespace App\Http\Controllers;

use App\Models\CampaignMail;
use App\Models\Unsubscribe;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    public function unsubscribe(Request $request, CampaignMail $campaignMail)
    {
        $email = $campaignMail->email;
        
        $campaignMail->update([
            'unsubscribed_at' => now()
        ]);

        $unsubscribe = Unsubscribe::updateOrCreate([
            'email' => $email
        ]);
        return view('unsubscribe', compact('email'));
    }

    function bounce(Request $request) {
        // $email = $request->email;
        // $unsubscribe = Unsubscribe::updateOrCreate([
        //     'email' => $email
        // ]);
        logger($request->all());

        /**
         * [2024-09-08 05:54:40] local.DEBUG: array (
        'Message-Id' => '<1c6224ff993bbebf01bbd5b23a6e55c1@happendesk.com>',
        'Subject' => 'Get your first month for $0 and earn up to $10,000 in credits',
        'auth' => 'happendesk.com',
        'bounce' => 'hard',
        'context' => 'RCPT TO:<rhonda@designsplus.ca>',
        'email_id' => '1snAsM-FnQW0hPl4SU-LMQG',
        'event' => 'bounce',
        'from' => 'Shopify <noreply@happendesk.com>',
        'from_address' => 'noreply@happendesk.com',
        'from_name' => 'Shopify',
        'host' => 'mail.designsplus.ca [142.4.196.175]',
        'id' => '35aa6baf370796f66af1088b96fde79e',
        'message' => '525 5.7.13 Disabled recipient address',
        'message-id' => '<1c6224ff993bbebf01bbd5b23a6e55c1@happendesk.com>',
        'rcpt' => 'rhonda@designsplus.ca',
        'sender' => 'noreply@happendesk.com',
        'sendtime' => '2024-09-08T05:54:21.252481+00:00',
        'subject' => 'Get your first month for $0 and earn up to $10,000 in credits',
        'time' => '2024-09-08T05:54:35Z',
)

         */
        if($request->event == 'bounce') {
            $email = $request->rcpt;
            $unsubscribe = Unsubscribe::updateOrCreate([
                'email' => $email
            ]);

            // get recent campaign mail
            $campaignMail = CampaignMail::where('email', $email)->latest()->first();
            if($campaignMail) {
                $campaignMail->update([
                    'unsubscribed_at' => now(),
                    'is_bounced' => true
                ]);
            }
            logger('Unsubscribed email: ' . $email);
        }
        return response()->json(['message' => 'Email added to unsubscribe list']);
        
    }
}
