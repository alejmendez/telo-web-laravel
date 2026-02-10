<?php

namespace Modules\Crm\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'professional_id' => $this->professional_id,
            'customer' => [
                'full_name' => $this->customer->full_name,
            ],
            'professional' => [
                'full_name' => $this->professional->full_name,
            ],
            'request' => [
                'description' => $this->request->description,
                'address' => $this->request->address,
            ],
            'status' => $this->status,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
