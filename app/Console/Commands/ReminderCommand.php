<?php

namespace App\Console\Commands;

use App\Jobs\ReminderNotificationQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        $reminders = DB::table('users')->rightJoin('reminders as r', 'users.id', '=', 'r.owner')
            ->leftJoin('projects as p', 'r.project', '=', 'p.id')
            ->select('users.name', 'users.id as user_id', 'users.email', 'r.id as reminder_id', 'r.title', 'r.message', 'p.id as project_id', 'p.name as project_name')
            ->selectRaw('to_char(r.due_date, \'Dy DD Mon, YYYY at HH:MI AM \') as due_date')
            ->where('r.due_date', '=', $now)
            ->whereNull('r.deleted_at')
            ->get()
        ;

        if ($reminders->isNotEmpty()) {
            ReminderNotificationQueue::dispatch($reminders);
        }
    }
}
