<?php

namespace App\Services;

use App\Models\User;

class NotificationService
{
    public function getNotificationCount(int $userId): int
    {
        return User::findOrFail($userId)->unreadNotifications
            ->count()
        ;
    }
}
