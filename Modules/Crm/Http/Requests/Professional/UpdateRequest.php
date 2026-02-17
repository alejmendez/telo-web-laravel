<?php

namespace Modules\Crm\Http\Requests\Professional;

class UpdateRequest extends StoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $id = $this->route('professional');

        $rules['dni'] = 'required|max:64|unique:professionals,dni,'.$id;

        return $rules;
    }
}
