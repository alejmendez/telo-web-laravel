<?php

namespace Modules\Crm\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        return $rules;
    }
}
