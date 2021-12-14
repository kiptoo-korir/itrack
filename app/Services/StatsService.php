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

    public function getTasksStats(int $userId): array
    {
        return DB::select(DB::raw('
            with tasks_info AS
            (SELECT log_name, subject_id, properties::jsonb->\'task\' as task,
            to_char(created_at, \'Dy DD Mon, YYYY at HH:MI AM \') as created_at
            FROM activity_log
            WHERE log_name IN (:log_name_1, :log_name_2)
            AND causer_id = :causer_id
            ORDER BY activity_log.created_at DESC
            LIMIT 1000
            )

            SELECT log_name, subject_id as task_id, task::jsonb->>\'title\' as task_name,
            task::jsonb->>\'description\' as task_description, created_at
            FROM tasks_info
        '), [
            'log_name_1' => 'create-task',
            'log_name_2' => 'update-task',
            'causer_id' => $userId,
        ]);
    }

    public function getTasksStatsInPeriod(int $userId, string $startDate, string $endDate): array
    {
        return DB::select(DB::raw('
            with tasks_info AS
            (SELECT log_name, subject_id, properties::jsonb->\'task\' as task,
            to_char(created_at, \'Dy DD Mon, YYYY at HH:MI AM \') as created_at
            FROM activity_log
            WHERE log_name IN (:log_name_1, :log_name_2)
            AND activity_log.created_at BETWEEN :start_date AND :end_date
            AND causer_id = :causer_id
            ORDER BY activity_log.created_at DESC
            LIMIT 1000
            )

            SELECT log_name, subject_id as task_id, task::jsonb->>\'title\' as task_name,
            task::jsonb->>\'description\' as task_description, created_at
            FROM tasks_info
        '), [
            'log_name_1' => 'create-task',
            'log_name_2' => 'update-task',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'causer_id' => $userId,
        ]);
    }

    public function createdVsCompleted(int $userId, string $startDate, string $endDate): array
    {
        $createTasksStats = DB::table('activity_log')->where(['log_name' => 'create-task', 'causer_id' => $userId])
            ->whereRaw('created_at > ? and created_at < ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        $updateTasksStats = DB::table('activity_log')->where(['log_name' => 'update-task', 'causer_id' => $userId])
            ->whereRaw('created_at > ? and created_at < ?', [$startDate, $endDate])
            ->distinct('id')
            ->count()
        ;

        return [
            'created' => $createTasksStats,
            'completed' => $updateTasksStats,
        ];
    }

    public function getNotes(int $userId): array
    {
        return DB::select(DB::raw('
            with notes_info AS
            (SELECT log_name, subject_id, properties::jsonb->\'note\' as note,
            to_char(created_at, \'Dy DD Mon, YYYY at HH:MI AM \') as created_at
            FROM activity_log
            WHERE log_name = :log_name
            AND causer_id = :causer_id
            ORDER BY activity_log.created_at DESC
            LIMIT 1000
            )

            SELECT log_name, subject_id as note_id, note::jsonb->>\'title\' as note_title,
            note::jsonb->>\'message\' as note_description, 
            note::jsonb->>\'project\' as project, created_at
            FROM notes_info
        '), [
            'log_name' => 'create-note',
            'causer_id' => $userId,
        ]);
    }

    public function getNotesInPeriod(int $userId, string $startDate, string $endDate): array
    {
        return DB::select(DB::raw('
            with notes_info AS
            (SELECT log_name, subject_id, properties::jsonb->\'note\' as note,
            to_char(created_at, \'Dy DD Mon, YYYY at HH:MI AM \') as created_at
            FROM activity_log
            WHERE log_name = :log_name
            AND activity_log.created_at BETWEEN :start_date AND :end_date
            AND causer_id = :causer_id
            ORDER BY activity_log.created_at DESC
            LIMIT 1000
            )

            SELECT log_name, subject_id as note_id, note::jsonb->>\'title\' as note_title,
            note::jsonb->>\'message\' as note_description, 
            note::jsonb->>\'project\' as project, created_at
            FROM notes_info
        '), [
            'log_name' => 'create-note',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'causer_id' => $userId,
        ]);
    }

    public function getNotesInPeriodAndProject(int $userId, int $project, string $startDate, string $endDate): array
    {
        return DB::select(DB::raw('
            with notes_info AS
            (SELECT log_name, subject_id, properties::jsonb->\'note\' as note,
            to_char(created_at, \'Dy DD Mon, YYYY at HH:MI AM \') as created_at
            FROM activity_log
            WHERE log_name = :log_name
            AND activity_log.created_at BETWEEN :start_date AND :end_date
            AND causer_id = :causer_id
            ORDER BY activity_log.created_at DESC
            LIMIT 1000
            )

            SELECT log_name, subject_id as note_id, note::jsonb->>\'title\' as note_title,
            note::jsonb->>\'message\' as note_description,
            note::jsonb->>\'project\' as project, created_at
            FROM notes_info
            WHERE note::jsonb->>\'project\' = :project
        '), [
            'log_name' => 'create-note',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'causer_id' => $userId,
            'project' => $project,
        ]);
    }
}
