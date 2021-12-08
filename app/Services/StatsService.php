<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StatsService
{
    public function getLifetimeStats(int $userId): array
    {
        $logInStats = DB::table('activity_log')->where(['log_name' => 'log in', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        $createTasksStats = DB::table('activity_log')->where(['log_name' => 'create-task', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        $createNotesStats = DB::table('activity_log')->where(['log_name' => 'create-note', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        $createProjectsStats = DB::table('activity_log')->where(['log_name' => 'create-project', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        $remindersDispatchedStats = DB::table('activity_log')->where(['log_name' => 'dispatch-reminder', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        return [
            'logIn' => $logInStats,
            'tasks' => $createTasksStats,
            'notes' => $createNotesStats,
            'projects' => $createProjectsStats,
            'reminders' => $remindersDispatchedStats,
        ];
    }

    public function getStatsInPeriod(int $userId, string $startDate, string $endDate): array
    {
        $logInStats = DB::table('activity_log')->where(['log_name' => 'log in', 'causer_id' => $userId])
            ->whereRaw('created_at >= ? and created_at <= ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        $createTasksStats = DB::table('activity_log')->where(['log_name' => 'create-task', 'causer_id' => $userId])
            ->whereRaw('created_at >= ? and created_at <= ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        $createNotesStats = DB::table('activity_log')->where(['log_name' => 'create-note', 'causer_id' => $userId])
            ->whereRaw('created_at >= ? and created_at <= ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        $createProjectsStats = DB::table('activity_log')->where(['log_name' => 'create-project', 'causer_id' => $userId])
            ->whereRaw('created_at >= ? and created_at <= ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        $remindersDispatchedStats = DB::table('activity_log')->where(['log_name' => 'dispatch-reminder', 'causer_id' => $userId])
            ->whereRaw('created_at >= ? and created_at <= ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        return [
            'logIn' => $logInStats,
            'tasks' => $createTasksStats,
            'notes' => $createNotesStats,
            'projects' => $createProjectsStats,
            'reminders' => $remindersDispatchedStats,
        ];
    }
}
