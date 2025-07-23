<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Assessment;
use App\Models\Child;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Child $child)
    {
        $assessments = $child->assessments()
            ->with(['responses.question.category'])
            ->latest()
            ->get();

        return response()->json($assessments);
    }

    public function store(Request $request, Child $child)
    {
        $validated = $request->validate([
            'assessment_date' => 'required|date',
            'notes' => 'nullable|string',
            'responses' => 'required|array',
            'responses.*.question_id' => 'required|exists:questions,id',
            'responses.*.response' => 'required|in:can,cannot',
            'selected_ages' => 'required|array',
            'selected_ages.*' => 'integer|min:1|max:12',
        ]);

        $assessment = $child->assessments()->create([
            'assessment_date' => $validated['assessment_date'],
            'notes' => $validated['notes'],
            'selected_ages' => $validated['selected_ages'],
        ]);

        foreach ($validated['responses'] as $response) {
            $assessment->responses()->create([
                'question_id' => $response['question_id'],
                'response' => $response['response'],
            ]);
        }

        return response()->json($assessment->load('responses.question.category'), 201);
    }

    public function show(Assessment $assessment)
    {
        return response()->json($assessment->load([
            'child',
            'responses.question.category'
        ]));
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        return response()->json(null, 204);
    }
}