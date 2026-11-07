<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResearcherResource extends JsonResource
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
            'email' => $this->email,
            'institution' => $this->institution,
            'department' => $this->department,
            'specialization' => $this->specialization,
            'contact_information' => $this->contact_information,
            'status' => $this->status,
            'loans_count' => $this->whenCounted('loans'),
            'projects_count' => $this->whenCounted('projects'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
