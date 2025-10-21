<?php

namespace Modules\Users\Services;

use Modules\Core\Services\PrimevueDatatables;
use Spatie\Permission\Models\Role;
use Modules\Users\Services\Contracts\RoleServiceContract;

class RoleService implements RoleServiceContract
{
    private const SEARCHABLE_COLUMNS = ['name'];

    public function listSelectValues($params = [])
    {
        return Role::select('name as value', 'name as text')->orderBy('name')->get();
    }

    public function list($params = [])
    {
        $query = Role::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $users = $datatable->of($query)->make();

        return $users;
    }

    public function find($id): Role
    {
        return Role::findOrFail($id);
    }

    public function create(array $data): Role
    {
        $role = new Role;
        $role->name = $data['name'];
        $role->save();

        return $role;
    }

    public function update($id, array $data): Role
    {
        $role = Role::findOrFail($id);

        $role->name = $data['name'];
        $role->save();

        return $role;
    }

    public function delete($id): void
    {
        Role::destroy($id);
    }

    public function syncPermissions($id, array $permissions): void
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($permissions);
    }
}
