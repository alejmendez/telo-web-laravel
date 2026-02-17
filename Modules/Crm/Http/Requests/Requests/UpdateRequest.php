<?php

namespace Modules\Crm\Http\Requests\Requests;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        return $rules;
    }
}
