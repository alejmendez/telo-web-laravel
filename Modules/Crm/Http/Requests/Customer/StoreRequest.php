<?php

namespace Modules\Crm\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dni' => 'required|max:20|unique:customers,dni',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:200|unique:customers,email',
            'phone_e164' => 'required|max:20|unique:customers,phone_e164|regex:/^\+[1-9]\d{7,14}$/',
            'customer_type_id' => 'required|integer|exists:customer_types,id',
            'location_id' => 'required|integer|exists:locations,id',
            'notes' => 'nullable|max:800',
        ];
    }

    public function attributes()
    {
        return [
            'dni' => __('customer.form.dni.label'),
            'first_name' => __('customer.form.first_name.label'),
            'last_name' => __('customer.form.last_name.label'),
            'email' => __('customer.form.email.label'),
            'phone_e164' => __('customer.form.phone_e164.label'),
            'customer_type_id' => __('customer.form.customer_type_id.label'),
            'location_id' => __('customer.form.location_id.label'),
            'notes' => __('customer.form.notes.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $location_id = is_array($this->location_id) ? array_key_first($this->location_id) : null;
        $customer_type_id = isset($this->customer_type_id['value']) ? $this->customer_type_id['value'] : null;
        $this->merge([
            'dni' => Str::upper($this->dni),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'email' => Str::lower($this->email),
            'phone_e164' => $this->phone_e164,
            'customer_type_id' => $customer_type_id,
            'location_id' => $location_id,
            'notes' => $this->notes,
        ]);
    }
}
