<?php

namespace Modules\Crm\Http\Requests\Category;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['slug'] = 'required|max:250|unique:categories,slug,'.$this->id;

        return $rules;
    }
}
