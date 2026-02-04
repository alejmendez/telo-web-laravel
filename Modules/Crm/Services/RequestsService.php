<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Request;
use Modules\Core\Services\PrimevueDatatables;

class RequestsService
{
    protected const SEARCHABLE_COLUMNS = ['title', 'description', 'status'];

    public function list(Array $params = [])
    {
        $query = Request::with(['customer', 'category', 'urgencyType', 'assignedProfessional']);

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $requests = $datatable->of($query)->make();

        return $requests;
    }

    public function find(int $id): Request
    {
        return Request::with(['customer', 'category', 'urgencyType', 'assignedProfessional'])->findOrFail($id);
    }

    public function create(Array $data): Request
    {
        $request = new Request;

        $request->title = $data['title'];
        $request->description = $data['description'];
        $request->status = $data['status'];
        $request->priority = $data['priority'];
        $request->sla_due_at = $data['sla_due_at'];
        $request->accepted_at = $data['accepted_at'] ?? null;

        $request->customer_id = $data['customer_id'];

        $request->category_id = $data['category_id'];
        $request->urgency_type_id = $data['urgency_type_id'];
        $request->assigned_professional_id = $data['assigned_professional_id'] ?? null;
        $request->save();

        return $request;
    }

    public function update(int $id, Array $data): Request
    {
        $request = $this->find($id);

        $request->title = $data['title'] ?? $request->title;
        $request->description = $data['description'] ?? $request->description;
        $request->status = $data['status'] ?? $request->status;
        $request->priority = $data['priority'] ?? $request->priority;
        $request->sla_due_at = $data['sla_due_at'] ?? $request->sla_due_at;
        $request->accepted_at = $data['accepted_at'] ?? $request->accepted_at;

        $request->customer_id = $data['customer_id'] ?? $request->customer_id;
        $request->category_id = $data['category_id'] ?? $request->category_id;
        $request->urgency_type_id = $data['urgency_type_id'] ?? $request->urgency_type_id;
        $request->assigned_professional_id = $data['assigned_professional_id'] ?? $request->assigned_professional_id;

        $request->save();

        return $request;
    }

    public function delete(int $id): bool
    {
        $request = $this->find($id);
        return $request->delete();
    }
}
