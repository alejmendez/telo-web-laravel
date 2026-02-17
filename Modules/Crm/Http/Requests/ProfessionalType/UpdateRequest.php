<?php

namespace Modules\Crm\Http\Requests\ProfessionalType;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['code'] = 'required|max:250|unique:professional_types,code,'.$this->id;

        return $rules;
    }
}
