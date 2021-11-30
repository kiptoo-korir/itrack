<?php

namespace App\Views\Composers;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use illuminate\View\View;

class UserDataComposer
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        // Dependencies are automatically resolved by the service container
        $this->notificationService = $notificationService;
    }

    public function compose(View $view)
    {
        $userData = Auth::user();
        $firstLetter = substr($userData->name, 0, 1);
        $userData->first_letter = $firstLetter;
        $notificationCount = $this->notificationService->getNotificationCount($userData->id);

        $view->with([
            'user_data' => $userData,
            'notification_count' => $notificationCount,
        ]);
    }
}
