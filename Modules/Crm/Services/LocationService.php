<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Location;

class LocationService
{
    protected const SEARCHABLE_COLUMNS = ['name'];

    public function list(array $params = [])
    {
        $locations = Location::query()->orderBy('name')->get();

        return $locations;
    }

    public function listAsSelect()
    {
        $RM = Location::where('code', 'RM')->select('id')->first();
        $RM_children_ids = Location::where('parent_id', $RM->id)->select('id')->pluck('id');
        $RM_children_lv2_ids = Location::whereIn('parent_id', $RM_children_ids)->select('id')->pluck('id');

        $ids = array_merge(
            [$RM->id],
            $RM_children_ids->toArray(),
            $RM_children_lv2_ids->toArray()
        );

        $locations = Location::orderBy('name')
            ->select('id', 'name', 'parent_id', 'code')
            ->whereIn('id', $ids);

        return $locations->get();
    }

    public function find(int $id): Location
    {
        return Location::findOrFail($id);
    }

    public function create(array $data): Location
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

    public function update(int $id, array $data): Location
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
