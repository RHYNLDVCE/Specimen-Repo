<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Loan;
use App\Models\Specimen;
use DomainException;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Create a new specimen loan.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws ValidationException
     */
    public function createLoan(array $data, Admin $admin): Loan
    {
        $specimen = Specimen::find($data['specimen_id']);

        if (! $specimen) {
            throw ValidationException::withMessages([
                'specimen_id' => ['The specified biological specimen was not found.'],
            ]);
        }

        if ($specimen->status === 'loaned') {
            throw new DomainException("Specimen [{$specimen->specimen_code}] is already on loan and cannot be loaned again.");
        }

        if (in_array($specimen->status, ['damaged', 'lost', 'deaccessioned'], true)) {
            throw new DomainException("Specimen [{$specimen->specimen_code}] is {$specimen->status} and not available for loan.");
        }

        // Set default status to pending unless admin explicitly approves
        $loanStatus = $data['status'] ?? 'pending';

        $data['status'] = $loanStatus;
        if (in_array($loanStatus, ['approved', 'active'], true)) {
            $data['approved_by'] = $admin->_id;
            $specimen->update(['status' => 'loaned']);
        }

        $loan = Loan::create($data);

        $this->auditLogService->log(
            admin: $admin,
            action: 'CREATE_LOAN',
            resourceType: 'Loan',
            resourceId: (string) $loan->_id,
            description: "Created loan for specimen {$specimen->specimen_code} to borrower {$loan->borrower_name}."
        );

        return $loan->load(['specimen', 'researcher', 'approver']);
    }

    /**
     * Approve a pending loan.
     *
     * @throws DomainException
     */
    public function approveLoan(Loan $loan, Admin $admin): Loan
    {
        if ($loan->status === 'approved' || $loan->status === 'active') {
            throw new DomainException("Loan is already approved/active.");
        }

        if ($loan->status === 'returned' || $loan->status === 'cancelled') {
            throw new DomainException("Cannot approve a loan with status {$loan->status}.");
        }

        $specimen = $loan->specimen;
        if (! $specimen) {
            throw new DomainException("Associated specimen was not found.");
        }

        if ($specimen->status === 'loaned') {
            throw new DomainException("Specimen [{$specimen->specimen_code}] is already loaned.");
        }

        $loan->update([
            'status' => 'approved',
            'approved_by' => $admin->_id,
        ]);

        $specimen->update(['status' => 'loaned']);

        $this->auditLogService->log(
            admin: $admin,
            action: 'APPROVE_LOAN',
            resourceType: 'Loan',
            resourceId: (string) $loan->_id,
            description: "Approved loan for specimen {$specimen->specimen_code}."
        );

        return $loan->fresh(['specimen', 'researcher', 'approver']);
    }

    /**
     * Return a loaned specimen.
     *
     * @throws DomainException
     */
    public function returnLoan(Loan $loan, Admin $admin, ?string $returnDate = null, ?string $notes = null): Loan
    {
        if ($loan->status === 'returned') {
            throw new DomainException("This loan has already been marked as returned.");
        }

        if ($loan->status === 'cancelled') {
            throw new DomainException("Cannot return a cancelled loan.");
        }

        $actualReturnDate = $returnDate ?? now()->format('Y-m-d');

        $updateData = [
            'status' => 'returned',
            'actual_return_date' => $actualReturnDate,
        ];

        if ($notes !== null) {
            $updateData['notes'] = ($loan->notes ? $loan->notes . "\n" : '') . "Return Note: " . $notes;
        }

        $loan->update($updateData);

        // Update specimen status back to active
        if ($loan->specimen) {
            $loan->specimen->update(['status' => 'active']);
        }

        $this->auditLogService->log(
            admin: $admin,
            action: 'RETURN_SPECIMEN',
            resourceType: 'Loan',
            resourceId: (string) $loan->_id,
            description: "Returned loaned specimen {$loan->specimen?->specimen_code}."
        );

        return $loan->fresh(['specimen', 'researcher', 'approver']);
    }

    /**
     * Cancel a loan transaction.
     *
     * @throws DomainException
     */
    public function cancelLoan(Loan $loan, Admin $admin, ?string $reason = null): Loan
    {
        if ($loan->status === 'returned') {
            throw new DomainException("Cannot cancel a completed/returned loan.");
        }

        if ($loan->status === 'cancelled') {
            throw new DomainException("This loan is already cancelled.");
        }

        $previousStatus = $loan->status;

        $updateData = ['status' => 'cancelled'];
        if ($reason) {
            $updateData['notes'] = ($loan->notes ? $loan->notes . "\n" : '') . "Cancellation Reason: " . $reason;
        }

        $loan->update($updateData);

        // If specimen was marked as loaned by this loan, restore to active
        if (in_array($previousStatus, ['approved', 'active'], true) && $loan->specimen) {
            $loan->specimen->update(['status' => 'active']);
        }

        $this->auditLogService->log(
            admin: $admin,
            action: 'CANCEL_LOAN',
            resourceType: 'Loan',
            resourceId: (string) $loan->_id,
            description: "Cancelled loan for specimen {$loan->specimen?->specimen_code}."
        );

        return $loan->fresh(['specimen', 'researcher', 'approver']);
    }
}
