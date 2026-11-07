<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class Loan extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'specimen_id',
        'researcher_id',
        'borrower_name',
        'purpose',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'status', // pending, approved, active, returned, overdue, cancelled
        'notes',
        'approved_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'loan_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the loaned specimen.
     */
    public function specimen(): BelongsTo
    {
        return $this->belongsTo(Specimen::class, 'specimen_id');
    }

    /**
     * Get the researcher borrowing the specimen.
     */
    public function researcher(): BelongsTo
    {
        return $this->belongsTo(Researcher::class, 'researcher_id');
    }

    /**
     * Get the admin who approved the loan.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
