<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Request;
use Modules\Crm\Enums\RequestStatus;
use Modules\Core\Services\PrimevueDatatables;

class RequestsService
{
    protected const SEARCHABLE_COLUMNS = ['description', 'status', 'assignedProfessional.full_name', 'customer.full_name'];

    public function list(Array $params = [])
    {
        $query = $this->getRequestQueryBase();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $requests = $datatable->of($query)->make();

        return $requests;
    }

    public function find(int $id): Request
    {
        $query = $this->getRequestQueryBase();
        return $query->findOrFail($id);
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(
            Request::leftJoin('customers', 'requests.customer_id', '=', 'customers.id')
                ->select('requests.id', 'customers.id', 'customers.full_name', 'requests.address')
                ->where('requests.status', RequestStatus::Pending->value)
                ->orderBy('customers.full_name'),
            ['id', 'full_name', 'address'],
            $filter
        )->map(function (Request $request) {
            return [
                'value' => $request->id,
                'text' => $request->full_name . ' - ' . $request->address,
                'customer_id' => $request->customer_id,
            ];
        });
    }

    public function create(Array $data): Request
    {
        $request = new Request;

        $request->address = $data['address'];
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

        $request->address = $data['address'] ?? $request->address;
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

    protected function getRequestQueryBase()
    {
        return Request::with([
            'customer' => function ($query) {
                $query->select('id', 'full_name');
            },
            'assignedProfessional' => function ($query) {
                $query->select('id', 'full_name');
            },
            'category' => function ($query) {
                $query->select('id', 'name');
            },
            'urgencyType' => function ($query) {
                $query->select('id', 'name');
            },
        ]);
    }
}
