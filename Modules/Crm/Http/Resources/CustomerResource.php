<?php

namespace Modules\Crm\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Crm\Enums\ContactTypes;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dni' => $this->dni,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'emails' => $this->emails,
            'phone_e164' => $this->phone_e164,
            'customer_type_id' => $this->customer_type_id,
            'notes' => $this->notes,
            'contacts' => $this->contacts,
            'addresses' => $this->addresses,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
