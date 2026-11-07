<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class SpecimenDocument extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'specimen_id',
        'filename',
        'path',
        'url',
        'document_type', // field_notes, dna_sequence, permits, publication, report, other
        'description',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the specimen this document belongs to.
     */
    public function specimen(): BelongsTo
    {
        return $this->belongsTo(Specimen::class, 'specimen_id');
    }

    /**
     * Get the admin who uploaded the document.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }
}
