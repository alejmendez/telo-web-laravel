<?php

namespace Modules\Crm\Http\Requests\Customer;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['dni'] = 'required|max:20|unique:customers,dni,' . $this->id;
        $rules['email'] = 'required|email|max:200|unique:customers,email,' . $this->id;
        $rules['phone_e164'] = 'required|max:20|unique:customers,phone_e164,' . $this->id . '|regex:/^\+[1-9]\d{7,14}$/';

        return $rules;
    }
}
