<?php

namespace Modules\Crm\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:250',
            'country_id' => 'required|exists:countries,id',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('city.form.name.label'),
            'country_id' => __('city.form.country_id.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::title($this->name),
            'country_id' => $this->country_id['value'],
        ]);
    }
}
