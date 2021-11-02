<?php

namespace App\Notifications;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class InvalidTokenNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     */
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'notification_message' => 'You currently don\'t have a valid token to facilitate access to Github, you can generate a new one here:',
            'notification_title' => 'Invalid Github Token',
            'notification_type' => 'invalidToken',
            'action_link' => route('profile'),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notificationService = new NotificationService();
        $count = $notificationService->getNotificationCount($notifiable->id);

        return new BroadcastMessage([
            'notifications_count' => $count,
            'id' => $this->id,
            'notification_message' => 'You currently don\'t have a valid token to facilitate access to Github, you can generate a new one here:',
            'notification_title' => 'Invalid Github Token',
            'notification_type' => 'invalidToken',
            'action_link' => route('profile'),
        ]);
    }
}
