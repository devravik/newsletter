<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CampaignController extends Controller
{
    public function index(Request $request){
        $campaigns = Campaign::latest();
        if($request->has('search')){
            $campaigns->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('subject', 'like', '%'.$request->search.'%')
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
            $campaigns->orderBy($request->sort, $request->order);
        }
        if($request->has('status')){
            $campaigns->where('status', $request->status);
        }
        $per_page = $request->per_page ?? 25;
        $campaigns = $campaigns->paginate($per_page)->withQueryString();
        return view('campaigns.index', ['campaigns' => $campaigns]);
    }

    public function show(Campaign $campaign){
        return view('campaigns.show', ['campaign' => $campaign]);
    }

    public function edit(Campaign $campaign){
        return view('campaigns.edit', ['campaign' => $campaign]);
    }

    public function create(){
        

        $templatesPath = resource_path('views/emails');
        $templates = File::allFiles($templatesPath);

        $templates = array_map(function($file){
            //get the filename without the extension            
            $filename = pathinfo($file, PATHINFO_FILENAME);
            return str_replace('.blade','',$filename);
        }, $templates);

        return view('campaigns.create', ['templates' => $templates]);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'from_name' => 'required|string|max:255',
            'from_email' => 'required|email',
            'reply_to' => 'nullable|email',
            'sent_at' => 'nullable|date',
            'template' => 'nullable|string',
            'content' => 'nullable|string',
            'contact_filters' => 'nullable|array',
            'meta' => 'nullable|array',
            'options' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        // Process each array field to save key-value pairs properly
        $validatedData['contact_filters'] = $this->prepareKeyValueArray($request->input('contact_filters', []));
        $validatedData['meta'] = $this->prepareKeyValueArray($request->input('meta', []));
        $validatedData['options'] = $this->prepareKeyValueArray($request->input('options', []));
        $validatedData['report'] = $this->prepareKeyValueArray($request->input('report', []));
        $validatedData['settings'] = $this->prepareKeyValueArray($request->input('settings', []));


        $campaign = Campaign::create($validatedData);
        return redirect()->route('campaigns.show', $campaign);
    }

    protected function prepareKeyValueArray($input)
    {
        $output = [];
        foreach ($input as $item) {
            if (!empty($item['key']) && !empty($item['value'])) {
                $output[$item['key']] = $item['value'];
            }
        }
        return $output;
    }

    public function update(Request $request, Campaign $campaign){
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
            'from_name' => 'required',
            'from_email' => 'required',
            'reply_to' => 'required',
            'template' => 'required',
        ]);
        $campaign->update($request->all());
        return redirect()->route('campaigns.show', $campaign);
    }

    public function destroy(Campaign $campaign){
        $campaign->delete();
        return redirect()->route('campaigns.index');
    }
}
