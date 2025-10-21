<?php

namespace Modules\Core\Services\Contracts;

use Modules\Users\Models\User;

interface CacheServiceContract
{
    public function getUserDataSession(User $user);

    public function getUserDataSessionAjax(User $user);

    public function getUserAvatar(User $user);

    public function getUserRoles(User $user);

    public function getUserPermissions(User $user);

    public function getUserUnreadNotifications(User $user);

    public function clearUserCache(User $user);

    public function clearUserCacheById($userId);
}
