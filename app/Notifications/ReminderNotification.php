<?php

namespace App\Notifications;

use App\Mail\ReminderMail;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
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
        return ['database', 'broadcast', 'mail'];
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
        return (new ReminderMail($this->reminder))
            ->to($notifiable->email)
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
        $notificationService = new NotificationService();
        $count = $notificationService->getNotificationCount($notifiable->id);

        return new BroadcastMessage([
            'reminderId' => $this->reminder->reminder_id,
            'notificationsCount' => $count,
            'notificationTitle' => $this->reminder->title,
            'notificationMessage' => $this->reminder->message,
            'id' => $this->id,
            'notificationType' => 'reminder',
        ]);
    }
}
