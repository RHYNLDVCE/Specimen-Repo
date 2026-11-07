<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;

class Collection extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'collections';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'collection_type',
        'collector',
        'date_started',
        'date_ended',
        'status', // active, archived, closed
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
            'date_started' => 'date',
            'date_ended' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get specimens belonging to this collection.
     */
    public function specimens(): HasMany
    {
        return $this->hasMany(Specimen::class, 'collection_id');
    }

    /**
     * Get the admin who created this collection.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
