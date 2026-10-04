<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBooking extends Model
{
    protected $fillable = [
        'event_id',
        'vendor_profile_id',
        'status',
        'agreed_price',
        'message',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor()
    {
        return $this->belongsTo(VendorProfile::class, 'vendor_profile_id');
    }
}
