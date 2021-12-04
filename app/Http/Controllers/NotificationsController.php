<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public function notificationsView(Request $request)
    {
        return view('all-notifications');
    }

    public function getAllNotifications(int $page)
    {
        $userId = Auth::id();
        $limit = 3;
        $offset = ($page - 1) * $limit;

        $totalCount = DB::table('notifications')
            ->selectRaw('count(distinct(id))')
            ->where([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userId,
            ])
            ->get()[0]->count
        ;

        $notifications = DB::table('notifications')->select('data', 'created_at', 'id', 'read_at')
            ->where([
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $userId,
            ])
            ->offset($offset)
            ->limit($limit)
            ->orderBy('created_at', 'desc')
            ->get()
        ;

        $notificationCount = $notifications->count();
        $notificationsShown = $offset + $notificationCount;
        $endOfContent = ($notificationsShown === $totalCount) ? true : false;

        $notificationsDecoded = $notifications->map(function ($notification) {
            $notificationData = json_decode($notification->data);
            $notificationData->created_at = $notification->created_at;
            $notificationData->id = $notification->id;
            $notificationData->read_at = $notification->read_at;

            return $notificationData;
        });

        return response()->json(['notifications' => $notificationsDecoded, 'endOfContentStatus' => $endOfContent, $notificationsShown], 200);
    }
}
