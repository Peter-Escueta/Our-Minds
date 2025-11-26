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
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User deletion failed', 'error' => $e->getMessage()], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string',
                'role' => 'required|string',
                'name' => 'required|string'
            ]);
            $user = User::create([
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
                'name' => $validated['name']
            ]);
            return response()->json($user, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User creation failed', 'error' => $e->getMessage()], 500);
        }
    }
}
