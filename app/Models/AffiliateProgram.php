<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateProgram extends Model
{
    protected $fillable = ['event_id', 'commission_percentage', 'is_active'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
