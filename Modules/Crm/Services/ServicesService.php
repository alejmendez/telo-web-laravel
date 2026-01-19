<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Services;
use Modules\Core\Services\PrimevueDatatables;

class ServicesService
{
    protected const SEARCHABLE_COLUMNS = ['name'];

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
        $services->name = $data['name'];
        $services->save();

        return $services;
    }

    public function update(int $id, Array $data): Services
    {
        $services = $this->find($id);
        $services->name = $data['name'] ?? $services->name;
        $services->save();

        return $services;
    }

    public function delete(int $id): bool
    {
        $services = $this->find($id);
        return $services->delete();
    }
}
