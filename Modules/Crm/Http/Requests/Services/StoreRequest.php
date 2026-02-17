<?php

namespace Modules\Crm\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Crm\Enums\ServicesStatus;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'request_id' => 'required|integer|exists:requests,id',
            'professional_id' => 'required|integer|exists:professionals,id',
            'status' => 'required|string|in:'.implode(',', array_column(ServicesStatus::cases(), 'value')),
            'started_at' => 'required|date',
            'completed_at' => 'nullable|date|after_or_equal:started_at',
        ];
    }

    public function attributes()
    {
        return [
            'request_id' => __('services.form.request_id.label'),
            'professional_id' => __('services.form.professional_id.label'),
            'status' => __('services.form.status.label'),
            'started_at' => __('services.form.started_at.label'),
            'completed_at' => __('services.form.completed_at.label'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'request_id' => $this->request_id['value'] ?? null,
            'professional_id' => $this->professional_id['value'] ?? null,
            'status' => $this->status['value'] ?? null,
        ]);
    }
}
