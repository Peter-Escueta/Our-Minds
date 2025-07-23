<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class SkillCategoryController extends Controller
{
    public function index()
    {
        $categories = SkillCategory::with('questions')->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:skill_categories',
            'color' => 'nullable|string|max:50',
        ]);

        $category = SkillCategory::create($validated);

        return response()->json($category, 201);
    }

    public function show(SkillCategory $skillCategory)
    {
        return response()->json($skillCategory->load('questions'));
    }

    public function update(Request $request, SkillCategory $skillCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:skill_categories,slug,'.$skillCategory->id,
            'color' => 'nullable|string|max:50',
        ]);

        $skillCategory->update($validated);

        return response()->json($skillCategory);
    }

    public function destroy(SkillCategory $skillCategory)
    {
        $skillCategory->delete();
        return response()->json(null, 204);
    }
}