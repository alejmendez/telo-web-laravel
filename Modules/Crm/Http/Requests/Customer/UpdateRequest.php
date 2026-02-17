<?php

namespace Modules\Crm\Http\Requests\Customer;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $id = $this->route('customer');

        $rules['dni'] = 'required|max:20|unique:customers,dni,'.$id;

        return $rules;
    }
}
