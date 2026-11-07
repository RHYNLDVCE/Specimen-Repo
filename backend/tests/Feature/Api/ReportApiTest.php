<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    protected Admin $admin;
    protected string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::updateOrCreate(
            ['email' => 'report_tester@specimenrepo.local'],
            [
                'name' => 'Report Tester',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
                'status' => 'active',
            ]
        );
        $this->adminToken = $this->admin->createToken('report-token')->plainTextToken;
    }

    public function test_can_fetch_dashboard_summary(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/reports/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'specimens' => [
                        'total',
                        'by_status',
                    ],
                    'loans' => [
                        'total',
                        'active',
                        'overdue',
                    ],
                    'totals' => [
                        'taxa',
                        'collections',
                        'researchers',
                    ],
                ],
            ]);
    }

    public function test_can_fetch_specimens_report(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/reports/specimens');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'by_status',
                    'by_preservation_method',
                    'by_kingdom',
                    'monthly_acquisitions',
                ],
            ]);
    }

    public function test_can_fetch_loans_report(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/reports/loans');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'by_status',
                    'top_borrowers',
                    'overdue_count',
                    'overdue_loans',
                ],
            ]);
    }

    public function test_can_fetch_taxonomy_report(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->getJson('/api/v1/reports/taxonomy');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'top_families',
                    'top_orders',
                    'by_class',
                    'total_taxa',
                ],
            ]);
    }

    public function test_unauthenticated_request_to_reports_is_denied(): void
    {
        $response = $this->getJson('/api/v1/reports/dashboard');
        $response->assertStatus(401);
    }
}
