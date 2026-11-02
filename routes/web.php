<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ReplacementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\MoldController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoldExportController;
use App\Http\Controllers\HistoryExportController;
use App\Http\Controllers\AtasanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('landingpage.index');
})->name('landing');

// Alias supaya /landingpage tidak 404
Route::redirect('/landingpage', '/');

// Laravel mengarahkan user yang sudah login ke /home
Route::get('/home', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/daftar', [RegisterController::class, 'show'])->name('register');
    Route::post('/daftar', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/lupa-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('molds/export', MoldExportController::class)->name('molds.export');

    Route::resource('molds', MoldController::class)->except('show');

    Route::get('molds/{mold}/penggantian/baru', [ReplacementController::class, 'create'])->name('replacements.create');
    Route::post('molds/{mold}/penggantian', [ReplacementController::class, 'store'])->name('replacements.store');

    Route::get('/riwayat/export', HistoryExportController::class)->name('history.export');
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history.index');

    Route::get('/persetujuan', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/persetujuan/{replacement}/setujui', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/persetujuan/{replacement}/tolak', [ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('/reminder', [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/reminder/kirim', [ReminderController::class, 'send'])->name('reminders.send');

    Route::resource('atasan', AtasanController::class)
        ->except('show')
        ->parameters(['atasan' => 'user']);
});