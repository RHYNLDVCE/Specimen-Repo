<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Specimen;
use App\Models\SpecimenDocument;
use App\Models\SpecimenImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Upload and attach an image to a specimen.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function uploadImage(Specimen $specimen, UploadedFile $file, array $metadata, Admin $admin): SpecimenImage
    {
        $disk = config('filesystems.default', 'local');
        $directory = 'specimens/images/' . (string) $specimen->_id;
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($directory, $filename, $disk);
        $url = Storage::disk($disk)->url($path);

        $image = SpecimenImage::create([
            'specimen_id' => $specimen->_id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'url' => $url,
            'caption' => $metadata['caption'] ?? null,
            'type' => $metadata['type'] ?? 'other',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $admin->_id,
        ]);

        $this->auditLogService->log(
            admin: $admin,
            action: 'UPLOAD_IMAGE',
            resourceType: 'SpecimenImage',
            resourceId: (string) $image->_id,
            description: "Uploaded image {$file->getClientOriginalName()} for specimen {$specimen->specimen_code}."
        );

        return $image;
    }

    /**
     * Upload and attach a document to a specimen.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function uploadDocument(Specimen $specimen, UploadedFile $file, array $metadata, Admin $admin): SpecimenDocument
    {
        $disk = config('filesystems.default', 'local');
        $directory = 'specimens/documents/' . (string) $specimen->_id;
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs($directory, $filename, $disk);
        $url = Storage::disk($disk)->url($path);

        $document = SpecimenDocument::create([
            'specimen_id' => $specimen->_id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'url' => $url,
            'document_type' => $metadata['document_type'] ?? 'other',
            'description' => $metadata['description'] ?? null,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $admin->_id,
        ]);

        $this->auditLogService->log(
            admin: $admin,
            action: 'UPLOAD_DOCUMENT',
            resourceType: 'SpecimenDocument',
            resourceId: (string) $document->_id,
            description: "Uploaded document {$file->getClientOriginalName()} for specimen {$specimen->specimen_code}."
        );

        return $document;
    }
}
