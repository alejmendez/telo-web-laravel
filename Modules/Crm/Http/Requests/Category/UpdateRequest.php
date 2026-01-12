<?php

namespace Modules\Crm\Http\Requests\Category;

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
            'slug' => 'required|max:250|unique:categories,slug',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('category.form.name.label'),
            'slug' => __('category.form.slug.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::title($this->name),
            'slug' => Str::slug($this->slug),
        ]);
    }
}
