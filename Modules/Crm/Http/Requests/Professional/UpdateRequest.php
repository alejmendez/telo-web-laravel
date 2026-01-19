<?php

namespace Modules\Crm\Http\Requests\Professional;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateRequest extends FormRequest
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
            'name' => __('professional.form.name.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::title($this->name),
        ]);
    }
}
