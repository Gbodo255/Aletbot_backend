<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\NotificationPreference;
use App\Models\Role;
use App\Models\User;
use App\Traits\CanLogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use CanLogActivity;

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        // Assign default role
        $role = Role::where('name', 'user')->first();
        if ($role) {
            $user->assignRole($role);
        }

        // Create notification preferences
        NotificationPreference::create([
            'user_id' => $user->id,
            'email_notifications' => true,
            'push_notifications' => true,
            'activity_alerts' => true,
            'security_alerts' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->logActivity('register', 'User registered', 'User', $user->id);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    /**
     * Authenticate a user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Log activity using trait
        $this->logActivity('login', 'User logged in', 'User', $user->id);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ], Response::HTTP_OK);
    }

    /**
     * Logout a user
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Log activity using trait
        $this->logActivity('logout', 'User logged out', 'User', $user->id);

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], Response::HTTP_OK);
    }

    /**
     * Get current user profile
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('roles', 'notificationPreferences'),
        ], Response::HTTP_OK);
    }
}

