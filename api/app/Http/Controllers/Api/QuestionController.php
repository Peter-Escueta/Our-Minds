<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Question;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $categories = SkillCategory::with(['questions' => function($query) {
            $query->orderBy('age');
        }])->get();

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'skill_category_id' => 'required|exists:skill_categories,id',
            'text' => 'required|string',
            'age' => 'required|integer|min:1|max:12',
        ]);

        $question = Question::create($validated);

        return response()->json($question->load('category'), 201);
    }

    public function show(Question $question)
    {
        return response()->json($question->load('category'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'skill_category_id' => 'required|exists:skill_categories,id',
            'text' => 'required|string',
            'age' => 'required|integer|min:1|max:12',
        ]);

        $question->update($validated);

        return response()->json($question->load('category'));
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return response()->json(null, 204);
    }

    public function byCategoryAndAge($categoryId, $age)
    {
        $questions = Question::where('skill_category_id', $categoryId)
            ->where('age', $age)
            ->get();

        return response()->json($questions);
    }
}