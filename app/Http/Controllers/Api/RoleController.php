<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\AssignPermissionRequest;
use App\Http\Requests\Api\Admin\StoreRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Traits\CanLogActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    use CanLogActivity;

    /**
     * Display a listing of the roles
     */
    public function index(Request $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $roles = Role::with('permissions')->get();

        return response()->json([
            'roles' => $roles,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created role
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validated();
        $role = Role::create($validated);

        // Log activity using trait
        $this->logActivity('create_role', "Role {$role->name} created", 'Role', $role->id);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role,
        ], Response::HTTP_CREATED);
    }

    /**
     * Assign a permission to a role
     */
    public function assignPermission(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validated();
        $permission = Permission::find($validated['permission_id']);
        
        $role->permissions()->syncWithoutDetaching($permission);

        // Log activity using trait
        $this->logActivity(
            'assign_permission', 
            "Permission {$permission->name} assigned to role {$role->name}", 
            'Role', 
            $role->id
        );

        return response()->json([
            'message' => 'Permission assigned successfully',
            'role' => $role->load('permissions'),
        ], Response::HTTP_OK);
    }

    /**
     * Remove a permission from a role
     */
    public function removePermission(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validated();
        $permission = Permission::find($validated['permission_id']);
        
        $role->permissions()->detach($permission);

        // Log activity using trait
        $this->logActivity(
            'remove_permission', 
            "Permission {$permission->name} removed from role {$role->name}", 
            'Role', 
            $role->id
        );

        return response()->json([
            'message' => 'Permission removed successfully',
            'role' => $role->load('permissions'),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified role
     */
    public function destroy(Request $request, Role $role): JsonResponse
    {
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $roleName = $role->name;
        $roleId = $role->id;

        // Log activity using trait
        $this->logActivity('delete_role', "Role {$roleName} deleted", 'Role', $roleId);

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ], Response::HTTP_OK);
    }
}

