<?php

namespace Modules\Crm\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Crm\Enums\ContactTypes;

class ProfessionalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $contacts = $this->contacts->map(function ($contact) {
            return [
                'id' => $contact->id,
                'contact_type' => $contact->contact_type,
                'content' => $contact->content,
            ];
        });

        $addresses = $this->addresses->map(function ($address) {
            return [
                'id' => $address->id,
                'location_id' => $address->location_id,
                'address' => $address->address,
                'postal_code' => intval($address->postal_code),
                'is_primary' => $address->is_primary,
            ];
        });

        $email = $contacts->where('contact_type', ContactTypes::Email->value)->pluck('content')->toArray();
        $phone_e164 = $contacts->whereIn('contact_type', [
            ContactTypes::Phone->value,
            ContactTypes::Whatsapp->value,
        ])->pluck('content')->toArray();

        return [
            'id' => $this->id,
            'dni' => $this->dni,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $email,
            'phone_e164' => $phone_e164,
            'professional_type_id' => $this->professional_type_id,
            'average_rating' => $this->average_rating,
            'bio' => $this->bio,
            'categories' => $this->categories->pluck('id')->toArray(),
            'contacts' => $contacts,
            'addresses' => $addresses,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
