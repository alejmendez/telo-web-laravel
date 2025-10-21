<?php

namespace Modules\Users\Services\Contracts;

use Modules\Users\Models\User;

interface UserServiceContract
{
    /**
     * Lista usuarios con paginación y filtros
     */
    public function list($params = []);

    /**
     * Encuentra un usuario por ID
     */
    public function find($id): User;

    /**
     * Crea un nuevo usuario
     */
    public function create(array $data): User;

    /**
     * Actualiza un usuario existente
     */
    public function update($id, array $data): User;

    /**
     * Elimina un usuario
     */
    public function delete($id): void;
}
