<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $reminder;

    /**
     * Create a new notification instance.
     *
     * @param mixed $reminder
     */
    public function __construct($reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!')
        ;
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
            'details' => $this->reminder,
            'notification_message' => $this->reminder->message,
            'notification_title' => $this->reminder->title,
            'notification_type' => 'reminder',
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $count = User::findOrFail($this->reminder->owner)->unreadNotifications->where('type', '=', 'App\Notifications\ReminderNotification')->count();

        return new BroadcastMessage([
            'reminder_id' => $this->reminder->id,
            'notifications_count' => $count,
            'notification_title' => $this->reminder->title,
            'notification_message' => $this->reminder->message,
            'id' => $this->id,
            'notification_type' => 'reminder',
        ]);
    }
}
