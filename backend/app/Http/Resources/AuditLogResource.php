<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
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
            'admin_id' => $this->admin_id ? (string) $this->admin_id : null,
            'admin_email' => $this->admin_email,
            'action' => $this->action,
            'resource_type' => $this->resource_type,
            'resource_id' => $this->resource_id ? (string) $this->resource_id : null,
            'description' => $this->description,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
