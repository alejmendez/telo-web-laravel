<?php

namespace Modules\Crm\Http\Requests\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'status' => 'required|in:pending,active,rejected',
            'priority' => 'required|integer',
            'sla_due_at' => 'required|date_format:Y-m-d',
            'accepted_at' => 'nullable|date_format:Y-m-d',
            'customer_id' => 'required|exists:customers,id',
            'category_id' => 'required|exists:categories,id',
            'urgency_type_id' => 'required|exists:urgency_types,id',
            'assigned_professional_id' => 'nullable|exists:professionals,id',
        ];
    }

    public function attributes()
    {
        return [
            'title' => __('requests.form.title.label'),
            'description' => __('requests.form.description.label'),
            'status' => __('requests.form.status.label'),
            'priority' => __('requests.form.priority.label'),
            'sla_due_at' => __('requests.form.sla_due_at.label'),
            'accepted_at' => __('requests.form.accepted_at.label'),
            'customer_id' => __('requests.form.customer_id.label'),
            'category_id' => __('requests.form.category_id.label'),
            'urgency_type_id' => __('requests.form.urgency_type_id.label'),
            'assigned_professional_id' => __('requests.form.assigned_professional_id.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $status = isset($this->status['value']) ? $this->status['value'] : null;
        $customer_id = isset($this->customer_id['value']) ? $this->customer_id['value'] : null;
        $category_id = isset($this->category_id['value']) ? $this->category_id['value'] : null;
        $urgency_type_id = isset($this->urgency_type_id['value']) ? $this->urgency_type_id['value'] : null;
        $assigned_professional_id = isset($this->assigned_professional_id['value']) ? $this->assigned_professional_id['value'] : null;

        $this->merge([
            'title' => Str::title($this->title),
            'description' => Str::title($this->description),
            'status' => $status,
            'priority' => $this->priority,
            'sla_due_at' => $this->sla_due_at ? date('Y-m-d', strtotime($this->sla_due_at)) : null,
            'accepted_at' => $this->accepted_at ? date('Y-m-d', strtotime($this->accepted_at)) : null,
            'customer_id' => $customer_id,
            'category_id' => $category_id,
            'urgency_type_id' => $urgency_type_id,
            'assigned_professional_id' => $assigned_professional_id,
        ]);
    }
}
