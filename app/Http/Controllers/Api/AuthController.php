<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // email verified?
        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email address first. Check your inbox for the verification link.',
            ], 403);
        }

        // admin approved?
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is pending admin approval.',
            ], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'department' => $user->department,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:student,teacher',
            'year'     => 'required_if:role,student|nullable|in:1st,2nd,3rd,4th',
            'section'  => 'required_if:role,student|nullable|in:A,B',
        ]);

        // student হলে email অবশ্যই @stud.kuet.ac.bd
        if ($data['role'] === 'student' && !str_ends_with($data['email'], '@stud.kuet.ac.bd')) {
            return response()->json([
                'message' => 'Students must use a @stud.kuet.ac.bd email.'
            ], 422);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
            'status'   => 'pending',
            'year'     => $data['year'] ?? null,
            'section'  => $data['section'] ?? null,
        ]);

        // verification email পাঠাও
        event(new \Illuminate\Auth\Events\Registered($user));

        return response()->json([
            'message' => 'Registration successful! Please check your email to verify your address, then wait for admin approval.',
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate(['fcm_token' => 'required|string']);

        $user = $request->user();
        $user->update(['fcm_token' => $request->fcm_token]);

        return response()->json(['message' => 'Token saved']);
    }
}