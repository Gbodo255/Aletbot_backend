<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AssignRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\CanLogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use CanLogActivity;

    /**
     * Display a listing of the users
     */
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $users = User::with('roles')->paginate(15);

        return response()->json([
            'users' => $users,
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified user
     */
    public function show(Request $request, User $user): JsonResponse
    {
        if (!$request->user()->isAdmin() && $request->user()->id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'user' => $user->load('roles', 'notificationPreferences'),
        ], Response::HTTP_OK);
    }

    /**
     * Assign a role to a user
     */
    public function assignRole(AssignRoleRequest $request, User $user): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validated();
        $role = Role::find($validated['role_id']);
        
        $user->assignRole($role);

        // Log activity using trait
        $this->logActivity(
            'assign_role', 
            "Role {$role->name} assigned to {$user->name}", 
            'User', 
            $user->id
        );

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user->load('roles'),
        ], Response::HTTP_OK);
    }

    /**
     * Remove a role from a user
     */
    public function removeRole(AssignRoleRequest $request, User $user): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validated();
        $role = Role::find($validated['role_id']);
        
        $user->removeRole($role);

        // Log activity using trait
        $this->logActivity(
            'remove_role', 
            "Role {$role->name} removed from {$user->name}", 
            'User', 
            $user->id
        );

        return response()->json([
            'message' => 'Role removed successfully',
            'user' => $user->load('roles'),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified user
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $userName = $user->name;
        $userId = $user->id;

        // Log activity using trait
        $this->logActivity('delete_user', "User {$userName} deleted", 'User', $userId);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], Response::HTTP_OK);
    }
}

