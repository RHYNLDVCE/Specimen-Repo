<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure test admin exists in MongoDB
        $this->admin = Admin::updateOrCreate(
            ['email' => 'testadmin@specimenrepo.local'],
            [
                'name' => 'Test Super Admin',
                'password' => Hash::make('Password123!'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );
    }

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'testadmin@specimenrepo.local',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'admin' => ['id', 'name', 'email', 'role', 'status'],
                    'token',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'testadmin@specimenrepo.local',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_admin_can_access_me_profile(): void
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'email' => 'testadmin@specimenrepo.local',
                    'role' => 'super_admin',
                ],
            ]);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_admin_can_log_out(): void
    {
        $token = $this->admin->createToken('logout-test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully logged out.',
            ]);
    }
}
