<?php

namespace Modules\Crm\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'sla_due_at' => $this->sla_due_at,
            'accepted_at' => $this->accepted_at,
            'customer_id' => $this->customer_id,
            'assigned_professional_id' => $this->assigned_professional_id,
            'urgency_type_id' => $this->urgency_type_id,
            'subcategory_id' => $this->subcategory_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
