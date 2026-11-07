<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'project_code' => $this->project_code,
            'title' => $this->title,
            'description' => $this->description,
            'principal_researcher_id' => $this->principal_researcher_id ? (string) $this->principal_researcher_id : null,
            'principal_researcher' => new ResearcherResource($this->whenLoaded('principalResearcher')),
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'status' => $this->status,
            'specimens_count' => $this->whenCounted('specimens'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
