<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GenerateReportsController extends Controller
{
    protected $statsService;
    protected $wkhtml = 'wkhtmltopdf';

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function generateSummaryReport(Request $request)
    {
        $period = $request->period;
        $user = Auth::user();
        $userId = $user->id;

        if ('lifetime' === $period) {
            $stats = $this->statsService->getLifetimeStats($userId);
            $header = 'LIFETIME STATISTICS';
        }

        if ('yearly' === $period) {
            $year = $request->year;

            $yearStart = Carbon::createFromDate($year, 1, 1)->startOfYear()->format('Y-m-d H:i:s');
            $yearEnd = Carbon::createFromDate($year, 1, 1)->endOfYear()->format('Y-m-d H:i:s');
            $stats = $this->statsService->getStatsInPeriod($userId, $yearStart, $yearEnd);
            $header = "{$year} IN NUMBERS";
        }

        if ('monthly' === $period) {
            $year = $request->monthYear;
            $month = $request->month;
            $monthName = $this->monthName($month);

            $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d H:i:s');
            $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d H:i:s');
            $stats = $this->statsService->getStatsInPeriod($userId, $monthStart, $monthEnd);
            $header = "{$monthName}, {$year} IN NUMBERS";
        }

        // Setup where the generated file will be stored
        $outputPath = $this->getOutputPath($user->name);

        // Setup report route
        $route = route('summary-report', [
            'header' => $header,
            'name' => $user->name,
            'stats' => $stats,
        ]);

        // Setup wkhtmltopdf process
        $process = new Process([$this->wkhtml, $route, $outputPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->download($outputPath);
    }

    public function generateTaskReport(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $startDate = $request->startDate;
        $endDate = $request->endDate;

        // Setup where the generated file will be stored
        $outputPath = $this->getOutputPath($user->name);

        // Setup report route
        $route = route('task-report', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userId' => $userId,
            'name' => $user->name,
        ]);

        $process = new Process([$this->wkhtml, $route, $outputPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->download($outputPath);
    }

    public function generateNoteReport(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $startDate = $request->startDate;
        $endDate = $request->endDate;
        $project = $request->project;

        // Setup where the generated file will be stored
        $outputPath = $this->getOutputPath($user->name);

        // Setup report route
        $route = route('note-report', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userId' => $userId,
            'name' => $user->name,
            'project' => $project,
        ]);

        // Setup wkhtmltopdf process
        $process = new Process([$this->wkhtml, $route, $outputPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->download($outputPath);
    }

    public function generateProjectReport(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $startDate = $request->startDate;
        $endDate = $request->endDate;

        // Setup where the generated file will be stored
        $outputPath = $this->getOutputPath($user->name);

        // Setup report route
        $route = route('project-report', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userId' => $userId,
            'name' => $user->name,
        ]);

        // Setup wkhtmltopdf process
        $process = new Process([$this->wkhtml, $route, $outputPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->download($outputPath);
    }

    public function generateReminderReport(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $startDate = $request->startDate;
        $endDate = $request->endDate;

        // Setup where the generated file will be stored
        $outputPath = $this->getOutputPath($user->name);

        // Setup report route
        $route = route('reminder-report', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'userId' => $userId,
            'name' => $user->name,
        ]);

        // Setup wkhtmltopdf process
        $process = new Process([$this->wkhtml, $route, $outputPath]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return response()->download($outputPath);
    }

    private function getOutputPath(string $username): string
    {
        $nameLowerCase = strtolower(explode(' ', $username)[0]);
        $filename = $nameLowerCase.strtotime(now()).'.pdf';

        return base_path('public/files/reports/').$filename;
    }

    private function monthName($month): string
    {
        if ($month > 12) {
            return 'Error';
        }

        $monthArray = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        return $monthArray[$month - 1];
    }
}
