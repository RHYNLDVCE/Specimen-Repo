<?php

namespace App\Repositories;

use App\Models\Specimen;
use Illuminate\Pagination\LengthAwarePaginator;

class SpecimenRepository
{
    /**
     * Query and paginate specimens with filters, search, and sorting.
     *
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Specimen::query()->with(['taxon', 'collection', 'location', 'project', 'creator']);

        // Filtering by status
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filtering by collection_id
        if (! empty($filters['collection_id'])) {
            $query->where('collection_id', $filters['collection_id']);
        }

        // Filtering by taxon_id
        if (! empty($filters['taxon_id'])) {
            $query->where('taxon_id', $filters['taxon_id']);
        }

        // Filtering by location_id
        if (! empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }

        // Filtering by project_id
        if (! empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        // Filtering by kingdom, family, genus
        if (! empty($filters['kingdom'])) {
            $query->where('kingdom', $filters['kingdom']);
        }
        if (! empty($filters['family'])) {
            $query->where('family', $filters['family']);
        }
        if (! empty($filters['genus'])) {
            $query->where('genus', $filters['genus']);
        }

        // Search by scientific_name, common_name, specimen_code, catalog_number
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $regex = ['$regex' => $search, '$options' => 'i'];
                $q->where('scientific_name', 'regex', $regex)
                  ->orWhere('common_name', 'regex', $regex)
                  ->orWhere('specimen_code', 'regex', $regex)
                  ->orWhere('catalog_number', 'regex', $regex)
                  ->orWhere('genus', 'regex', $regex)
                  ->orWhere('species', 'regex', $regex);
            });
        }

        // Sorting
        $allowedSortFields = [
            'specimen_code', 'scientific_name', 'common_name',
            'collection_date', 'status', 'created_at', 'updated_at'
        ];
        $sortBy = in_array($filters['sort_by'] ?? null, $allowedSortFields, true)
            ? $filters['sort_by']
            : 'created_at';
        $sortOrder = strtolower($filters['sort_order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Find a specimen by ID with all relations.
     */
    public function findById(string $id): ?Specimen
    {
        return Specimen::with(['taxon', 'collection', 'location', 'project', 'creator', 'images', 'documents', 'loans'])
            ->find($id);
    }
}
