<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Assessment;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'responses.*.question_id' => 'required|exists:questions,id',
                'responses.*.age' => 'required|integer|min:1|max:12',
                'responses.*.response' => 'required|in:can,cannot,emerging,not_observed',
                'selected_ages' => 'required|array|min:1',
                'selected_ages.*' => 'array',
                'selected_ages.*.*' => 'integer|min:1|max:12',
            ]);



            $assessment = DB::transaction(function () use ($child, $validated) {
                $assessment = $child->assessments()->create([
                    'assessment_date' => now(),
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
            return response()->json([
                'message' => 'Failed to delete assessment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function results(Assessment $assessment)
    {
        try {
            $assessment->load([
                'responses.question.category',
                'child'
            ]);

            $results = [];
            $assessedAges = [];

            foreach ($assessment->responses as $response) {
                $categoryId = $response->question->skill_category_id;
                $categoryName = $response->question->category->name;
                $questionAge = $response->question->age;

                if (!in_array($questionAge, $assessedAges)) {
                    $assessedAges[] = $questionAge;
                }

                if (!isset($results[$categoryId])) {
                    $results[$categoryId] = [
                        'name' => $categoryName,
                        'ages' => []
                    ];
                }

                if (!isset($results[$categoryId]['ages'][$questionAge])) {
                    $results[$categoryId]['ages'][$questionAge] = [
                        'age' => $questionAge,
                        'responses' => [],
                        'total_questions' => 0,
                        'can_count' => 0
                    ];
                }

                $responseText = $response->response === 'can'
                    ? "can {$response->question->text}"
                    : "has difficulty {$response->question->text}";

                $results[$categoryId]['ages'][$questionAge]['responses'][] = $responseText;
                $results[$categoryId]['ages'][$questionAge]['total_questions']++;

                if ($response->response === 'can') {
                    $results[$categoryId]['ages'][$questionAge]['can_count']++;
                }
            }

            $formattedResults = [];
            foreach ($results as $categoryId => $categoryData) {
                foreach ($categoryData['ages'] as $ageData) {
                    $percentage = ($ageData['can_count'] / $ageData['total_questions']) * 100;

                    $formattedResults[] = [
                        'name' => $categoryData['name'],
                        'age' => $ageData['age'],
                        'responses' => $ageData['responses'],
                        'competency' => $percentage >= 50
                            ? "within the range of expected competency for age {$ageData['age']}"
                            : "below the expected range for age {$ageData['age']}"
                    ];
                }
            }


            usort($formattedResults, function ($a, $b) {
                if ($a['name'] === $b['name']) {
                    return $a['age'] <=> $b['age'];
                }
                return $a['name'] <=> $b['name'];
            });

            return response()->json([
                'data' => [
                    'id' => $assessment->id,
                    'child_name' => $assessment->child->first_name,
                    'assessment_date' => $assessment->assessment_date,
                    'assessed_ages' => $assessedAges,
                    'categories' => $formattedResults
                ],
                'message' => 'Assessment results retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve assessment results',
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
            return response()->json([
                'message' => 'Failed to retrieve latest assessment',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
