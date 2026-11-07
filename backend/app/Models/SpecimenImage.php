<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Relations\BelongsTo;

class SpecimenImage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'images';

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
        'caption',
        'type', // dorsal, ventral, lateral, habitat, microscopic, other
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
     * Get the specimen this image belongs to.
     */
    public function specimen(): BelongsTo
    {
        return $this->belongsTo(Specimen::class, 'specimen_id');
    }

    /**
     * Get the admin who uploaded the image.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }
}
