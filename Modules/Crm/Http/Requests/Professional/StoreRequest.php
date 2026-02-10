<?php

namespace Modules\Crm\Http\Requests\Professional;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Modules\Crm\Enums\ContactTypes;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dni' => 'required|max:64|unique:professionals,dni',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'professional_type_id' => 'required|exists:professional_types,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'bio' => 'nullable|string',
            'contacts' => 'required|array',
            'contacts.*.id' => 'nullable|integer|exists:customer_contacts,id',
            'contacts.*.contact_type' => 'required|string|in:' . implode(',', ContactTypes::codes()),
            'contacts.*.content' => [
                'required',
                'max:200',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $contacts = $this->input('contacts');
                    $type = $contacts[$index]['contact_type'] ?? null;

                    if ($type === ContactTypes::Email->value) {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('El formato del correo electrónico es inválido.');
                        }
                    } elseif (in_array($type, [ContactTypes::Phone->value, ContactTypes::Whatsapp->value])) {
                        if (!preg_match('/^\+[1-9]\d{7,14}$/', $value)) {
                            $fail('El formato del teléfono es inválido. Debe cumplir con el estándar E.164 (ej. +54911...)');
                        }
                    }
                },
            ],
            'addresses' => 'required|array',
            'addresses.*.id' => 'nullable|integer|exists:customer_addresses,id',
            'addresses.*.location_id' => 'required|integer|exists:locations,id',
            'addresses.*.address' => 'required|max:200',
            'addresses.*.postal_code' => 'required|max:8|digits:8',
            'addresses.*.is_primary' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'professional_type_id' => __('professional.form.professional_type_id.label'),
            'first_name' => __('professional.form.first_name.label'),
            'last_name' => __('professional.form.last_name.label'),
            'categories' => __('professional.form.categories.label'),
            'dni' => __('professional.form.dni.label'),
            'bio' => __('professional.form.bio.label'),
            'contacts.*.id' => __('contact.form.id.label'),
            'contacts.*.contact_type' => __('contact.form.contact_type.label'),
            'contacts.*.content' => __('contact.form.content.label'),
            'addresses.*.id' => __('address.form.id.label'),
            'addresses.*.location_id' => __('address.form.location_id.label'),
            'addresses.*.address' => __('address.form.address.label'),
            'addresses.*.postal_code' => __('address.form.postal_code.label'),
            'addresses.*.is_primary' => __('address.form.is_primary.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $professional_type_id = isset($this->professional_type_id['value']) ? $this->professional_type_id['value'] : null;
        $categories = $this->categories ? collect($this->categories)->pluck('value')->toArray() : [];

        $contacts = $this->contacts ?? [];
        $contacts = array_map(function ($contact) {
            if ($contact['contact_type'] == null || $contact['content'] == null) {
                return null;
            }
            return [
                'id' => $contact['id'] ?? null,
                'contact_type' => $contact['contact_type']['value'],
                'content' => $contact['content'],
            ];
        }, $contacts);

        $addresses = $this->addresses ?? [];
        $addresses = array_map(function ($address) {
            return [
                'id' => $address['id'] ?? null,
                'location_id' => is_array($address['location_id']) ? array_key_first($address['location_id']) : null,
                'address' => $address['address'],
                'postal_code' => $address['postal_code'],
                'is_primary' => $address['is_primary'],
            ];
        }, $addresses);

        $this->merge([
            'dni' => Str::upper($this->dni),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'professional_type_id' => $professional_type_id,
            'categories' => $categories,
            'contacts' => $contacts,
            'addresses' => $addresses,
        ]);
    }
}
