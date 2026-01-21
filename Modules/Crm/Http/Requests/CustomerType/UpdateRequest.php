<?php

namespace Modules\Crm\Http\Requests\CustomerType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['code'] = 'required|max:250|unique:customer_types,code,' . $this->id;

        return $rules;
    }
}
