<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\ContactType;
use Modules\Core\Services\PrimevueDatatables;

class ContactTypeService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'code'];

    public function list(Array $params = [])
    {
        $query = ContactType::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $contacttypes = $datatable->of($query)->make();

        return $contacttypes;
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(ContactType::select('id as value', 'name as text')->orderBy('name'), ['id', 'name', 'code'], $filter);
    }

    public function find(int $id): ContactType
    {
        return ContactType::findOrFail($id);
    }

    public function create(Array $data): ContactType
    {
        $contacttype = new ContactType;
        $contacttype->name = $data['name'];
        $contacttype->code = $data['code'];
        $contacttype->save();

        return $contacttype;
    }

    public function update(int $id, Array $data): ContactType
    {
        $contacttype = $this->find($id);
        $contacttype->name = $data['name'] ?? $contacttype->name;
        $contacttype->code = $data['code'] ?? $contacttype->code;
        $contacttype->save();

        return $contacttype;
    }

    public function delete(int $id): bool
    {
        $contacttype = $this->find($id);
        return $contacttype->delete();
    }
}
