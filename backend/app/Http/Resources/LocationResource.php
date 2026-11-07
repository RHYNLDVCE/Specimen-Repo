<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'province' => $this->province,
            'municipality' => $this->municipality,
            'barangay' => $this->barangay,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'habitat' => $this->habitat,
            'specimens_count' => $this->whenCounted('specimens'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
