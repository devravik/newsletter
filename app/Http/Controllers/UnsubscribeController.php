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
        return response()->json(['message' => 'Email added to unsubscribe list']);
        
    }
}
