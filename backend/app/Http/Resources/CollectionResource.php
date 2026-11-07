<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
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
            'collection_type' => $this->collection_type,
            'collector' => $this->collector,
            'date_started' => $this->date_started?->format('Y-m-d'),
            'date_ended' => $this->date_ended?->format('Y-m-d'),
            'status' => $this->status,
            'created_by' => $this->created_by ? (string) $this->created_by : null,
            'creator' => new AdminResource($this->whenLoaded('creator')),
            'specimens_count' => $this->whenCounted('specimens'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
