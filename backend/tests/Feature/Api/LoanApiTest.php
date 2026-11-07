<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Loan;
use App\Models\Researcher;
use App\Models\Specimen;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanApiTest extends TestCase
{
    protected Admin $admin;
    protected Researcher $researcher;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::updateOrCreate(
            ['email' => 'loan_tester@specimenrepo.local'],
            [
                'name' => 'Loan Tester',
                'password' => Hash::make('Password123!'),
                'role' => 'super_admin',
                'status' => 'active',
            ]
        );

        $this->researcher = Researcher::updateOrCreate(
            ['email' => 'borrower_tester@university.edu'],
            [
                'name' => 'Borrower Tester',
                'institution' => 'University Department',
                'status' => 'active',
            ]
        );

        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_admin_can_create_a_loan_for_active_specimen(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-LOAN-' . uniqid(),
            'scientific_name' => 'Canis lupus',
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans', [
                'specimen_id' => (string) $specimen->_id,
                'researcher_id' => (string) $this->researcher->_id,
                'borrower_name' => 'Borrower Tester',
                'purpose' => 'Morphometric skull analysis',
                'loan_date' => '2026-05-01',
                'expected_return_date' => '2026-07-01',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Loan request created successfully.',
                'data' => [
                    'borrower_name' => 'Borrower Tester',
                    'status' => 'pending',
                ],
            ]);
    }

    public function test_a_loaned_specimen_cannot_be_loaned_again(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-ALREADY-LOANED-' . uniqid(),
            'scientific_name' => 'Panthera leo',
            'status' => 'loaned',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans', [
                'specimen_id' => (string) $specimen->_id,
                'researcher_id' => (string) $this->researcher->_id,
                'borrower_name' => 'Borrower Tester',
                'purpose' => 'DNA extraction',
                'loan_date' => '2026-05-01',
                'expected_return_date' => '2026-07-01',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_admin_can_approve_loan_and_update_specimen_status(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-APPROVE-' . uniqid(),
            'scientific_name' => 'Ursus arctos',
            'status' => 'active',
        ]);

        $loan = Loan::create([
            'specimen_id' => $specimen->_id,
            'researcher_id' => $this->researcher->_id,
            'borrower_name' => 'Borrower Tester',
            'purpose' => 'Study purpose',
            'loan_date' => '2026-05-01',
            'expected_return_date' => '2026-07-01',
            'status' => 'pending',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans/' . (string) $loan->_id . '/approve');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'approved',
                ],
            ]);

        $this->assertEquals('loaned', $specimen->fresh()->status);
    }

    public function test_admin_can_return_specimen_and_restores_active_status(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-RETURN-' . uniqid(),
            'scientific_name' => 'Aquila chrysaetos',
            'status' => 'loaned',
        ]);

        $loan = Loan::create([
            'specimen_id' => $specimen->_id,
            'researcher_id' => $this->researcher->_id,
            'borrower_name' => 'Borrower Tester',
            'purpose' => 'Comparative radiography',
            'loan_date' => '2026-04-01',
            'expected_return_date' => '2026-06-01',
            'status' => 'approved',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans/' . (string) $loan->_id . '/return', [
                'actual_return_date' => '2026-05-15',
                'notes' => 'Returned intact in custom padded box.',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'returned',
                    'actual_return_date' => '2026-05-15',
                ],
            ]);

        $this->assertEquals('active', $specimen->fresh()->status);
    }

    public function test_admin_can_cancel_loan(): void
    {
        $specimen = Specimen::create([
            'specimen_code' => 'SPEC-CANCEL-' . uniqid(),
            'scientific_name' => 'Struthio camelus',
            'status' => 'loaned',
        ]);

        $loan = Loan::create([
            'specimen_id' => $specimen->_id,
            'researcher_id' => $this->researcher->_id,
            'borrower_name' => 'Borrower Tester',
            'purpose' => 'Feather keratin spectrometry',
            'loan_date' => '2026-04-01',
            'expected_return_date' => '2026-06-01',
            'status' => 'approved',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/loans/' . (string) $loan->_id . '/cancel', [
                'reason' => 'Permit renewal delay',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'cancelled',
                ],
            ]);

        $this->assertEquals('active', $specimen->fresh()->status);
    }
}
