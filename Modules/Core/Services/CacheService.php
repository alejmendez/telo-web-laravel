<?php

namespace Modules\Core\Services;

use Illuminate\Support\Str;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Modules\Users\Models\User;

class CacheService implements CacheServiceContract
{
    private int $userDataTtl = 600; // 10 minutes

    private int $unreadNotificationsTtl = 60; // 1 minute

    private int $menuTtl = 1; // 86_400; // 24 hours

    public function getUserDataSession(User $user)
    {
        return cache()->remember('user_'.$user->id.'_data_session', $this->userDataTtl, function () use ($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'avatar_url' => $this->getUserAvatar($user),
                'roles' => $this->getUserRoles($user),
                'permissions' => $this->getUserPermissions($user),
                'unread_notifications' => $this->getUserUnreadNotifications($user),
            ];
        });
    }

    public function getUserDataSessionAjax(User $user)
    {
        return cache()->remember('user_'.$user->id.'_data_session_ajax', $this->userDataTtl, function () use ($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'avatar_url' => $this->getUserAvatar($user),
                'unread_notifications' => $this->getUserUnreadNotifications($user),
            ];
        });
    }

    public function getUserAvatar(User $user)
    {
        return cache()->remember('user_'.$user->id.'_avatar', $this->userDataTtl, function () use ($user) {
            return $user->avatar_url;
        });
    }

    public function getUserRoles(User $user)
    {
        return cache()->remember('user_'.$user->id.'_roles', $this->userDataTtl, function () use ($user) {
            return collect($user->getRoleNames())->map(fn ($roleName) => Str::slug($roleName));
        });
    }

    public function getUserPermissions(User $user)
    {
        return cache()->remember('user_'.$user->id.'_permissions', $this->userDataTtl, function () use ($user) {
            return $user->getAllPermissions()->pluck('name')->map(fn ($permissionName) => $permissionName);
        });
    }

    public function getUserUnreadNotifications(User $user)
    {
        return cache()->remember('user_'.$user->id.'_unreadnotifications', $this->unreadNotificationsTtl, function () use ($user) {
            return $user->unreadNotifications->where('type', '!=', 'Modules\Tasks\Notifications\TaskNotification');
        });
    }

    public function clearUserCache(User $user)
    {
        $this->clearUserCacheById($user->id);
    }

    public function clearUserCacheById($userId)
    {
        cache()->forget('menu_'.$userId);
        cache()->forget('user_'.$userId);
        cache()->forget('user_'.$userId.'_avatar');
        cache()->forget('user_'.$userId.'_roles');
        cache()->forget('user_'.$userId.'_permissions');
        cache()->forget('user_'.$userId.'_unreadnotifications');
        cache()->forget('user_'.$userId.'_data_session');
        cache()->forget('user_'.$userId.'_data_session_ajax');
    }
}
