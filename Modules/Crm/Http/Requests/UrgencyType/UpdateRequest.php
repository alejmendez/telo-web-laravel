<?php

namespace Modules\Crm\Http\Requests\UrgencyType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:250',
            'code' => 'required|max:250|unique:urgency_types,code',
            'priority_weight' => 'required|integer',
            'sla_hours' => 'required|integer',
        ];
    }

    public function attributes()
    {
        return [
            'name' => __('urgencytype.form.name.label'),
            'code' => __('urgencytype.form.code.label'),
            'priority_weight' => __('urgencytype.form.priority_weight.label'),
            'sla_hours' => __('urgencytype.form.sla_hours.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::title($this->name),
            'code' => Str::slug($this->code),
            'priority_weight' => intval($this->priority_weight),
            'sla_hours' => intval($this->sla_hours),
        ]);
    }
}
