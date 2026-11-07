<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Specimen;
use DomainException;

class SpecimenService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Create a new specimen record.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, Admin $admin): Specimen
    {
        $data['created_by'] = $admin->_id;
        $data['status'] = $data['status'] ?? 'active';

        $specimen = Specimen::create($data);

        $this->auditLogService->log(
            admin: $admin,
            action: 'CREATE_SPECIMEN',
            resourceType: 'Specimen',
            resourceId: (string) $specimen->_id,
            description: "Created specimen {$specimen->specimen_code} ({$specimen->scientific_name})."
        );

        return $specimen->load(['taxon', 'collection', 'location', 'project', 'creator']);
    }

    /**
     * Update an existing specimen record.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Specimen $specimen, array $data, Admin $admin): Specimen
    {
        $specimen->update($data);

        $this->auditLogService->log(
            admin: $admin,
            action: 'UPDATE_SPECIMEN',
            resourceType: 'Specimen',
            resourceId: (string) $specimen->_id,
            description: "Updated specimen {$specimen->specimen_code}."
        );

        return $specimen->fresh(['taxon', 'collection', 'location', 'project', 'creator', 'images', 'documents']);
    }

    /**
     * Delete a specimen record.
     *
     * @throws DomainException
     */
    public function delete(Specimen $specimen, Admin $admin): void
    {
        if ($specimen->status === 'loaned') {
            throw new DomainException("Cannot delete specimen {$specimen->specimen_code} because it is currently loaned.");
        }

        $code = $specimen->specimen_code;
        $id = (string) $specimen->_id;

        $specimen->delete();

        $this->auditLogService->log(
            admin: $admin,
            action: 'DELETE_SPECIMEN',
            resourceType: 'Specimen',
            resourceId: $id,
            description: "Deleted specimen {$code}."
        );
    }
}
