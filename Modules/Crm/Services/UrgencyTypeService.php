<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\UrgencyType;
use Modules\Core\Services\PrimevueDatatables;

class UrgencyTypeService
{
    protected const SEARCHABLE_COLUMNS = ['name'];

    public function list(Array $params = [])
    {
        $query = UrgencyType::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $urgencytypes = $datatable->of($query)->make();

        return $urgencytypes;
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(UrgencyType::select('id as value', 'name as text')->orderBy('name'), ['id', 'name', 'code'], $filter);
    }

    public function find(int $id): UrgencyType
    {
        return UrgencyType::findOrFail($id);
    }

    public function create(Array $data): UrgencyType
    {
        $urgencytype = new UrgencyType;
        $urgencytype->name = $data['name'];
        $urgencytype->code = $data['code'];
        $urgencytype->priority_weight = $data['priority_weight'];
        $urgencytype->sla_hours = $data['sla_hours'];
        $urgencytype->save();

        return $urgencytype;
    }

    public function update(int $id, Array $data): UrgencyType
    {
        $urgencytype = $this->find($id);
        $urgencytype->name = $data['name'] ?? $urgencytype->name;
        $urgencytype->code = $data['code'] ?? $urgencytype->code;
        $urgencytype->priority_weight = $data['priority_weight'] ?? $urgencytype->priority_weight;
        $urgencytype->sla_hours = $data['sla_hours'] ?? $urgencytype->sla_hours;
        $urgencytype->save();

        return $urgencytype;
    }

    public function delete(int $id): bool
    {
        $urgencytype = $this->find($id);
        return $urgencytype->delete();
    }
}
