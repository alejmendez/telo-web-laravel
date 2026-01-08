<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Country;
use Modules\Core\Services\PrimevueDatatables;

class CountryService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'iso2'];

    public function list(Array $params = [])
    {
        $query = Country::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $countries = $datatable->of($query)->make();

        return $countries;
    }

    public function find(int $id): Country
    {
        return Country::findOrFail($id);
    }

    public function create(Array $data): Country
    {
        $country = new Country;
        $country->name = $data['name'];
        $country->iso2 = $data['iso2'];
        $country->save();

        return $country;
    }

    public function update(int $id, Array $data): Country
    {
        $country = $this->find($id);
        $country->name = $data['name'] ?? $country->name;
        $country->iso2 = $data['iso2'] ?? $country->iso2;
        $country->save();

        return $country;
    }

    public function delete(int $id): bool
    {
        $country = $this->find($id);
        return $country->delete();
    }
}
