<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
            ]);
            $user = User::create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password'])
            ]);
            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User creation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
