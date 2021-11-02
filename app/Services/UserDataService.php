<?php

namespace App\Services;

use App\Models\AccessToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDataService
{
    public static function fetch_notifications_count()
    {
        $user_id = Auth::id();

        return DB::table('notifications')
            ->selectRaw('count(id)')
            ->where(['notifiable_id' => $user_id, 'notifiable_type' => 'App\Models\User'])
            ->whereNull('read_at')
            ->first()->count
        ;
    }

    public function checkGithubTokenStatus(int $userId): bool
    {
        $validTokenCount = AccessToken::where(['platform' => 1, 'owner' => $userId, 'verified' => true])
            ->count()
        ;

        if (0 === $validTokenCount) {
            return false;
        }

        return true;
    }

    public function checkForPreviousTokens(int $userId): bool
    {
        $validTokenCount = AccessToken::where(['platform' => 1, 'owner' => $userId, 'verified' => true])
            ->withTrashed()
            ->count()
        ;

        if (0 === $validTokenCount) {
            return false;
        }

        return true;
    }
}
