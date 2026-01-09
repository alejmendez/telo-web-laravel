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
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('city.form.name.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            // 'name' => Str::title($this->name),
        ]);
    }
}
