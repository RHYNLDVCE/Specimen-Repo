<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasMany;

class Location extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'province',
        'municipality',
        'barangay',
        'latitude',
        'longitude',
        'habitat',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get specimens collected at this location.
     */
    public function specimens(): HasMany
    {
        return $this->hasMany(Specimen::class, 'location_id');
    }
}
