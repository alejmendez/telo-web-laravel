<?php

namespace Modules\Core\Http\Controllers;

use Modules\Core\Services\Contracts\ListNotificationContract;

class NotificationsController extends Controller
{
    private ListNotificationContract $listNotification;

    public function __construct(ListNotificationContract $listNotification)
    {
        $this->listNotification = $listNotification;
    }

    public function unread()
    {
        $user = auth()->user();
        $type = request()->route('type');

        return $this->listNotification->call($user, $type);
    }
}
