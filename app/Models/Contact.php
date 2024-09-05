<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
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

    public function metas()
    {
        return $this->hasMany(ContactMeta::class);
    }    
}
