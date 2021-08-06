<?php

namespace App\Jobs;

use App\Mail\ReminderMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

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
            Mail::to($reminder->email)->send(new ReminderMail($reminder));
        }
    }
}
