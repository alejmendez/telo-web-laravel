<?php

namespace Modules\Crm\Http\Requests\Location;

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
            'type' => 'required|max:250',
            'country_code' => 'required|max:250',
            'parent_id' => 'required|max:250',
            'level' => 'required|max:250',
            'code' => 'required|max:250',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('location.form.name.label'),
            'type' => __('location.form.type.label'),
            'country_code' => __('location.form.country_code.label'),
            'parent_id' => __('location.form.parent_id.label'),
            'level' => __('location.form.level.label'),
            'code' => __('location.form.code.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'type' => Str::title($this->type),
            'country_code' => Str::title($this->country_code),
            'parent_id' => Str::title($this->parent_id),
            'level' => Str::title($this->level),
            'code' => Str::title($this->code),
        ]);
    }
}
