<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'event_id',
        'code',
        'discount_type',
        'discount_amount',
        'usage_limit',
        'times_used',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'discount_amount' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function isValid()
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }
}
