<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('reports.summary-report');
    }
}
