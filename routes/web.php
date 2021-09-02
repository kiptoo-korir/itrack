<?php

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
Route::get('/login', [App\Http\Controllers\AuthController::class, 'login_view']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'register_view']);
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::get('/test', [App\Http\Controllers\TestingController::class, 'test']);
Route::get('/test_2', [App\Http\Controllers\TestingController::class, 'test_2']);
Route::get('/test_3', [App\Http\Controllers\TestingController::class, 'fake_data']);
Route::get('/test_notify', [App\Http\Controllers\TestingController::class, 'notify']);
Route::get('/github_callback', [App\Http\Controllers\ProfileController::class, 'callback']);

// Only Authorised and Verified users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile_pic', [App\Http\Controllers\ProfileController::class, 'profile_photo'])->name('profile_pic');
    Route::post('/change_pass', [App\Http\Controllers\ProfileController::class, 'change_password'])->name('change_pass');
    Route::post('/add_access', [App\Http\Controllers\ProfileController::class, 'add_token'])->name('add_pan');
    Route::get('/recent_activities', [App\Http\Controllers\ProfileController::class, 'recent_activity'])->name('recent_activity');
    Route::get('/token_list', [App\Http\Controllers\ProfileController::class, 'tokens_list'])->name('token_list');
    Route::post('/remove_token', [App\Http\Controllers\ProfileController::class, 'remove_token'])->name('delete_token');
    Route::get('/task', [App\Http\Controllers\TaskController::class, 'task_view'])->name('task_view');
    Route::get('/get_tasks', [App\Http\Controllers\TaskController::class, 'get_tasks'])->name('get_tasks');
    Route::post('/add_task', [App\Http\Controllers\TaskController::class, 'create_task'])->name('add_task');
    Route::post('/edit_task', [App\Http\Controllers\TaskController::class, 'edit_task'])->name('edit_task');
    Route::post('/remove_task', [App\Http\Controllers\TaskController::class, 'delete_task'])->name('delete_task');
    Route::get('/repositories', [App\Http\Controllers\RepositoryController::class, 'repositories_view'])->name('repositories');
    Route::get('/notes', [App\Http\Controllers\NotesController::class, 'notes_view'])->name('notes_view');
    Route::post('/add_note', [App\Http\Controllers\NotesController::class, 'create_note'])->name('add_note');
    Route::post('/edit_note', [App\Http\Controllers\NotesController::class, 'edit_note'])->name('edit_note');
    Route::get('/get_note', [App\Http\Controllers\NotesController::class, 'get_specific_note'])->name('get_note');
    Route::get('/get_all_notes', [App\Http\Controllers\NotesController::class, 'get_notes'])->name('get_all_notes');
    Route::post('/delete_note', [App\Http\Controllers\NotesController::class, 'delete_note'])->name('delete_note');
    Route::get('/reminders', [App\Http\Controllers\RemindersController::class, 'reminders_view'])->name('reminders_view');
    Route::post('/add_reminder', [App\Http\Controllers\RemindersController::class, 'create_reminder'])->name('add_reminder');
    Route::get('/get_all_reminders', [App\Http\Controllers\RemindersController::class, 'get_reminders'])->name('get_all_reminders');
    Route::get('/get_reminder', [App\Http\Controllers\RemindersController::class, 'get_specific_reminder'])->name('get_reminder');
    Route::post('/edit_reminder', [App\Http\Controllers\RemindersController::class, 'edit_reminder'])->name('edit_reminder');
    Route::post('/delete_reminder', [App\Http\Controllers\RemindersController::class, 'delete_reminder'])->name('delete_reminder');
    Route::get('/repository/{id}', [App\Http\Controllers\RepositoryController::class, 'specific_repository'])->name('view_specific_repository');
    Route::get('/get_projects', [App\Http\Controllers\ProjectsController::class, 'get_projects'])->name('get_projects');
    Route::post('/add_project', [App\Http\Controllers\ProjectsController::class, 'create_project'])->name('add_project');
    Route::get('/get_recent_notifications', [App\Http\Controllers\HomeController::class, 'get_top_three_notifications'])->name('fetch_notifications');
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\AuthController::class, 'verification_view'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\AuthController::class, 'handle_verification_request'])->name('verification.verify');
});

Route::post('/email/verification-notification', [App\Http\Controllers\AuthController::class, 'send_verification'])->middleware(['throttle:6,1', 'auth'])->name('verification.send');
Route::post('/email/resend-verification', [App\Http\Controllers\AuthController::class, 'send_verification'])->middleware(['throttle:6,1', 'auth'])->name('verification.resend');
