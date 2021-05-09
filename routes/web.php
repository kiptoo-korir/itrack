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
});

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\AuthController::class, 'verification_view'])->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\AuthController::class, 'handle_verification_request'])->name('verification.verify');
});

Route::post('/email/verification-notification', [App\Http\Controllers\AuthController::class, 'send_verification'])->middleware(['throttle:6,1', 'auth'])->name('verification.send');
Route::post('/email/resend-verification', [App\Http\Controllers\AuthController::class, 'send_verification'])->middleware(['throttle:6,1', 'auth'])->name('verification.resend');
