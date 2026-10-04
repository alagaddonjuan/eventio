<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_category_id',
        'business_name',
        'description',
        'location',
        'base_price',
        'cover_image',
        'is_verified',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'vendor_category_id');
    }

    public function bookings()
    {
        return $this->hasMany(VendorBooking::class);
    }
}
