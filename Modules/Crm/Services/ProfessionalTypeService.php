<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\ProfessionalType;
use Modules\Core\Services\PrimevueDatatables;

class ProfessionalTypeService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'code'];

    public function list(Array $params = [])
    {
        $query = ProfessionalType::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $professionaltypes = $datatable->of($query)->make();

        return $professionaltypes;
    }

    public function find(int $id): ProfessionalType
    {
        return ProfessionalType::findOrFail($id);
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(ProfessionalType::select('id as value', 'name as text')->orderBy('name'), ['id', 'name', 'code'], $filter);
    }

    public function create(Array $data): ProfessionalType
    {
        $professionaltype = new ProfessionalType;
        $professionaltype->name = $data['name'];
        $professionaltype->code = $data['code'];
        $professionaltype->save();

        return $professionaltype;
    }

    public function update(int $id, Array $data): ProfessionalType
    {
        $professionaltype = $this->find($id);
        $professionaltype->name = $data['name'] ?? $professionaltype->name;
        $professionaltype->code = $data['code'] ?? $professionaltype->code;
        $professionaltype->save();

        return $professionaltype;
    }

    public function delete(int $id): bool
    {
        $professionaltype = $this->find($id);
        return $professionaltype->delete();
    }
}
