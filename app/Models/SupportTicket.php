<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = ['event_id', 'guest_id', 'subject', 'status'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }
}
