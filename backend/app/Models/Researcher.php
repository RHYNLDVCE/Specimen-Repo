<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\HasMany;

class Researcher extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'researchers';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'institution',
        'department',
        'specialization',
        'contact_information',
        'status', // active, inactive
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
     * Get loans associated with this researcher.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'researcher_id');
    }

    /**
     * Get research projects where this researcher is principal investigator.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'principal_researcher_id');
    }
}
