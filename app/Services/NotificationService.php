<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\InvalidTokenNotification;

class NotificationService
{
    public function getNotificationCount(int $userId): int
    {
        return User::findOrFail($userId)->unreadNotifications
            ->count()
        ;
    }

    public function notifyOfInvalidToken(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->notify(new InvalidTokenNotification());
    }
}
