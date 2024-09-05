<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::orderBy('email', 'asc');
        if($request->has('search')){
            $contacts->where('email', 'like', '%'.$request->search.'%')
                    ->orWhere('name', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%')
                    ->orWhere('company', 'like', '%'.$request->search.'%')
                    ->orWhere('job_title', 'like', '%'.$request->search.'%')
                    ->orWhere('city', 'like', '%'.$request->search.'%')
                    ->orWhere('country', 'like', '%'.$request->search.'%')
                    ->orWhere('status', 'like', '%'.$request->search.'%')
                    ->orWhere('notes', 'like', '%'.$request->search.'%')
                    ->orWhere('website', 'like', '%'.$request->search.'%')
                    ->orWhere('postal_code', 'like', '%'.$request->search.'%')
                    ->orWhere('address', 'like', '%'.$request->search.'%')
                    ->orWhere('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%')
                    ->orWhereHas('metas', function($query) use ($request){
                        $query->where('value', 'like', '%'.$request->search.'%');
                    });
        }
        if($request->has('sort')){
            $contacts->orderBy($request->sort, $request->order);
        }
        if($request->has('status')){
            $contacts->where('status', $request->status);
        }
        if($request->has('source')){
            $contacts->where('source', $request->source);
        }
        $per_page = $request->per_page ?? 25;
        $contacts = $contacts->paginate($per_page)->withQueryString();
        return view('contacts.index', ['contacts' => $contacts]);
    }

    public function show(Contact $contact)
    {
        return view('contacts.show', ['contact' => $contact]);
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', ['contact' => $contact]);
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        // Validate the request...
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $contact = new Contact();

        $contact->name = $request->name ?? null;
        $contact->first_name = $request->first_name ?? null;
        $contact->last_name = $request->last_name ?? null;
        $contact->email = $request->email ?? null;
        $contact->phone = $request->phone ?? null;
        $contact->country = $request->country ?? null;
        $contact->city = $request->city ?? null;
        $contact->job_title = $request->job_title ?? null;
        $contact->company = $request->company ?? null;
        $contact->address = $request->address ?? null;
        $contact->postal_code = $request->postal_code ?? null;
        $contact->website = $request->website ?? null;
        $contact->notes = $request->notes ?? null;
        $contact->source = $request->source ?? null;
        $contact->status = 1;
        
        $contact->save();

        return redirect()->route('contacts');

    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        // Validate dynamically based on fillable fields
        $rules = array_fill_keys($contact->getFillable(), 'nullable|string|max:255');
        $rules['email'] = 'required|email|max:255'; // Specific validation rules
        $request->validate($rules);

        // Update the contact with fillable fields
        $contact->update($request->only($contact->getFillable()));

        // Handle meta fields
        $metaIds = $request->input('meta_ids', []);
        $metaKeys = $request->input('meta_keys', []);
        $metaValues = $request->input('meta_values', []);

        // Update existing meta fields
        foreach ($metaIds as $index => $id) {
            if ($id) {
                $contact->metas()->where('id', $id)->update([
                    'key' => $metaKeys[$index],
                    'value' => $metaValues[$index],
                ]);
            }
        }

        // Add new meta fields
        foreach ($metaKeys as $index => $key) {
            if (!$metaIds[$index]) { // New meta field
                $contact->metas()->create([
                    'key' => $key,
                    'value' => $metaValues[$index],
                ]);
            }
        }

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }


    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index');
    }
        

}
