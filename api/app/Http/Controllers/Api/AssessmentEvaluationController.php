<?php

namespace App\Http\Controllers\Api;

use App\Enums\EvaluationStatus;
use App\Http\Controllers\Api\Controller;
use App\Models\Assessment;
use App\Models\AssessmentEvaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AssessmentEvaluationController extends Controller
{
    /**
     * Store a newly created evaluation
     */
    public function storeBackground(Request $request, Assessment $assessment)
    {
        try {
            $validated = $request->validate([
                'background_information' => 'required|string',
            ]);


            $evaluation = DB::transaction(function () use ($assessment, $validated) {
                return $assessment->evaluations()->create($validated);
            });

            return response()->json([
                'data' => $evaluation,
                'message' => 'Background information saved successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save background information',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function storeEvaluation(Request $request, Assessment $assessment)
    {
        try {
            $request->merge([
                'status' => EvaluationStatus::COMPLETED->value
            ]);
            $validated = $request->validate([
                'background_information' => 'nullable|string',
                'recommendations' => 'required|array|min:1',
                'recommendations.*' => 'string',
                'websites' => 'required|array|min:1',
                'websites.*' => 'string',
                'status' => [Rule::enum(EvaluationStatus::class)],
            ]);

            $evaluation = DB::transaction(function () use ($assessment, $validated) {
                $existing = $assessment->evaluations()->latest()->first();

                if ($existing) {

                    $existing->update($validated);
                    return $existing;
                }

                return $assessment->evaluations()->create($validated);
            });

            return response()->json([
                'data' => $evaluation,
                'message' => 'Evaluation saved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create evaluation',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    /**
     * Display evaluations for an assessment
     */
    public function index(Assessment $assessment)
    {
        try {
            $evaluations = $assessment->evaluations()
                ->latest()
                ->get();

            return response()->json([
                'data' => $evaluations,
                'message' => 'Evaluations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve evaluations',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display a specific evaluation with full details
     */
    public function show(AssessmentEvaluation $evaluation)
    {
        try {
            $evaluation->load([
                'assessment.child',
                'assessment.responses.question.category'
            ]);

            $categories = [];
            $assessedAges = [];

            foreach ($evaluation->assessment->responses as $response) {
                $categoryId = $response->question->skill_category_id;
                $categoryName = $response->question->category->name;
                $questionAge = $response->question->age;

                if (!in_array($questionAge, $assessedAges)) {
                    $assessedAges[] = $questionAge;
                }

                $categoryAgeKey = $categoryId . '-' . $questionAge;

                if (!isset($categories[$categoryAgeKey])) {
                    $categories[$categoryAgeKey] = [
                        'name' => $categoryName,
                        'age' => $questionAge,
                        'responses' => [],
                        'total_questions' => 0,
                        'can_count' => 0
                    ];
                }

                $responseText = $response->response === 'can'
                    ? "can {$response->question->text}"
                    : "has difficulty {$response->question->text}";

                $categories[$categoryAgeKey]['responses'][] = $responseText;
                $categories[$categoryAgeKey]['total_questions']++;

                if ($response->response === 'can') {
                    $categories[$categoryAgeKey]['can_count']++;
                }
            }

            $formattedCategories = [];
            foreach ($categories as $categoryData) {
                $percentage = ($categoryData['can_count'] / $categoryData['total_questions']) * 100;

                $formattedCategories[] = [
                    'name' => $categoryData['name'],
                    'age' => $categoryData['age'],
                    'responses' => $categoryData['responses'],
                    'competency' => $percentage >= 50
                        ? "within the range of expected competency for age {$categoryData['age']}"
                        : "below the expected range for age {$categoryData['age']}"
                ];
            }

            usort($formattedCategories, function ($a, $b) {
                if ($a['name'] === $b['name']) {
                    return $a['age'] <=> $b['age'];
                }
                return $a['name'] <=> $b['name'];
            });

            return response()->json([
                'data' => [
                    'id' => $evaluation->id,
                    'created_at' => $evaluation->created_at,
                    'background_information' => $evaluation->background_information,
                    'recommendations' => $evaluation->recommendations,
                    'websites' => $evaluation->websites,
                    'assessment' => [
                        'child_name' => $evaluation->assessment->child->first_name,
                        'assessment_date' => $evaluation->assessment->assessment_date,
                        'assessed_ages' => $assessedAges,
                        'categories' => $formattedCategories
                    ]
                ],
                'message' => 'Evaluation retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve evaluation',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function generateEvaluation(AssessmentEvaluation $evaluation)
    {
        try {
            $centerName = env('CENTER_NAME', 'REDACTED');
            $centerLocation = env('CENTER_LOCATION', 'REDACTED');
            $centerContact = env('CENTER_CONTACT', 'REDACTED');
            $specialist =  env('SPECIALIST', 'REDACTED');
            $specialistTitle = env('SPECIALIST_TITLE', 'REDACTED');

            $evaluation->load([
                'assessment.child',
                'assessment.responses.question.category'
            ]);

            $assessment = $evaluation->assessment;

            $formattedCategories = [];
            $assessedAges = [];

            foreach ($assessment->responses as $response) {
                $categoryId = $response->question->skill_category_id;
                $categoryName = $response->question->category->name;
                $questionAge = $response->question->age;

                if (!in_array($questionAge, $assessedAges)) {
                    $assessedAges[] = $questionAge;
                }

                $categoryAgeKey = $categoryId . '-' . $questionAge;

                if (!isset($formattedCategories[$categoryAgeKey])) {
                    $formattedCategories[$categoryAgeKey] = [
                        'name' => $categoryName,
                        'age' => $questionAge,
                        'responses' => [],
                        'total_questions' => 0,
                        'can_count' => 0
                    ];
                }

                $responseText = $response->response === 'can'
                    ? "can {$response->question->text}"
                    : "has difficulty {$response->question->text}";

                $formattedCategories[$categoryAgeKey]['responses'][] = $responseText;
                $formattedCategories[$categoryAgeKey]['total_questions']++;

                if ($response->response === 'can') {
                    $formattedCategories[$categoryAgeKey]['can_count']++;
                }
            }

            $finalCategories = [];
            foreach ($formattedCategories as $categoryData) {
                $percentage = ($categoryData['can_count'] / $categoryData['total_questions']) * 100;

                $finalCategories[] = [
                    'name' => $categoryData['name'],
                    'age' => $categoryData['age'],
                    'responses' => $categoryData['responses'],
                    'competency' => $percentage >= 50
                        ? "within the range of expected competency for age {$categoryData['age']}"
                        : "below the expected range for age {$categoryData['age']}"
                ];
            }

            usort($finalCategories, function ($a, $b) {
                if ($a['name'] === $b['name']) {
                    return $a['age'] <=> $b['age'];
                }
                return $a['name'] <=> $b['name'];
            });

            $data = [
                'evaluation' => $evaluation,
                'child' => $assessment->child,
                'assessment_date' => $assessment->assessment_date,
                'categories' => $finalCategories,
                'assessed_ages' => $assessedAges,
                'date' => now()->format('F j, Y'),
                'centerName' => $centerName,
                'centerLocation' => $centerLocation,
                'centerContact' => $centerContact,
                'specialist' => $specialist,
                'specialistTitle' => $specialistTitle,
                'logo' => $this->getBase64Image(public_path('images/logo.png'))
            ];

            $pdf = Pdf::loadView('evaluation-form', $data)
                ->setPaper('A4', 'portrait')
                ->setOptions([
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'Calibri',
                    'chroot' => public_path()
                ]);

            $filename = "evaluation-{$assessment->child->first_name}-{$evaluation->created_at->format('Ymd')}.pdf";

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate evaluation PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function getBase64Image($path)
    {
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return null;
    }
}
