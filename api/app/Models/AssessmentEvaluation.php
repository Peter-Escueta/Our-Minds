<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'background_information',
        'recommendations',
        'websites',
        'status'
    ];

    protected $casts = [
        'recommendations' => 'array',
        'websites' => 'array',
        'status' => EvaluationStatus::class,
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
