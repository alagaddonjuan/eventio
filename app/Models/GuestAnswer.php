<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',
        'custom_question_id',
        'answer_text',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function question()
    {
        return $this->belongsTo(CustomQuestion::class, 'custom_question_id');
    }
}
