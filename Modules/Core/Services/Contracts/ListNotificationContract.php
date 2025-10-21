<?php

namespace Modules\Core\Services\Contracts;

use Modules\Users\Models\User;

interface ListNotificationContract
{
    /**
     * Obtiene notificaciones paginadas formateadas para el usuario y tipo indicado
     *
     * @return array{messages: mixed, countMessagesUnRead: int}
     */
    public function call(User $user, string $type): array;
}
