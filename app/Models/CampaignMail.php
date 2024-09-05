<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignMail extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'email',
        'subject',
        'from_name',
        'from_email',
        'template',
        'reply_to',
        'status',
        'sent_at',
        'opened_at',
        'unsubscribed_at',
        'is_bounced',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }    
    
}
