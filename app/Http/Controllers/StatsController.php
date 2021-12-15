<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StatsController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function tasksStatsViews()
    {
        $userId = Auth::id();
        $createTasksStats = DB::table('activity_log')->where(['log_name' => 'create-task', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        $updateTasksStats = DB::table('activity_log')->where(['log_name' => 'update-task', 'causer_id' => $userId])
            ->distinct('id')
            ->count()
        ;

        $data = [
            'tasks' => $createTasksStats,
            'tasksDone' => $updateTasksStats,
        ];

        return view('stats.task')->with($data);
    }

    public function getTaskActivity()
    {
        $userId = Auth::id();
        $tasksStats = $this->statsService->getTasksStats($userId);

        return DataTables::of($tasksStats)
            ->make(true)
        ;
    }

    public function getTaskActivityInPeriod(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $tasksStats = $this->statsService->getTasksStatsInPeriod($userId, $startDate, $endDate);

        return DataTables::of($tasksStats)
            ->make(true)
        ;
    }

    public function tasksCreatedAgainstCompleted(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $stats = $this->statsService->createdVsCompleted($userId, $startDate, $endDate);

        return response()->json($stats, 200);
    }

    public function notesStatsViews()
    {
        $userId = Auth::id();
        $projects = DB::table('projects')->select('id', 'name')
            ->where([
                'owner' => $userId,
            ])
            ->get()
        ;

        return view('stats.note')->with(['projects' => $projects]);
    }

    public function getNotesActivity()
    {
        $userId = Auth::id();
        $notesActivities = $this->statsService->getNotes($userId);

        return DataTables::of($notesActivities)->make(true);
    }

    public function getNoteActivityInPeriod(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $project = $request->project;

        if ($project) {
            $notesActivities = $this->statsService->getNotesInPeriodAndProject($userId, (int) $project, $startDate, $endDate);
        } else {
            $notesActivities = $this->statsService->getNotesInPeriod($userId, $startDate, $endDate);
        }

        return DataTables::of($notesActivities)
            ->make(true)
        ;
    }

    public function projectsStatsView()
    {
        return view('stats.project');
    }

    public function getProjectsActivity()
    {
        $userId = Auth::id();
        $projectsActivities = $this->statsService->getProjectActivity($userId);

        return DataTables::of($projectsActivities)->make(true);
    }

    public function getProjectsActivityInPeriod(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $projectsActivities = $this->statsService->getProjectActivityInPeriod($userId, $startDate, $endDate);

        return DataTables::of($projectsActivities)->make(true);
    }
}
