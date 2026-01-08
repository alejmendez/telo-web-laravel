<?php

namespace Modules\Cmr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:250',
            'iso2' => 'required|max:2|min:2',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('country.form.name.label'),
            'iso2' => __('country.form.iso2.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::title($this->name),
            'iso2' => Str::upper($this->iso2),
        ]);
    }
}
