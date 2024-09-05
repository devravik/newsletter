<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'key',
        'value',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
