<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Therapy extends Model
{
    protected $fillable = [
        'type',
        'is_received',
        'therapy_center',
        'therapist_name',
        'therapist_contact_number',
        'therapist_email',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}
