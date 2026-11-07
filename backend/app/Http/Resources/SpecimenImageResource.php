<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecimenImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->_id,
            'specimen_id' => (string) $this->specimen_id,
            'filename' => $this->filename,
            'path' => $this->path,
            'url' => $this->url,
            'caption' => $this->caption,
            'type' => $this->type,
            'file_size' => $this->file_size,
            'mime_type' => $this->mime_type,
            'uploaded_by' => $this->uploaded_by ? (string) $this->uploaded_by : null,
            'uploader' => new AdminResource($this->whenLoaded('uploader')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
