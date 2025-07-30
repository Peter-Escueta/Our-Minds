<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Assessment;
use App\Models\AssessmentEvaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentEvaluationController extends Controller
{
    /**
     * Store a newly created evaluation
     */
    public function store(Request $request, Assessment $assessment)
    {
        try {
            $validated = $request->validate([
                'background_information' => 'required|string',
                'recommendations' => 'required|array|min:1',
                'recommendations.*' => 'string',
                'summary_notes' => 'required|string',
            ]);

            $evaluation = DB::transaction(function () use ($assessment, $validated) {
                return $assessment->evaluations()->create($validated);
            });

            return response()->json([
                'data' => $evaluation,
                'message' => 'Evaluation created successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error('AssessmentEvaluationController@store error: ' . $e->getMessage());
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
            Log::error('AssessmentEvaluationController@index error: ' . $e->getMessage());
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
            // Load all necessary relationships
            $evaluation->load([
                'assessment.child',
                'assessment.responses.question.category'
            ]);

            // Format categories with responses
            $categories = [];
            foreach ($evaluation->assessment->responses as $response) {
                $categoryId = $response->question->skill_category_id;
                
                if (!isset($categories[$categoryId])) {
                    $categories[$categoryId] = [
                        'name' => $response->question->category->name,
                        'age' => $response->question->age,
                        'responses' => [],
                    ];
                }

                $responseText = $response->response === 'can' 
                    ? "can {$response->question->text}" 
                    : "has difficulty {$response->question->text}";
                
                $categories[$categoryId]['responses'][] = $responseText;
            }

            // Calculate competency for each category
            foreach ($categories as &$category) {
                $total = count($category['responses']);
                $positive = count(array_filter($category['responses'], fn($r) => str_starts_with($r, 'can')));
                $percentage = ($positive / $total) * 100;
                
                $category['competency'] = $percentage >= 50
                    ? "within the range of expected competency for age {$category['age']}"
                    : "below the expected range for age {$category['age']}";
            }

            return response()->json([
                'data' => [
                    'id' => $evaluation->id,
                    'created_at' => $evaluation->created_at,
                    'background_information' => $evaluation->background_information,
                    'recommendations' => $evaluation->recommendations,
                    'summary_notes' => $evaluation->summary_notes,
                    'assessment' => [
                        'child_name' => $evaluation->assessment->child->first_name,
                        'assessment_date' => $evaluation->assessment->assessment_date,
                        'categories' => array_values($categories)
                    ]
                ],
                'message' => 'Evaluation retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('AssessmentEvaluationController@show error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve evaluation',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
public function generateEvaluation(AssessmentEvaluation $evaluation)
{
    try {
        // Load all necessary relationships
        $evaluation->load([
            'assessment.child',
            'assessment.responses.question.category'
        ]);

        $assessment = $evaluation->assessment;

        // Format categories with responses
        $formattedCategories = [];
        foreach ($assessment->responses as $response) {
            $categoryId = $response->question->skill_category_id;
            
            if (!isset($formattedCategories[$categoryId])) {
                $formattedCategories[$categoryId] = [
                    'name' => $response->question->category->name,
                    'age' => $response->question->age,
                    'responses' => [],
                ];
            }

            $responseText = $response->response === 'can' 
                ? "can {$response->question->text}" 
                : "has difficulty {$response->question->text}";
            
            $formattedCategories[$categoryId]['responses'][] = $responseText;
        }

        // Calculate competency for each category
        foreach ($formattedCategories as &$category) {
            $total = count($category['responses']);
            $positive = count(array_filter($category['responses'], fn($r) => str_starts_with($r, 'can')));
            $percentage = ($positive / $total) * 100;
            
            $category['competency'] = $percentage >= 50
                ? "within the range of expected competency for age {$category['age']}"
                : "below the expected range for age {$category['age']}";
        }

        // Prepare data for PDF
        $data = [
            'evaluation' => $evaluation,
            'child' => $assessment->child, // Direct child object
            'assessment_date' => $assessment->assessment_date,
            'categories' => array_values($formattedCategories),
            'date' => now()->format('F j, Y'),
            'centerName' => 'OUR MINDS Intervention & Therapy Center',
            'centerLocation' => 'San Pedro Laguna',
            'centerContact' => '(09912583429)',
            'specialist' => 'Raymond E. Mindanao MA LPT RPm',
            'specialistTitle' => 'Licensed Psychometrician',
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
        Log::error("Evaluation PDF Generation Error: " . $e->getMessage());
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