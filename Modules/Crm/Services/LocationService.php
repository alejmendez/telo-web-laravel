<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Location;
use Modules\Core\Services\PrimevueDatatables;

class LocationService
{
    protected const SEARCHABLE_COLUMNS = ['name'];

    public function list(Array $params = [])
    {
        $locations = Location::query()->orderBy('name')->get();

        return $locations;
    }

    public function find(int $id): Location
    {
        return Location::findOrFail($id);
    }

    public function create(Array $data): Location
    {
        $location = new Location;
        $location->name = $data['name'];
        $location->type = $data['type'];
        $location->country_code_code = $data['country_code'];
        $location->parent_id = $data['parent_id'] ?? null;
        $location->level = $data['level'] ?? 0;
        $location->code = $data['code'] ?? null;

        $location->save();

        return $location;
    }

    public function update(int $id, Array $data): Location
    {
        $location = $this->find($id);
        $location->name = $data['name'] ?? $location->name;
        $location->type = $data['type'] ?? $location->type;
        $location->country_code_code = $data['country_code'] ?? $location->country_code_code;
        $location->parent_id = $data['parent_id'] ?? $location->parent_id;
        $location->level = $data['level'] ?? $location->level;
        $location->code = $data['code'] ?? $location->code;

        $location->save();

        return $location;
    }

    public function delete(int $id): bool
    {
        $location = $this->find($id);
        return $location->delete();
    }
}
