<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Taxon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaxonApiTest extends TestCase
{
    protected Admin $admin;
    protected Admin $researcher;
    protected string $adminToken;
    protected string $researcherToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::updateOrCreate(
            ['email' => 'taxon_admin@specimenrepo.local'],
            [
                'name' => 'Taxon Admin',
                'password' => Hash::make('Password123!'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );
        $this->adminToken = $this->admin->createToken('admin-token')->plainTextToken;

        $this->researcher = Admin::updateOrCreate(
            ['email' => 'taxon_researcher@specimenrepo.local'],
            [
                'name' => 'Taxon Researcher',
                'password' => Hash::make('Password123!'),
                'role' => 'researcher',
                'status' => 'active',
            ]
        );
        $this->researcherToken = $this->researcher->createToken('researcher-token')->plainTextToken;
    }

    public function test_can_list_taxa_with_pagination(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/taxa');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_admin_can_create_taxon(): void
    {
        $uniqueSciName = 'Panthera onca ' . uniqid();
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/v1/taxa', [
                'scientific_name' => $uniqueSciName,
                'common_name' => 'Jaguar',
                'kingdom' => 'Animalia',
                'phylum' => 'Chordata',
                'class' => 'Mammalia',
                'order' => 'Carnivora',
                'family' => 'Felidae',
                'genus' => 'Panthera',
                'species' => 'onca',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'scientific_name' => $uniqueSciName,
                    'genus' => 'Panthera',
                    'species' => 'onca',
                ],
            ]);

        $this->assertNotNull(Taxon::where('scientific_name', $uniqueSciName)->first());
    }

    public function test_researcher_cannot_create_taxon(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->researcherToken)
            ->postJson('/api/v1/taxa', [
                'scientific_name' => 'Unauthorized species',
            ]);

        $response->assertStatus(403);
    }

    public function test_can_retrieve_single_taxon(): void
    {
        $taxon = Taxon::firstOrCreate(
            ['scientific_name' => 'Canis lupus'],
            [
                'common_name' => 'Wolf',
                'kingdom' => 'Animalia',
                'genus' => 'Canis',
                'species' => 'lupus',
            ]
        );

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson("/api/v1/taxa/{$taxon->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => (string) $taxon->id,
                    'scientific_name' => 'Canis lupus',
                ],
            ]);
    }
}
