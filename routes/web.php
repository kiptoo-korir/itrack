<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenerateReportsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IssuesController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\RemindersController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth::routes();
Route::get('/landing-page', [HomeController::class, 'landingPage']);
Route::get('/login', [AuthController::class, 'login_view']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register_view']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/test', [TestingController::class, 'test']);
Route::get('/test_2', [TestingController::class, 'test_2']);
Route::get('/test_3', [TestingController::class, 'fake_data']);
Route::get('/test_notify', [TestingController::class, 'notify']);
Route::get('/sample_reminder_mail', [TestingController::class, 'showEmailView']);
Route::get('/github_callback', [ProfileController::class, 'callback']);

// Only Authorised and Verified users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile_pic', [ProfileController::class, 'profile_photo'])->name('profile_pic');
    Route::post('/change_pass', [ProfileController::class, 'change_password'])->name('change_pass');
    Route::post('/add_access', [ProfileController::class, 'add_token'])->name('add_pan');
    Route::get('/recent_activities', [ProfileController::class, 'recent_activity'])->name('recent_activity');
    Route::get('/token_list', [ProfileController::class, 'tokens_list'])->name('token_list');
    Route::post('/remove_token', [ProfileController::class, 'remove_token'])->name('delete_token');
    Route::get('/task', [TaskController::class, 'task_view'])->name('task_view');
    Route::get('/get_tasks', [TaskController::class, 'get_tasks'])->name('get_tasks');
    Route::post('/add_task', [TaskController::class, 'create_task'])->name('add_task');
    Route::post('/edit_task', [TaskController::class, 'edit_task'])->name('edit_task');
    Route::post('/remove_task', [TaskController::class, 'delete_task'])->name('delete_task');
    Route::get('/repositories', [RepositoryController::class, 'repositories_view'])->name('repositories');
    Route::get('/notes', [NotesController::class, 'notes_view'])->name('notes_view');
    Route::post('/add_note', [NotesController::class, 'create_note'])->name('add_note');
    Route::post('/edit_note', [NotesController::class, 'edit_note'])->name('edit_note');
    Route::get('/get_note', [NotesController::class, 'get_specific_note'])->name('get_note');
    Route::get('/get_all_notes', [NotesController::class, 'get_notes'])->name('get_all_notes');
    Route::post('/delete_note', [NotesController::class, 'delete_note'])->name('delete_note');
    Route::get('/reminders', [RemindersController::class, 'reminders_view'])->name('reminders_view');
    Route::post('/add_reminder', [RemindersController::class, 'create_reminder'])->name('add_reminder');
    Route::get('/get_all_reminders', [RemindersController::class, 'get_reminders'])->name('get_all_reminders');
    Route::get('/get_reminder', [RemindersController::class, 'get_specific_reminder'])->name('get_reminder');
    Route::post('/edit_reminder', [RemindersController::class, 'edit_reminder'])->name('edit_reminder');
    Route::post('/delete_reminder', [RemindersController::class, 'delete_reminder'])->name('delete_reminder');
    Route::get('/repository/{id}', [RepositoryController::class, 'specific_repository'])->name('view_specific_repository');
    Route::get('/get_projects', [ProjectsController::class, 'get_projects'])->name('get_projects');
    Route::post('/add_project', [ProjectsController::class, 'create_project'])->name('add_project');
    Route::get('/get_recent_notifications', [HomeController::class, 'get_top_three_notifications'])->name('fetch_notifications');
    Route::get('/repository/{id}/issues', [RepositoryController::class, 'fetch_issues_in_repository'])->name('fetch_issues_in_repo');
    Route::post('/add_new_issue', [IssuesController::class, 'createIssue'])->name('add_new_issue');
    Route::get('/project/{id}', [ProjectsController::class, 'specificProject'])->name('view_specific_project');
    Route::get('/mark_as_read/{id}', [HomeController::class, 'markNotificationAsRead'])->name('mark_as_read');
    Route::get('/project/{id}/notes', [NotesController::class, 'getNotesSpecificProject'])->name('get_notes_specific_project');
    Route::get('/project/{id}/repositories', [RepositoryController::class, 'getRepositoriesSpecificProject'])->name('get_repos_specific_project');
    Route::get('/project/{id}/linked_repositories', [ProjectsController::class, 'getLinkedRepositories'])->name('get_linked_repos_array');
    Route::get('/project/{id}/reminders', [ProjectsController::class, 'getLinkedReminders'])->name('get_linked_reminders');
    Route::post('/change_linked_repositories', [ProjectsController::class, 'changeLinkedRepositories'])->name('change_linked_repos');
    Route::post('/update-project', [ProjectsController::class, 'updateProject'])->name('update-project');
    Route::get('/edit-project/{id}', [ProjectsController::class, 'editProject'])->name('edit-project');
    Route::post('/delete-project', [ProjectsController::class, 'remove_project'])->name('delete-project');
    Route::get('/all-notifications', [NotificationsController::class, 'notificationsView'])->name('notifications-view');
    Route::get('/notifications/{page}', [NotificationsController::class, 'getAllNotifications'])->name('get-notifications');
    Route::get('/reports', [ReportsController::class, 'reportsView'])->name('reports');
    Route::get('/stats-period', [ReportsController::class, 'getStatsInPeriod'])->name('stats-in-period');
    Route::get('/generate-summary', [GenerateReportsController::class, 'generateSummaryReport'])->name('generate-summary-report');
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [AuthController::class, 'verification_view'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'handle_verification_request'])->name('verification.verify');
});

Route::post('/email/verification-notification', [AuthController::class, 'send_verification'])->middleware(['throttle:6,1', 'auth'])->name('verification.send');
Route::post('/email/resend-verification', [AuthController::class, 'send_verification'])->middleware(['throttle:6,1', 'auth'])->name('verification.resend');
