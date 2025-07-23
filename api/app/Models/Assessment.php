<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = ['child_id', 'assessment_date', 'notes'];

    protected $casts = [
        'assessment_date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function responses()
    {
        return $this->hasMany(AssessmentResponse::class);
    }
}