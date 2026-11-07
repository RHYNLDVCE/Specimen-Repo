<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonResource extends JsonResource
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
            'kingdom' => $this->kingdom,
            'phylum' => $this->phylum,
            'class' => $this->class,
            'order' => $this->order,
            'family' => $this->family,
            'genus' => $this->genus,
            'species' => $this->species,
            'scientific_name' => $this->scientific_name,
            'common_name' => $this->common_name,
            'authority' => $this->authority,
            'description' => $this->description,
            'specimens_count' => $this->whenCounted('specimens'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
