<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'background_information',
        'recommendations',
        'summary_notes'
    ];

    protected $casts = [
        'recommendations' => 'array',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}