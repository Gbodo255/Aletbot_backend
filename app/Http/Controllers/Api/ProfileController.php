<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\ChangePasswordRequest;
use App\Http\Requests\Api\Profile\UpdateNotificationPreferencesRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Models\NotificationPreference;
use App\Traits\CanLogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use CanLogActivity;

    /**
     * Get the authenticated user's profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load('roles', 'notificationPreferences'),
        ], Response::HTTP_OK);
    }

    /**
     * Update the authenticated user's profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $oldValues = $user->toArray();

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        // Log activity using trait
        $this->logActivity(
            'update_profile', 
            'Profile updated', 
            'User', 
            $user->id, 
            $oldValues, 
            $user->fresh()->toArray()
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()->load('roles', 'notificationPreferences'),
        ], Response::HTTP_OK);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(UpdateNotificationPreferencesRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();
        $prefs = $user->notificationPreferences;
        
        if (!$prefs) {
            $prefs = NotificationPreference::create([
                'user_id' => $user->id,
            ]);
        }

        $oldValues = $prefs->toArray();
        $prefs->update($validated);

        // Log activity using trait
        $this->logActivity(
            'update_notification_preferences', 
            'Notification preferences updated', 
            'NotificationPreference', 
            $prefs->id, 
            $oldValues, 
            $prefs->fresh()->toArray()
        );

        return response()->json([
            'message' => 'Notification preferences updated successfully',
            'preferences' => $prefs->fresh(),
        ], Response::HTTP_OK);
    }

    /**
     * Change the authenticated user's password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log activity using trait
        $this->logActivity('change_password', 'Password changed', 'User', $user->id);

        return response()->json([
            'message' => 'Password changed successfully',
        ], Response::HTTP_OK);
    }
}

