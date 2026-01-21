<?php

namespace Modules\Crm\Http\Requests\Professional;

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
            'dni' => 'required|max:64|unique:professionals,dni',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:200|unique:professionals,email',
            'phone_e164' => 'required|max:32|unique:professionals,phone_e164|regex:/^\+[1-9]\d{7,14}$/',
            'professional_type_id' => 'required|exists:professional_types,id',
            'location_id' => 'required|exists:locations,id',
            'bio' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'professional_type_id' => __('professional.form.professional_type_id.label'),
            'first_name' => __('professional.form.first_name.label'),
            'last_name' => __('professional.form.last_name.label'),
            'email' => __('professional.form.email.label'),
            'phone_e164' => __('professional.form.phone_e164.label'),
            'location_id' => __('professional.form.location_id.label'),
            'dni' => __('professional.form.dni.label'),
            'bio' => __('professional.form.bio.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $location_id = is_array($this->location_id) ? array_key_first($this->location_id) : null;
        $professional_type_id = isset($this->professional_type_id['value']) ? $this->professional_type_id['value'] : null;

        $this->merge([
            'dni' => Str::upper($this->dni),
            'first_name' => Str::title($this->first_name),
            'last_name' => Str::title($this->last_name),
            'email' => Str::lower($this->email),
            'phone_e164' => $this->phone_e164,
            'location_id' => $location_id,
            'professional_type_id' => $professional_type_id,
        ]);
    }
}
