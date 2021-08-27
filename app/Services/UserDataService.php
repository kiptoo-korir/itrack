<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDataService
{
    public static function fetch_notifications_count()
    {
        $user_id = Auth::id();

        return DB::table('notifications')
            ->selectRaw('count(id)')
            ->where(['notifiable_id' => $user_id, 'notifiable_type' => 'App\User'])
            ->whereNull('read_at')
            ->first()->count
        ;
    }
}
