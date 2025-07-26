<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SkillCategoryController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Child routes (unchanged)
    Route::apiResource('children', ChildController::class);
    Route::get('/children/search', [ChildController::class, 'search']);
    Route::get('/children/{child}/assessments/count', [ChildController::class, 'assessmentCount']);
    
    // Assessment routes (unchanged)
    Route::apiResource('children.assessments', AssessmentController::class)->except(['update']);
    Route::get('/children/{child}/assessments/latest', [AssessmentController::class, 'latest']);
    
    /*------------------
    | Questions & Categories - UPDATED SECTION
    -------------------*/
    // Questions with category filtering
    Route::get('/questions', [QuestionController::class, 'index']); // Add ?category_id=X param support
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);
    
    // Skill Categories with question counts
    Route::get('/skill-categories', [SkillCategoryController::class, 'index']); // Should include questions_count
    Route::get('/skill-categories/{category}', [SkillCategoryController::class, 'show']);
    
    // Additional question endpoints
    Route::get('/questions/categories', [QuestionController::class, 'categoriesWithQuestions']);
    Route::get('/questions/category/{category}/age/{age}', [QuestionController::class, 'byCategoryAndAge']);
    
    // Reports (unchanged)
    Route::prefix('reports')->group(function () {
        Route::get('/children/{child}', [ReportController::class, 'childReport']);
        Route::get('/children/{child}/timeline', [ReportController::class, 'childTimeline']);
        Route::get('/assessments/{assessment}/full', [ReportController::class, 'fullAssessment']);
        Route::get('/assessments/{assessment}/stats', [ReportController::class, 'assessmentStats']);
        Route::get('/assessments/{assessment}/pdf', [ReportController::class, 'generatePdfReport']);
        Route::get('/children/{child}/progress', [ReportController::class, 'progressReport']);
    });
    
    // Dashboard (unchanged)
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [ReportController::class, 'dashboardStats']);
        Route::get('/recent-assessments', [ReportController::class, 'recentAssessments']);
        Route::get('/age-distribution', [ReportController::class, 'ageDistribution']);
    });
});

// Public endpoints (unchanged)
Route::get('/health', fn () => response()->json(['status' => 'healthy']));
Route::get('/docs', function () { /* ... */ });