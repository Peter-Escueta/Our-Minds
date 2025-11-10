<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname',
        'first_name',
        'middle_name',
        'educational_placement',
        'is_initial_assessment',
        'is_follow_up',
        'address',
        'email',
        'date_of_birth',
        'date_of_assessment',
        'age_at_consult',
        'gender',
        'siblings',
        'mother_name',
        'mother_occupation',
        'mother_contact',
        'father_name',
        'father_occupation',
        'father_contact',
        'medical_diagnosis',
        'referring_doctor',
        'last_assessment_date',
        'follow_up_date',
        'occupational_therapy',
        'physical_therapy',
        'behavioral_therapy',
        'speech_therapy',
        'school',
        'grade',
        'placement',
        'year',
        'reason'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_assessment' => 'date',
        'last_assessment_date' => 'date',
        'follow_up_date' => 'date',
        'is_initial_assessment' => 'boolean',
        'is_follow_up' => 'boolean',
        'occupational_therapy' => 'boolean',
        'physical_therapy' => 'boolean',
        'behavioral_therapy' => 'boolean',
        'speech_therapy' => 'boolean',
    ];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
