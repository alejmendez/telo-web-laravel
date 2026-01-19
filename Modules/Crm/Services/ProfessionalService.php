<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Professional;
use Modules\Core\Services\PrimevueDatatables;

class ProfessionalService
{
    protected const SEARCHABLE_COLUMNS = ['name'];

    public function list(Array $params = [])
    {
        $query = Professional::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $professionals = $datatable->of($query)->make();

        return $professionals;
    }

    public function find(int $id): Professional
    {
        return Professional::findOrFail($id);
    }

    public function create(Array $data): Professional
    {
        $professional = new Professional;
        $professional->name = $data['name'];
        $professional->save();

        return $professional;
    }

    public function update(int $id, Array $data): Professional
    {
        $professional = $this->find($id);
        $professional->name = $data['name'] ?? $professional->name;
        $professional->save();

        return $professional;
    }

    public function delete(int $id): bool
    {
        $professional = $this->find($id);
        return $professional->delete();
    }
}
