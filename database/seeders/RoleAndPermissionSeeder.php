<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator role with full access']
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Standard user role']
        );

        // Create permissions
        $permissions = [
            'users.view' => 'View users',
            'users.create' => 'Create users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
            'roles.view' => 'View roles',
            'roles.create' => 'Create roles',
            'roles.edit' => 'Edit roles',
            'roles.delete' => 'Delete roles',
            'permissions.view' => 'View permissions',
            'permissions.create' => 'Create permissions',
            'permissions.edit' => 'Edit permissions',
            'permissions.delete' => 'Delete permissions',
            'activity-logs.view' => 'View activity logs',
            'activity-logs.export' => 'Export activity logs',
            'alerts.view' => 'View alerts',
            'alerts.create' => 'Create alerts',
            'alerts.edit' => 'Edit alerts',
            'alerts.delete' => 'Delete alerts',
            'alerts.send' => 'Send alerts',
        ];

        $permissionObjects = [];
        foreach ($permissions as $name => $description) {
            $permissionObjects[] = Permission::firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Assign all permissions to admin role
        foreach ($permissionObjects as $permission) {
            $adminRole->permissions()->syncWithoutDetaching($permission);
        }

        // Assign limited permissions to user role
        $userPermissions = Permission::whereIn('name', [
            'activity-logs.view',
            'alerts.view',
            'alerts.create',
            'alerts.edit',
            'alerts.delete',
            'alerts.send',
        ])->get();
        foreach ($userPermissions as $permission) {
            $userRole->permissions()->syncWithoutDetaching($permission);
        }
    }
}

