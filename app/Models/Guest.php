<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_id',
        'name',
        'email',
        'rsvp_status',
        'unique_token',
        'check_in_status',
        'barcode_data'
    ];

    /**
     * Get the event that owns the guest.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }
}
