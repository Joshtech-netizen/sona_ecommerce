<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Strict Validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Find the user by email
        $user = User::where('email', $request->email)->first();

        // 3. Security Check: Verify user exists and password matches the encrypted hash
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // We return a generic 401 error so hackers don't know if the email or password was wrong
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // 4. Generate a secure, randomized API token via Sanctum
        $token = $user->createToken('admin-dashboard-token')->plainTextToken;

        // 5. Check if they are actually an admin
        $isAdmin = $user->hasRole('admin');

        return response()->json([
            'token' => $token,
            'is_admin' => $isAdmin,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        // Revoke the current token, logging the user out securely
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}