<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\AssessmentEvaluationController;
use App\Http\Controllers\Api\ChildConsentController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\SkillCategoryController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/health', fn() => response()->json(['status' => 'healthy']));
Route::get('/docs', function () {
    return response()->json([
        'message' => 'API Documentation',
        'endpoints' => [
            'POST /login' => 'User authentication',
            'POST /logout' => 'User logout (requires authentication)',
            'GET /user' => 'Get current user (requires authentication)',
        ]
    ]);
});

// Protected routes with Passport
Route::middleware('auth:api')->group(function () {
    // User routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Child routes
    Route::apiResource('children', ChildController::class);
    Route::get('/children/search', [ChildController::class, 'search']);
    Route::get('/children/{child}/assessments/count', [ChildController::class, 'assessmentCount']);
    Route::get('/children/{child}/consent', [ChildConsentController::class, 'generateConsent']);

    // Assessment routes
    Route::apiResource('children.assessments', AssessmentController::class)->except(['update']);
    Route::get('/children/{child}/assessments/latest', [AssessmentController::class, 'latest']);
    Route::apiResource('assessments.evaluations', AssessmentEvaluationController::class)
        ->only(['index', 'store']);

    /*------------------
    | Questions & Categories
    -------------------*/
    // Questions with category filtering
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    // Skill Categories with question counts
    Route::get('/skill-categories', [SkillCategoryController::class, 'index']);
    Route::get('/skill-categories/{category}', [SkillCategoryController::class, 'show']);

    // Additional question endpoints
    Route::get('/questions/categories', [QuestionController::class, 'categoriesWithQuestions']);
    Route::get('/questions/category/{category}/age/{age}', [QuestionController::class, 'byCategoryAndAge']);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/children/{child}', [ReportController::class, 'childReport']);
        Route::get('/children/{child}/timeline', [ReportController::class, 'childTimeline']);
        Route::get('/assessments/{assessment}/full', [ReportController::class, 'fullAssessment']);
        Route::get('/assessments/{assessment}/stats', [ReportController::class, 'assessmentStats']);
        Route::get('/assessments/{assessment}/pdf', [ReportController::class, 'generatePdfReport']);
        Route::get('/children/{child}/progress', [ReportController::class, 'progressReport']);
    });

    // Evaluation routes
    Route::get('evaluations/{evaluation}', [AssessmentEvaluationController::class, 'show']);
    Route::get('evaluations/{evaluation}/pdf', [AssessmentEvaluationController::class, 'generateEvaluation']);

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [ReportController::class, 'dashboardStats']);
        Route::get('/recent-assessments', [ReportController::class, 'recentAssessments']);
        Route::get('/age-distribution', [ReportController::class, 'ageDistribution']);
    });
});

// Public assessment results
Route::get('/assessments/{assessment}/results', [AssessmentController::class, 'results']);
