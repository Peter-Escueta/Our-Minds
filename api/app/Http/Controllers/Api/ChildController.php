<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Child;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChildController extends Controller
{
    public function index()
    {
        $children = Child::with('assessments.evaluations')->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($children);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'educational_placement' => 'nullable|string|max:255',
            'is_initial_assessment' => 'boolean',
            'is_follow_up' => 'boolean',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'date_of_birth' => 'required|date',
            'date_of_assessment' => 'required|date',
            'age_at_consult' => 'required|string|max:50',
            'gender' => 'required|in:male,female,other',
            'siblings' => 'nullable|string',
            'mother_name' => 'required|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_contact' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_contact' => 'required|string|max:255',
            'medical_diagnosis' => 'nullable|string',
            'referring_doctor' => 'nullable|string|max:255',
            'last_assessment_date' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'occupational_therapy' => 'boolean',
            'physical_therapy' => 'boolean',
            'behavioral_therapy' => 'boolean',
            'speech_therapy' => 'boolean',
            'school' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:50',
            'placement' => 'nullable|string|max:255',
            'year' => 'nullable|int',
            'reason' => 'nullable|string',
        ]);

        $child = Child::create($validated);

        return response()->json($child, 201);
    }

    public function show(Child $child)
    {
        return response()->json($child->load('assessments'));
    }

    public function update(Request $request, Child $child)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'educational_placement' => 'nullable|string|max:255',
            'is_initial_assessment' => 'boolean',
            'is_follow_up' => 'boolean',
            'address' => 'nullable|string',
            'email' => 'nullable|email',
            'date_of_birth' => 'required|date',
            'date_of_assessment' => 'required|date',
            'age_at_consult' => 'required|string|max:50',
            'gender' => 'required|in:male,female,other',
            'siblings' => 'nullable|string',
            'mother_name' => 'required|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'mother_contact' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'father_contact' => 'required|string|max:255',
            'medical_diagnosis' => 'nullable|string',
            'referring_doctor' => 'nullable|string|max:255',
            'last_assessment_date' => 'nullable|date',
            'follow_up_date' => 'nullable|date',
            'occupational_therapy' => 'boolean',
            'physical_therapy' => 'boolean',
            'behavioral_therapy' => 'boolean',
            'speech_therapy' => 'boolean',
            'school' => 'nullable|string|max:255',
            'grade' => 'nullable|string|max:50',
            'placement' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:50',
            'reason' => 'nullable|string',
        ]);

        $child->update($validated);

        return response()->json($child);
    }

    public function destroy(Child $child)
    {
        $child->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = Child::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('surname', 'like', "%$search%")
                    ->orWhere('first_name', 'like', "%$search%")
                    ->orWhere('mother_name', 'like', "%$search%");
            });
        }

        return response()->json($query->orderBy('surname')->get());
    }
}
