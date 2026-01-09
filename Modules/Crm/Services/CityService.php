<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\City;
use Modules\Core\Services\PrimevueDatatables;

class CityService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'country_id'];

    public function __construct()
    {
    }

    public function list(Array $params = [])
    {
        $query = City::query()->with('country');

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $cities = $datatable->of($query)->make();

        return $cities;
    }

    public function find(int $id): City
    {
        return City::findOrFail($id);
    }

    public function create(Array $data): City
    {
        $city = new City;
        $city->name = $data['name'];
        $city->country_id = $data['country_id'];
        $city->save();

        return $city;
    }

    public function update(int $id, Array $data): City
    {
        $city = $this->find($id);
        $city->name = $data['name'] ?? $city->name;
        $city->country_id = $data['country_id'] ?? $city->country_id;
        $city->save();

        return $city;
    }

    public function delete(int $id): bool
    {
        $city = $this->find($id);
        return $city->delete();
    }
}
