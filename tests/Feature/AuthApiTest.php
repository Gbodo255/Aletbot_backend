<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    protected function setupDatabase(): void
    {
        // Create roles
        Role::create(['name' => 'admin', 'description' => 'Admin role']);
        Role::create(['name' => 'user', 'description' => 'User role']);
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token'
        ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token'
        ]);
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name', 'user')->first());

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    public function test_get_authenticated_user_profile()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::where('name', 'user')->first());

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email', 'roles']
        ]);
    }
}
