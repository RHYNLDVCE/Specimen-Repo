<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;
use MongoDB\Laravel\Relations\HasMany;

class Project extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'project_code',
        'title',
        'description',
        'principal_researcher_id',
        'start_date',
        'end_date',
        'status', // planning, active, completed, suspended
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the principal researcher for the project.
     */
    public function principalResearcher(): BelongsTo
    {
        return $this->belongsTo(Researcher::class, 'principal_researcher_id');
    }

    /**
     * Get specimens associated with this project.
     */
    public function specimens(): HasMany
    {
        return $this->hasMany(Specimen::class, 'project_id');
    }
}
