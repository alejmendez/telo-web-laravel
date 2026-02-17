<?php

namespace Modules\Crm\Http\Requests\Services;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        return $rules;
    }
}
