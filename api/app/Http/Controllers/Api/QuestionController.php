<?php

namespace App\Http\Controllers\Api;

use App\Models\Question;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions with optional filters
     */
    public function index(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'sometimes|exists:skill_categories,id',
                'age' => 'sometimes|integer|min:1|max:12',
                'per_page' => 'sometimes|integer|min:1|max:100'
            ]);

            $query = Question::with('category')
                ->orderBy('age')
                ->orderBy('created_at');

            if ($request->has('category_id')) {
                $query->where('skill_category_id', $request->category_id);
            }

            if ($request->has('age')) {
                $query->where('age', $request->age);
            }

            return $request->has('per_page')
                ? $query->paginate($request->per_page)
                : $query->get();
        } catch (\Exception $e) {
            Log::error('QuestionController@index error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error retrieving questions',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'skill_category_id' => 'required|exists:skill_categories,id',
                'text' => 'required|string|max:500',
                'age' => 'required|integer|min:1|max:12'
            ]);

            $question = Question::create($validated);

            return response()->json([
                'message' => 'Question created successfully',
                'data' => $question->load('category')
            ], 201);
        } catch (\Exception $e) {
            Log::error('QuestionController@store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating question',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Question $question)
    {
        try {
            $validated = $request->validate([
                'text' => 'sometimes|string|max:500',
                'age' => 'sometimes|integer|min:1|max:12'
            ]);

            $question->update($validated);

            return response()->json([
                'message' => 'Question updated successfully',
                'data' => $question->load('category')
            ]);
        } catch (\Exception $e) {
            Log::error('QuestionController@update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating question',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified question
     */
    public function destroy(Question $question)
    {
        try {
            $question->delete();

            return response()->json([
                'message' => 'Question deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('QuestionController@destroy error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting question',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get questions grouped by category and age
     */
    public function byCategoryAndAge(SkillCategory $category, $age)
    {
        try {
            $questions = Question::where('skill_category_id', $category->id)
                ->where('age', $age)
                ->get();

            return response()->json($questions);
        } catch (\Exception $e) {
            Log::error('QuestionController@byCategoryAndAge error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error retrieving questions',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
