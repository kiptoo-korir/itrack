<?php

use App\Http\Controllers\ReportsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/summary-stats', [ReportsController::class, 'summaryStatsReport'])->name('summary-report');
Route::get('/task-report', [ReportsController::class, 'taskStatsReport'])->name('task-report');
Route::get('/note-report', [ReportsController::class, 'noteActivityReport'])->name('note-report');
Route::get('/project-report', [ReportsController::class, 'projectActivityReport'])->name('project-report');
Route::get('/reminder-report', [ReportsController::class, 'reminderActivityReport'])->name('reminder-report');
