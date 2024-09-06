<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'country',
        'city',
        'job_title',
        'company',
        'address',
        'postal_code',
        'website',
        'notes',
        'source',
        'status',                
    ];

    function getFullNameAttribute() : string {
        return !empty($this->name) ? $this->name : implode(' ', array_filter([$this->title, $this->first_name, $this->middle_name, $this->last_name]));
    }

    public function metas()
    {
        return $this->hasMany(ContactMeta::class);
    }  
    
    public function campaignMails()
    {
        return $this->hasMany(CampaignMail::class);
    }
}
