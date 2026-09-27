<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMedia extends Model
{
    protected $fillable = [
        'event_id',
        'uploader_type',
        'uploader_id',
        'media_url',
        'media_type',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
