<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;
use MongoDB\Laravel\Relations\HasOne;

class Specimen extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'specimens';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'specimen_code',
        'catalog_number',
        'scientific_name',
        'common_name',
        'kingdom',
        'phylum',
        'class',
        'order',
        'family',
        'genus',
        'species',
        'taxon_id',
        'collection_id',
        'project_id',
        'collector',
        'collection_date',
        'location_id',
        'latitude',
        'longitude',
        'habitat',
        'description',
        'preservation_method',
        'repository_location',
        'status', // active, loaned, damaged, lost, deaccessioned
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'collection_date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Taxon
     */
    public function taxon(): BelongsTo
    {
        return $this->belongsTo(Taxon::class, 'taxon_id');
    }

    /**
     * Relationship: Collection
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    /**
     * Relationship: Location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Relationship: Project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Relationship: Admin creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Relationship: Images
     */
    public function images(): HasMany
    {
        return $this->hasMany(SpecimenImage::class, 'specimen_id');
    }

    /**
     * Relationship: Documents
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SpecimenDocument::class, 'specimen_id');
    }

    /**
     * Relationship: Loans
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'specimen_id');
    }

    /**
     * Relationship: Current Active Loan
     */
    public function activeLoan(): HasOne
    {
        return $this->hasOne(Loan::class, 'specimen_id')
            ->whereIn('status', ['approved', 'active']);
    }
}
