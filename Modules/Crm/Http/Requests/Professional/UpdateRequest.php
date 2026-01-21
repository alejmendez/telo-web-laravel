<?php

namespace Modules\Crm\Http\Requests\Professional;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $id = $this->route('professional');

        $rules['dni'] = 'required|max:64|unique:professionals,dni,' . $id;
        $rules['email'] = 'required|email|max:200|unique:professionals,email,' . $id;
        $rules['phone_e164'] = 'required|max:32|unique:professionals,phone_e164,' . $id . '|regex:/^\+[1-9]\d{7,14}$/';

        return $rules;
    }
}
