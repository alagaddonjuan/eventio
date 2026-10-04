<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    public function vendorProfiles()
    {
        return $this->hasMany(VendorProfile::class);
    }
}
