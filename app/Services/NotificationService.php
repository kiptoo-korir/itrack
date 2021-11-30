<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\InvalidTokenNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function getNotificationCount(int $userId): int
    {
        return User::findOrFail($userId)->unreadNotifications
            ->count()
        ;
    }

    public function getTopThreeNotifications(int $userId): Collection
    {
        return DB::table('notifications')
            ->select('id', 'data', 'created_at')
            ->where(['notifiable_id' => $userId, 'notifiable_type' => 'App\Models\User'])
            ->whereNull('read_at')
            ->latest()
            ->limit(3)
            ->get()
        ;
    }

    public function notifyOfInvalidToken(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->notify(new InvalidTokenNotification());
    }
}
