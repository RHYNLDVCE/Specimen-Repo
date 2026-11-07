<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Specimen;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SpecimenApiTest extends TestCase
{
    protected Admin $admin;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::updateOrCreate(
            ['email' => 'specimen_tester@specimenrepo.local'],
            [
                'name' => 'Specimen Tester',
                'password' => Hash::make('Password123!'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_create_specimen_with_valid_data(): void
    {
        $code = 'SPEC-TEST-' . uniqid();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/specimens', [
                'specimen_code' => $code,
                'scientific_name' => 'Panthera tigris',
                'common_name' => 'Tiger',
                'kingdom' => 'Animalia',
                'preservation_method' => 'skeletal',
                'status' => 'active',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Specimen created successfully.',
                'data' => [
                    'specimen_code' => $code,
                    'scientific_name' => 'Panthera tigris',
                    'status' => 'active',
                ],
            ]);

        $this->assertNotNull(Specimen::where('specimen_code', $code)->first());
    }

    public function test_duplicate_specimen_code_is_rejected(): void
    {
        $code = 'SPEC-DUP-' . uniqid();

        Specimen::create([
            'specimen_code' => $code,
            'scientific_name' => 'Felis catus',
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/specimens', [
                'specimen_code' => $code,
                'scientific_name' => 'Felis catus',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed.',
            ])
            ->assertJsonValidationErrors(['specimen_code']);
    }

    public function test_invalid_specimen_data_is_rejected(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/specimens', [
                // missing required specimen_code and scientific_name
                'status' => 'invalid_status_enum',
            ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['specimen_code', 'scientific_name', 'status']);
    }

    public function test_admin_can_retrieve_specimen_by_id(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-GET-' . uniqid(),
            'scientific_name' => 'Varanus komodoensis',
            'common_name' => 'Komodo Dragon',
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/specimens/' . (string) $specimen->_id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => (string) $specimen->_id,
                    'specimen_code' => $specimen->specimen_code,
                    'scientific_name' => 'Varanus komodoensis',
                ],
            ]);
    }

    public function test_admin_can_update_specimen(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-UPDATE-' . uniqid(),
            'scientific_name' => 'Original Name',
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->putJson('/api/v1/specimens/' . (string) $specimen->_id, [
                'common_name' => 'Updated Common Name',
                'preservation_method' => 'dry',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'common_name' => 'Updated Common Name',
                    'preservation_method' => 'dry',
                ],
            ]);

        $this->assertEquals('Updated Common Name', $specimen->fresh()->common_name);
    }

    public function test_admin_can_delete_specimen(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-DEL-' . uniqid(),
            'scientific_name' => 'Delete Me',
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->deleteJson('/api/v1/specimens/' . (string) $specimen->_id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Specimen deleted successfully.',
            ]);

        $this->assertNull(Specimen::find($specimen->_id));
    }

    public function test_specimens_endpoint_supports_filtering_and_pagination(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/specimens?status=active&per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_specimens_endpoint_supports_search(): void
    {
        $uniqueSciName = 'SearchTarget' . uniqid();
        Specimen::create([
            'specimen_code' => 'SPEC-SEARCH-' . uniqid(),
            'scientific_name' => $uniqueSciName,
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/specimens?search=' . $uniqueSciName);

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals($uniqueSciName, $data[0]['scientific_name']);
    }
}
