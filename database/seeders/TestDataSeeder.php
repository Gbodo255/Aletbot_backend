<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles table data
        DB::statement('PRAGMA foreign_keys = ON');
        
        // Create roles
        \App\Models\Role::create(['name' => 'admin', 'description' => 'Administrator role']);
        \App\Models\Role::create(['name' => 'user', 'description' => 'Standard user role']);

        // Create permissions
        $permissions = [
            'users.view' => 'View users',
            'users.create' => 'Create users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
        ];

        foreach ($permissions as $name => $description) {
            \App\Models\Permission::create(['name' => $name, 'description' => $description]);
        }
    }
}
