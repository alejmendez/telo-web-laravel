<?php

namespace Modules\Users\Services\Contracts;

use Spatie\Permission\Models\Role;

interface RoleServiceContract
{
    /**
     * Lista roles para select
     */
    public function listSelectValues($params = []);

    /**
     * Lista roles con paginación y filtros
     */
    public function list($params = []);

    /**
     * Encuentra un rol por ID
     */
    public function find($id): Role;

    /**
     * Crea un nuevo rol
     */
    public function create(array $data): Role;

    /**
     * Actualiza un rol existente
     */
    public function update($id, array $data): Role;

    /**
     * Elimina un rol
     */
    public function delete($id): void;
}
