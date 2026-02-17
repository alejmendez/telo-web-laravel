<?php

namespace Modules\Crm\Services;

use Modules\Core\Services\PrimevueDatatables;
use Modules\Crm\Models\CustomerType;

class CustomerTypeService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'code'];

    public function list(array $params = [])
    {
        $query = CustomerType::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $customertypes = $datatable->of($query)->make();

        return $customertypes;
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(CustomerType::select('id as value', 'name as text')->orderBy('name'), ['id', 'name', 'code'], $filter);
    }

    public function find(int $id): CustomerType
    {
        return CustomerType::findOrFail($id);
    }

    public function create(array $data): CustomerType
    {
        $customertype = new CustomerType;
        $customertype->name = $data['name'];
        $customertype->code = $data['code'];
        $customertype->save();

        return $customertype;
    }

    public function update(int $id, array $data): CustomerType
    {
        $customertype = $this->find($id);
        $customertype->name = $data['name'] ?? $customertype->name;
        $customertype->code = $data['code'] ?? $customertype->code;
        $customertype->save();

        return $customertype;
    }

    public function delete(int $id): bool
    {
        $customertype = $this->find($id);

        return $customertype->delete();
    }
}
