<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'from_name',
        'from_email',
        'reply_to',
        'status',
        'sent_at',
        'template',
        'content',
        'contact_filters',
        'meta',
        'options',
        'report',
        'settings',        
    ];

    protected $casts = [
        'contact_filters' => 'array',
        'meta' => 'array',
        'options' => 'array',
        'report' => 'array',
        'settings' => 'array',
        'sent_at' => 'datetime',
    ];

    public function mails()
    {
        return $this->hasMany(CampaignMail::class);
    }
    
}
