<?php

namespace App\Console\Commands;

use App\Jobs\ReminderNotificationQueue;
use App\Models\User;
use Illuminate\Console\Command;

class ReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:minute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is to look for reminders in every minute and send them accordingly.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = now()->setSeconds(0)->setMicroseconds(0);
        $reminders = User::rightJoin('reminders as r', 'users.id', '=', 'r.owner')
            ->leftJoin('repositories as repo', 'r.repository', '=', 'repo.id')
            ->select('users.name', 'users.id', 'users.email', 'r.title', 'r.message', 'repo.id as repo_id', 'repo.fullname')
            ->selectRaw('to_char(r.due_date, \'Dy DD Mon, YYYY at HH:MI AM \') as due_date')
            ->where('r.due_date', '=', $now)
            ->get()
        ;

        if ($reminders->isNotEmpty()) {
            ReminderNotificationQueue::dispatch($reminders);
        }
    }
}