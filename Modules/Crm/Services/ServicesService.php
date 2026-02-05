<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Services;
use Modules\Core\Services\PrimevueDatatables;

class ServicesService
{
    protected const SEARCHABLE_COLUMNS = ['description', 'address'];

    public function list(Array $params = [])
    {
        $query = Services::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $services = $datatable->of($query)->make();

        return $services;
    }

    public function find(int $id): Services
    {
        return Services::findOrFail($id);
    }

    public function create(Array $data): Services
    {
        $services = new Services;
        $services->description = $data['description'];
        $services->status = $data['status'];
        $services->address = $data['address'];
        $services->priority = $data['priority'];
        $services->sla_due_at = $data['sla_due_at'];
        $services->accepted_at = $data['accepted_at'];
        $services->customer_id = $data['customer_id'];
        $services->category_id = $data['category_id'];
        $services->urgency_type_id = $data['urgency_type_id'];
        $services->assigned_professional_id = $data['assigned_professional_id'];
        $services->save();

        return $services;
    }

    public function update(int $id, Array $data): Services
    {
        $services = $this->find($id);
        $services->description = $data['description'] ?? $services->description;
        $services->status = $data['status'] ?? $services->status;
        $services->address = $data['address'] ?? $services->address;
        $services->priority = $data['priority'] ?? $services->priority;
        $services->sla_due_at = $data['sla_due_at'] ?? $services->sla_due_at;
        $services->accepted_at = $data['accepted_at'] ?? $services->accepted_at;
        $services->customer_id = $data['customer_id'] ?? $services->customer_id;
        $services->category_id = $data['category_id'] ?? $services->category_id;
        $services->urgency_type_id = $data['urgency_type_id'] ?? $services->urgency_type_id;
        $services->assigned_professional_id = $data['assigned_professional_id'] ?? $services->assigned_professional_id;
        $services->save();

        return $services;
    }

    public function delete(int $id): bool
    {
        $services = $this->find($id);
        return $services->delete();
    }
}
