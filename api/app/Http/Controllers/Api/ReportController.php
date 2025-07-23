<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Assessment;
use App\Models\Child;
use Illuminate\Http\Request;
use PDF;

class ReportController extends Controller
{
    public function childReport(Child $child)
    {
        $child->load(['assessments' => function($query) {
            $query->latest()->with('responses.question.category');
        }]);

        return response()->json($child);
    }

    public function generatePdfReport(Child $child, Assessment $assessment)
    {
        $assessment->load(['child', 'responses.question.category']);
        
        $pdf = PDF::loadView('reports.assessment', compact('assessment'));
        
        return $pdf->download("assessment-{$assessment->id}.pdf");
    }

    public function assessmentStats(Assessment $assessment)
    {
        $assessment->load(['responses.question.category']);
        
        $stats = [];
        $categories = [];
        
        foreach ($assessment->responses as $response) {
            $categoryId = $response->question->category->id;
            $categoryName = $response->question->category->name;
            
            if (!isset($stats[$categoryId])) {
                $stats[$categoryId] = [
                    'category' => $categoryName,
                    'total' => 0,
                    'can' => 0,
                    'cannot' => 0
                ];
                $categories[$categoryId] = $categoryName;
            }
            
            $stats[$categoryId]['total']++;
            $stats[$categoryId][$response->response]++;
        }
        
        return response()->json([
            'stats' => array_values($stats),
            'categories' => array_values($categories)
        ]);
    }
}