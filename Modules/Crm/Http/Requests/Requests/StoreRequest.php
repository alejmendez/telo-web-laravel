<?php

namespace Modules\Crm\Http\Requests\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Crm\Enums\RequestStatus;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string',
            'status' => 'required|in:'.implode(',', RequestStatus::values()),
            'priority' => 'required|integer',
            'address' => 'required|string',
            'sla_due_at' => 'required|date_format:Y-m-d',
            'accepted_at' => 'nullable|date_format:Y-m-d',
            'customer_id' => 'required|exists:customers,id',
            'category_id' => 'required|exists:categories,id',
            'urgency_type_id' => 'required|exists:urgency_types,id',
            'professional_id' => 'nullable|exists:professionals,id',
        ];
    }

    public function attributes()
    {
        return [
            'description' => __('requests.form.description.label'),
            'status' => __('requests.form.status.label'),
            'priority' => __('requests.form.priority.label'),
            'address' => __('requests.form.address.label'),
            'sla_due_at' => __('requests.form.sla_due_at.label'),
            'accepted_at' => __('requests.form.accepted_at.label'),
            'customer_id' => __('requests.form.customer_id.label'),
            'category_id' => __('requests.form.category_id.label'),
            'urgency_type_id' => __('requests.form.urgency_type_id.label'),
            'professional_id' => __('requests.form.professional_id.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $status = isset($this->status['value']) ? $this->status['value'] : null;
        $customer_id = isset($this->customer_id['value']) ? $this->customer_id['value'] : null;
        $category_id = isset($this->category_id['value']) ? $this->category_id['value'] : null;
        $urgency_type_id = isset($this->urgency_type_id['value']) ? $this->urgency_type_id['value'] : null;
        $professional_id = isset($this->professional_id['value']) ? $this->professional_id['value'] : null;

        $this->merge([
            'description' => $this->description,
            'status' => $status,
            'priority' => $this->priority,
            'address' => $this->address,
            'sla_due_at' => $this->sla_due_at ? date('Y-m-d', strtotime($this->sla_due_at)) : null,
            'accepted_at' => $this->accepted_at ? date('Y-m-d', strtotime($this->accepted_at)) : null,
            'customer_id' => $customer_id,
            'category_id' => $category_id,
            'urgency_type_id' => $urgency_type_id,
            'professional_id' => $professional_id,
        ]);
    }
}
