<?php

namespace App\Http\Controllers;

use App\Models\CampaignMail;
use Illuminate\Http\Request;

class CampaignMailController extends Controller
{
    public function index(Request $request){
        $mails = CampaignMail::latest();
        if($request->has('search')){
            $mails->where('subject', 'like', '%'.$request->search.'%')
                    ->orWhere('from_name', 'like', '%'.$request->search.'%')
                    ->orWhere('from_email', 'like', '%'.$request->search.'%')
                    ->orWhere('reply_to', 'like', '%'.$request->search.'%')
                    ->orWhere('status', 'like', '%'.$request->search.'%')
                    ->orWhere('sent_at', 'like', '%'.$request->search.'%')
                    ->orWhere('template', 'like', '%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%')
                    ->orWhere('contact_filters', 'like', '%'.$request->search.'%')
                    ->orWhere('meta', 'like', '%'.$request->search.'%')
                    ->orWhere('options', 'like', '%'.$request->search.'%')
                    ->orWhere('report', 'like', '%'.$request->search.'%')
                    ->orWhere('settings', 'like', '%'.$request->search.'%');
        }
        if($request->has('sort')){
            $mails->orderBy($request->sort, $request->order);
        }
        if($request->has('status')){
            $mails->where('status', $request->status);
        }
        if($request->has('campaign_id')){
            $mails->where('campaign_id', $request->campaign_id);
        }
        if($request->has('contact_id')){
            $mails->where('contact_id', $request->contact_id);
        }        
        $per_page = $request->per_page ?? 25;
        $mails = $mails->paginate($per_page)->withQueryString();
        return view('campaign_mails.index', ['mails' => $mails]);
    }

    public function show(CampaignMail $mail){
        return view('campaign_mails.show', ['mail' => $mail]);
    }

    public function edit(CampaignMail $mail){
        return view('campaign_mails.edit', ['mail' => $mail]);
    }

    public function create(){
        return view('campaign_mails.create');
    }

    public function store(Request $request){
        $request->validate([
            'campaign_id' => 'required',
            'contact_id' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'from_name' => 'required',
            'from_email' => 'required',
            'reply_to' => 'required',
            'template' => 'required',
        ]);
        $mail = CampaignMail::create($request->all());
        return redirect()->route('campaign_mails.show', $mail);
    }

    public function update(Request $request, CampaignMail $mail){
        $request->validate([
            'campaign_id' => 'required',
            'contact_id' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'from_name' => 'required',
            'from_email' => 'required',
            'reply_to' => 'required',
            'template' => 'required',
        ]);
        $mail->update($request->all());
        return redirect()->route('campaign_mails.show', $mail);
    }

    public function destroy(CampaignMail $mail){
        $mail->delete();
        return redirect()->route('mails');
    }
}
