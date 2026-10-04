<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoterLink extends Model
{
    protected $fillable = ['user_id', 'event_id', 'unique_code', 'clicks', 'sales_count', 'earnings'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
