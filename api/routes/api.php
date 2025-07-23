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
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*------------------
| Authentication
-------------------*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*------------------
| Child Management
-------------------*/
Route::middleware('auth:sanctum')->group(function () {
    // Basic CRUD
    Route::apiResource('children', ChildController::class);
    
    // Additional child endpoints
    Route::get('/children/search', [ChildController::class, 'search']);
    Route::get('/children/{child}/assessments/count', [ChildController::class, 'assessmentCount']);
    
    /*------------------
    | Assessments
    -------------------*/
    Route::apiResource('children.assessments', AssessmentController::class)->except(['update']);
    
    // Get latest assessment
    Route::get('/children/{child}/assessments/latest', [AssessmentController::class, 'latest']);
    
    /*------------------
    | Questions & Categories
    -------------------*/
    Route::apiResource('questions', QuestionController::class);
    Route::get('/questions/categories', [QuestionController::class, 'categoriesWithQuestions']);
    Route::get('/questions/category/{category}/age/{age}', [QuestionController::class, 'byCategoryAndAge']);
    
    // Skill Categories
    Route::apiResource('skill-categories', SkillCategoryController::class);
    
    /*------------------
    | Reports
    -------------------*/
    Route::prefix('reports')->group(function () {
        // Child reports
        Route::get('/children/{child}', [ReportController::class, 'childReport']);
        Route::get('/children/{child}/timeline', [ReportController::class, 'childTimeline']);
        
        // Assessment reports
        Route::get('/assessments/{assessment}/full', [ReportController::class, 'fullAssessment']);
        Route::get('/assessments/{assessment}/stats', [ReportController::class, 'assessmentStats']);
        Route::get('/assessments/{assessment}/pdf', [ReportController::class, 'generatePdfReport']);
        
        // Progress reports
        Route::get('/children/{child}/progress', [ReportController::class, 'progressReport']);
    });
    
    /*------------------
    | Dashboard & Analytics
    -------------------*/
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [ReportController::class, 'dashboardStats']);
        Route::get('/recent-assessments', [ReportController::class, 'recentAssessments']);
        Route::get('/age-distribution', [ReportController::class, 'ageDistribution']);
    });
});

/*------------------
| Public Endpoints
-------------------*/
// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});

// API documentation
Route::get('/docs', function () {
    return response()->json([
        'message' => 'API documentation endpoint',
        'routes' => [
            'auth' => [
                'POST /api/login',
                'POST /api/logout',
                'GET /api/user'
            ],
            'children' => [
                'GET /api/children',
                'POST /api/children',
                'GET /api/children/{id}',
                'PUT /api/children/{id}',
                'DELETE /api/children/{id}',
                'GET /api/children/search'
            ]
            ]
    ]);
});