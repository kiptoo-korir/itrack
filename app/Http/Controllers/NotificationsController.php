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
        $limit = 50;
        $offset = ($page - 1) * $limit;
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
        $endOfPagination = ($limit * $page > $notificationCount) ? $notificationCount : ($limit * $page);

        $paginationString = ($offset * $limit + 1).'-'.$endOfPagination.' of '.$notificationCount;

        $notificationsDecoded = $notifications->map(function ($notification) {
            $notificationData = json_decode($notification->data);
            $notificationData->created_at = $notification->created_at;
            $notificationData->id = $notification->id;
            $notificationData->read_at = $notification->read_at;

            return $notificationData;
        });

        return response()->json(['notifications' => $notificationsDecoded, 'paginationString' => $paginationString], 200);
    }
}
