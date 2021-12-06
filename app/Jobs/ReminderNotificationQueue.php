<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReminderNotificationQueue implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $reminders;

    /**
     * Create a new job instance.
     *
     * @param mixed $reminders
     */
    public function __construct($reminders)
    {
        $this->reminders = $reminders;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->reminders as $reminder) {
            $user = User::findOrFail($reminder->user_id);
            $user->notify(new ReminderNotification($reminder));
            $this->logReminder($reminder, $user);
        }
    }

    private function logReminder($reminder, User $user)
    {
        activity('dispatch-reminder')
            ->causedBy($user)
            ->withProperties([
                'action' => 'Successful',
                'reminder' => $reminder,
            ])
            ->log("reminder - {$reminder->title} dispatched")
        ;
    }
}
