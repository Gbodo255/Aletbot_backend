<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RoleAndPermissionSeeder::class);

        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Assign admin role
        $user->assignRole(\App\Models\Role::where('name', 'admin')->first());

        // Create notification preferences for admin
        \App\Models\NotificationPreference::create([
            'user_id' => $user->id,
        ]);
    }
}
