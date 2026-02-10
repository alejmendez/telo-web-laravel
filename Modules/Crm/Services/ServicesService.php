<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Service;
use Modules\Crm\Models\Request;
use Modules\Core\Services\PrimevueDatatables;

class ServicesService
{
    protected const SEARCHABLE_COLUMNS = ['description', 'address'];

    public function list(Array $params = [])
    {
        $query = Service::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $services = $datatable->of($query)->make();

        return $services;
    }

    public function find(int $id): Service
    {
        return Service::findOrFail($id);
    }

    public function create(Array $data): Service
    {
        $request_id = $data['request_id'];
        $request = Request::findOrFail($request_id);

        $service = new Service;
        $service->request_id = $request_id;
        $service->customer_id = $request->customer_id;
        $service->professional_id = $data['professional_id'];
        $service->status = $data['status'];
        $service->started_at = $data['started_at'];
        $service->completed_at = $data['completed_at'];

        $service->save();

        return $service;
    }

    public function update(int $id, Array $data): Service
    {
        $request_id = $data['request_id'] ?? $service->request_id;
        $request = Request::findOrFail($request_id);

        $service = $this->find($id);
        $service->request_id = $request_id;
        $service->customer_id = $request->customer_id;
        $service->professional_id = $data['professional_id'] ?? $service->professional_id;
        $service->status = $data['status'] ?? $service->status;
        $service->started_at = $data['started_at'] ?? $service->started_at;
        $service->completed_at = $data['completed_at'] ?? $service->completed_at;

        $service->save();

        return $service;
    }

    public function delete(int $id): bool
    {
        $service = $this->find($id);
        return $service->delete();
    }
}
