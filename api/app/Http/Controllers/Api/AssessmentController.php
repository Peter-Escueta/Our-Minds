<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Assessment;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments for a child
     */
    public function index(Child $child)
    {
        try {
            $assessments = $child->assessments()
                ->with(['responses.question.category'])
                ->latest()
                ->get();

            return response()->json([
                'data' => $assessments,
                'message' => 'Assessments retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('AssessmentController@index error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve assessments',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created assessment
     */
public function store(Request $request, Child $child)
{
    try {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'responses' => 'required|array|min:1',
            'responses.*.question_id' => [
                'required',
                Rule::exists('questions', 'id')->where(function ($query) use ($request) {
                    $query->whereIn('age', $request->selected_ages ?? []);
                })
            ],
            'responses.*.response' => 'required|in:can,cannot,emerging,not_observed', 
            'selected_ages' => 'required|array|min:1',
            'selected_ages.*' => 'integer|min:1|max:12',
        ]);

        $assessment = DB::transaction(function () use ($child, $validated) {
            $assessment = $child->assessments()->create([
                'assessment_date' => now(), // Set server-side timestamp
                'notes' => $validated['notes'] ?? null,
                'selected_ages' => $validated['selected_ages'],
            ]);

            $assessment->responses()->createMany($validated['responses']);

            return $assessment->load('responses.question.category');
        });

        return response()->json([
            'data' => $assessment,
            'message' => 'Assessment created successfully'
        ], 201);

    } catch (\Exception $e) {
        Log::error('AssessmentController@store error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to create assessment',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

    /**
     * Display the specified assessment
     */
    public function show(Assessment $assessment)
    {
        try {
            return response()->json([
                'data' => $assessment->load([
                    'child',
                    'responses.question.category'
                ]),
                'message' => 'Assessment retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('AssessmentController@show error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve assessment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified assessment
     */
    public function destroy(Assessment $assessment)
    {
        try {
            DB::transaction(function () use ($assessment) {
                $assessment->responses()->delete();
                $assessment->delete();
            });

            return response()->json([
                'message' => 'Assessment deleted successfully'
            ], 204);

        } catch (\Exception $e) {
            Log::error('AssessmentController@destroy error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete assessment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get the latest assessment for a child
     */
    public function latest(Child $child)
    {
        try {
            $assessment = $child->assessments()
                ->with(['responses.question.category'])
                ->latest()
                ->first();

            return response()->json([
                'data' => $assessment,
                'message' => $assessment ? 'Latest assessment retrieved' : 'No assessments found'
            ]);

        } catch (\Exception $e) {
            Log::error('AssessmentController@latest error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve latest assessment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}