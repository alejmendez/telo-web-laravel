<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Requests;
use Modules\Core\Services\PrimevueDatatables;

class RequestsService
{
    protected const SEARCHABLE_COLUMNS = ['name'];

    public function list(Array $params = [])
    {
        $query = Requests::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $requests = $datatable->of($query)->make();

        return $requests;
    }

    public function find(int $id): Requests
    {
        return Requests::findOrFail($id);
    }

    public function create(Array $data): Requests
    {
        $requests = new Requests;
        $requests->name = $data['name'];
        $requests->save();

        return $requests;
    }

    public function update(int $id, Array $data): Requests
    {
        $requests = $this->find($id);
        $requests->name = $data['name'] ?? $requests->name;
        $requests->save();

        return $requests;
    }

    public function delete(int $id): bool
    {
        $requests = $this->find($id);
        return $requests->delete();
    }
}
