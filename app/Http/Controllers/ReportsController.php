<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function reportsView()
    {
        $userId = Auth::id();

        $stats = $this->statsService->getLifetimeStats($userId);

        return view('reports-home')->with(['stats' => $stats]);
    }

    public function getStatsInPeriod(Request $request)
    {
        $period = $request->period;
        $userId = Auth::id();

        if ('lifetime' === $period) {
            $stats = $this->statsService->getLifetimeStats($userId);
        }

        if ('yearly' === $period) {
            $year = $request->year;

            $yearStart = Carbon::createFromDate($year, 1, 1)->startOfYear()->format('Y-m-d H:i:s');
            $yearEnd = Carbon::createFromDate($year, 1, 1)->endOfYear()->format('Y-m-d H:i:s');
            $stats = $this->statsService->getStatsInPeriod($userId, $yearStart, $yearEnd);
        }

        if ('monthly' === $period) {
            $year = $request->monthYear;
            $month = $request->month;

            $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d H:i:s');
            $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d H:i:s');
            $stats = $this->statsService->getStatsInPeriod($userId, $monthStart, $monthEnd);
        }

        return response()->json(['stats' => $stats], 200);
    }

    public function summaryStatsReport(Request $request)
    {
        $stats = $request->get('stats');
        $name = $request->get('name');
        $header = $request->get('header');

        $data = [
            'stats' => $stats,
            'name' => $name,
            'header' => $header,
        ];

        return view('reports.summary-report')->with($data);
    }

    public function taskStatsReport(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $userId = $request->get('userId');
        $name = $request->get('name');

        $tasksStats = $this->statsService->getTasksStatsInPeriod($userId, $startDate, $endDate);
        $statsSummary = $this->statsService->createdVsCompleted($userId, $startDate, $endDate);

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'stats' => $tasksStats,
            'statsSummary' => $statsSummary,
            'name' => $name,
        ];

        // dd($data);

        return view('reports.task-report')->with($data);
    }

    public function noteActivityReport(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $userId = $request->get('userId');
        $name = $request->get('name');
        $projectId = $request->get('project');

        $project = DB::table('projects')->select('name')
            ->where('id', $projectId)
            ->first()
        ;

        if ($projectId && $startDate && $endDate) {
            $noteActivity = $this->statsService->getNotesInPeriodAndProject($userId, $projectId, $startDate, $endDate);
        } elseif ($startDate && !$projectId) {
            $noteActivity = $this->statsService->getNotesInPeriod($userId, $startDate, $endDate);
        } else {
            $noteActivity = $this->statsService->getNotes($userId);
        }

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'activity' => $noteActivity,
            'name' => $name,
            'projectInfo' => $project,
        ];

        return view('reports.note-report')->with($data);
    }

    public function projectActivityReport(Request $request)
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        $userId = $request->get('userId');
        $name = $request->get('name');

        if (!$startDate) {
            $projectsActivities = $this->statsService->getProjectActivity($userId);
        } else {
            $projectsActivities = $this->statsService->getProjectActivityInPeriod($userId, $startDate, $endDate);
        }

        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'activity' => $projectsActivities,
            'name' => $name,
        ];

        return view('reports.project-report')->with($data);
    }
}
