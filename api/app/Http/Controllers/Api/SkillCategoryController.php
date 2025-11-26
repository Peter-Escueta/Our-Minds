<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SkillCategoryController extends Controller
{
    /**
     * Display a listing of skill categories with question counts
     */
    public function index()
    {
        try {
            $categories = SkillCategory::withCount('questions')
                ->orderBy('name')
                ->get();

            return response()->json([
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('SkillCategoryController@index error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve categories',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Store a newly created skill category
     */
    public function store(Request $request)
    {
        try {
            $slug = $this->nameToSlug($request->input('name'));
            $color = $this->convertToTailwindClass($request->input('color'));
            $request->merge([
                'slug' => $slug,
                'color' => $color
            ]);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z0-9-]+$/',
                    Rule::unique('skill_categories')
                ],
                'color' => 'nullable|string|max:50',
            ]);

            $category = SkillCategory::create($validated);

            return response()->json([
                'data' => $category,
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error('SkillCategoryController@store error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create category',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function convertToTailwindClass(string $color)
    {
        return 'text-' . $color . '-700';
    }

    public function nameToSlug(string $category)
    {
        return strtolower(str_replace(' ', '-', $category));
    }


    /**
     * Display the specified skill category with questions
     */
    public function show(SkillCategory $skillCategory)
    {
        try {
            return response()->json([
                'data' => $skillCategory->load(['questions' => function ($query) {
                    $query->orderBy('age')->orderBy('created_at');
                }]),
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('SkillCategoryController@show error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve category',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified skill category
     */
    public function update(Request $request, SkillCategory $skillCategory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z0-9-]+$/',
                    Rule::unique('skill_categories')->ignore($skillCategory->id)
                ],
                'color' => 'nullable|string|max:50',
            ]);

            $skillCategory->update($validated);

            return response()->json([
                'data' => $skillCategory,
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('SkillCategoryController@update error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update category',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Remove the specified skill category
     */
    public function destroy(SkillCategory $category)
    {
        try {
            if ($category->questions()->exists()) {
                return response()->json([
                    'message' => 'Cannot delete category with associated questions'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'category' => $category
            ], 200);
        } catch (\Exception $e) {
            Log::error('SkillCategoryController@destroy error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
