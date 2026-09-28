<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'venue_name',
        'latitude',
        'longitude',
        'event_date',
        'theme_color',
        'payment_status',
        'tracking_access_token',
        'manual_directions',
        'stream_status',
        'mux_stream_id',
        'mux_playback_id',
        'simulcast_targets',
        'banner_image',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'simulcast_targets' => 'array',
    ];

    /**
     * Get the user that owns the event.
     */
    public function host()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the guests for the event.
     */
    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function eventMedia()
    {
        return $this->hasMany(EventMedia::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promoCodes()
    {
        return $this->hasMany(PromoCode::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }
}
