<?php

namespace Modules\Core\Services;

use Modules\Core\Models\Notification;
use Modules\Core\Services\Contracts\ListNotificationContract;
use Modules\Users\Models\User;

class ListNotification implements ListNotificationContract
{
    public function call(User $user, string $type, int $perPage = 4): array
    {
        $userId = $user->id;
        $query = Notification::where('notifiable_id', $userId)->where('notifiable_type', User::class);
        if ($type) {
            $query->where('type', $type);
        }
        $notifications = $query->paginate($perPage);

        return [
            'messages' => $this->formatData($notifications),
            'countMessagesUnRead' => $query->where('read_at', null)->count(),
        ];
    }

    private function formatData($notifications)
    {
        $notifications->transform(function ($notification) {
            $notification_data = $notification['data'];

            $notifier_user = User::find($notification_data['notifier_user_id']);
            $avatar = $notifier_user?->avatar_url;

            return [
                'id' => $notification['id'],
                'notifier_user_avatar' => $avatar,
                'content' => str_replace('&nbsp;', ' ', strip_tags($notification_data['content'])),
                'created_at' => $notification['created_at'],
                'read_at' => $notification['read_at'],
            ];
        });

        return $notifications;
    }
}
