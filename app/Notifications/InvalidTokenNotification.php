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
    protected $clientId;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->clientId = env('GITHUB_CLIENT_ID');
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
            'notification_message' => 'You currently don\'t have a valid token to facilitate access to Github, you can generate a new one from the profile tab or through this',
            'notification_title' => 'Invalid Github Token',
            'notification_type' => 'invalidToken',
            'action_link' => "https://github.com/login/oauth/authorize?client_id={$this->clientId}&scope=repo%20notifications%20user",
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notificationService = new NotificationService();
        $count = $notificationService->getNotificationCount($notifiable->id);

        return new BroadcastMessage([
            'notifications_count' => $count,
            'id' => $this->id,
            'notification_message' => 'You currently don\'t have a valid token to facilitate access to Github, you can generate a new one from the profile tab or through this',
            'notification_title' => 'Invalid Github Token',
            'notification_type' => 'Github Token Status',
            'action_link' => "https://github.com/login/oauth/authorize?client_id={$this->clientId}&scope=repo%20notifications%20user",
        ]);
    }
}
