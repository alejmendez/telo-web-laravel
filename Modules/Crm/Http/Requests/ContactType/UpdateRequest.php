<?php

namespace Modules\Crm\Http\Requests\ContactType;

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
            'code' => 'required|max:250',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('contacttype.form.name.label'),
            'code' => __('contacttype.form.code.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'code' => Str::slug($this->code),
            'name' => Str::title($this->name),
        ]);
    }
}
