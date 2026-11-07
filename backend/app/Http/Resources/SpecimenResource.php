<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecimenResource extends JsonResource
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
            'specimen_code' => $this->specimen_code,
            'catalog_number' => $this->catalog_number,
            'scientific_name' => $this->scientific_name,
            'common_name' => $this->common_name,
            'kingdom' => $this->kingdom,
            'phylum' => $this->phylum,
            'class' => $this->class,
            'order' => $this->order,
            'family' => $this->family,
            'genus' => $this->genus,
            'species' => $this->species,
            'taxon_id' => $this->taxon_id ? (string) $this->taxon_id : null,
            'taxon' => new TaxonResource($this->whenLoaded('taxon')),
            'collection_id' => $this->collection_id ? (string) $this->collection_id : null,
            'collection' => new CollectionResource($this->whenLoaded('collection')),
            'project_id' => $this->project_id ? (string) $this->project_id : null,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'collector' => $this->collector,
            'collection_date' => $this->collection_date?->format('Y-m-d'),
            'location_id' => $this->location_id ? (string) $this->location_id : null,
            'location' => new LocationResource($this->whenLoaded('location')),
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'habitat' => $this->habitat,
            'description' => $this->description,
            'preservation_method' => $this->preservation_method,
            'repository_location' => $this->repository_location,
            'status' => $this->status,
            'created_by' => $this->created_by ? (string) $this->created_by : null,
            'creator' => new AdminResource($this->whenLoaded('creator')),
            'images' => SpecimenImageResource::collection($this->whenLoaded('images')),
            'documents' => SpecimenDocumentResource::collection($this->whenLoaded('documents')),
            'active_loan' => new LoanResource($this->whenLoaded('activeLoan')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
