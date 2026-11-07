<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasMany;

class Taxon extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'taxa';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'kingdom',
        'phylum',
        'class',
        'order',
        'family',
        'genus',
        'species',
        'scientific_name',
        'common_name',
        'authority',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get specimens classified under this taxon.
     */
    public function specimens(): HasMany
    {
        return $this->hasMany(Specimen::class, 'taxon_id');
    }
}
