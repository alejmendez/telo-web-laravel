<?php

namespace Modules\Crm\Http\Requests\UrgencyType;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['code'] = 'required|max:250|unique:urgency_types,code,'.$this->id;

        return $rules;
    }
}
