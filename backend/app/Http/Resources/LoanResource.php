<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
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
            'specimen' => new SpecimenResource($this->whenLoaded('specimen')),
            'researcher_id' => (string) $this->researcher_id,
            'researcher' => new ResearcherResource($this->whenLoaded('researcher')),
            'borrower_name' => $this->borrower_name,
            'purpose' => $this->purpose,
            'loan_date' => $this->loan_date?->format('Y-m-d'),
            'expected_return_date' => $this->expected_return_date?->format('Y-m-d'),
            'actual_return_date' => $this->actual_return_date?->format('Y-m-d'),
            'status' => $this->status,
            'notes' => $this->notes,
            'approved_by' => $this->approved_by ? (string) $this->approved_by : null,
            'approver' => new AdminResource($this->whenLoaded('approver')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
