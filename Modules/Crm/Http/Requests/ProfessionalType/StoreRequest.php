<?php

namespace Modules\Crm\Http\Requests\ProfessionalType;

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
            'name' => 'required|max:250',
            'code' => 'required|max:250|unique:professional_types,code',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('professionaltype.form.name.label'),
            'code' => __('professionaltype.form.code.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::title($this->name),
            'code' => Str::slug($this->code),
        ]);
    }
}
