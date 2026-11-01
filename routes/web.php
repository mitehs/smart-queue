<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\Staff;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

/* ------------------------------------------------------------------ */
/*  PUBLIC                                                             */
/* ------------------------------------------------------------------ */
Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/help', [UserDashboardController::class, 'help'])->name('help');
Route::get('/contact', [UserDashboardController::class, 'contact'])->name('contact');
Route::post('/contact', [UserDashboardController::class, 'sendContact'])->name('contact.send');

/* ------------------------------------------------------------------ */
/*  ADMIN                                                              */
/* ------------------------------------------------------------------ */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [Admin\DashboardController::class, 'analytics'])->name('analytics');

    // Places
    Route::get('/places', [Admin\PlaceController::class, 'index'])->name('places.index');
    Route::post('/places', [Admin\PlaceController::class, 'store'])->name('places.store');
    Route::put('/places/{place}', [Admin\PlaceController::class, 'update'])->name('places.update');
    Route::delete('/places/{place}', [Admin\PlaceController::class, 'destroy'])->name('places.destroy');

    // Services
    Route::get('/services', [Admin\ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [Admin\ServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{service}', [Admin\ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [Admin\ServiceController::class, 'destroy'])->name('services.destroy');

    // Counters
    Route::get('/counters', [Admin\CounterController::class, 'index'])->name('counters.index');
    Route::post('/counters', [Admin\CounterController::class, 'store'])->name('counters.store');
    Route::put('/counters/{counter}', [Admin\CounterController::class, 'update'])->name('counters.update');
    Route::delete('/counters/{counter}', [Admin\CounterController::class, 'destroy'])->name('counters.destroy');

    // Staff
    Route::get('/staff', [Admin\StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [Admin\StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [Admin\StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/assignments', [Admin\StaffController::class, 'assignments'])->name('staff.assignments');
    Route::get('/staff/{staff}/edit', [Admin\StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [Admin\StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [Admin\StaffController::class, 'destroy'])->name('staff.destroy');

    // Users
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Tokens
    Route::get('/tokens', [Admin\TokenController::class, 'index'])->name('tokens.index');
    Route::get('/tokens/{token}', [Admin\TokenController::class, 'show'])->name('tokens.show');
    Route::patch('/tokens/{token}', [Admin\TokenController::class, 'update'])->name('tokens.update');
    Route::delete('/tokens/{token}', [Admin\TokenController::class, 'destroy'])->name('tokens.destroy');

    // Reports
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/analytics', [Admin\ReportController::class, 'analytics'])->name('reports.analytics');
    Route::get('/reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [Admin\SettingController::class, 'editGeneral'])->name('settings.general');
    Route::post('/settings', [Admin\SettingController::class, 'updateGeneral'])->name('settings.general.update');
    Route::get('/settings/business', [Admin\SettingController::class, 'edit'])->name('settings.business');
    Route::post('/settings/business', [Admin\SettingController::class, 'update'])->name('settings.business.update');
});

/* ------------------------------------------------------------------ */
/*  STAFF / SERVICE                                                    */
/* ------------------------------------------------------------------ */
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/', [Staff\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/queue', [Staff\QueueController::class, 'index'])->name('queue.index');
    Route::get('/queue/today', [Staff\QueueController::class, 'today'])->name('queue.today');
    Route::get('/queue/upcoming', [Staff\QueueController::class, 'upcoming'])->name('queue.upcoming');
    Route::get('/queue/history', [Staff\QueueController::class, 'history'])->name('queue.history');

    Route::get('/active', [Staff\TokenController::class, 'active'])->name('tokens.active');
    Route::get('/active/status', [Staff\TokenController::class, 'status'])->name('tokens.status');
    Route::post('/active/call-next', [Staff\TokenController::class, 'callNext'])->name('tokens.call-next');
    Route::post('/active/complete', [Staff\TokenController::class, 'complete'])->name('tokens.complete');
    Route::post('/active/skip', [Staff\TokenController::class, 'skip'])->name('tokens.skip');
    Route::post('/active/no-show', [Staff\TokenController::class, 'noShow'])->name('tokens.no-show');
    Route::post('/tokens/{token}/recall', [Staff\TokenController::class, 'recall'])->name('tokens.recall');
    Route::get('/tokens/{token}', [Staff\TokenController::class, 'show'])->name('tokens.show');
});

/* ------------------------------------------------------------------ */
/*  USER / CUSTOMER                                                    */
/* ------------------------------------------------------------------ */
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/history', [UserDashboardController::class, 'history'])->name('user.history');
    Route::get('/notifications', [UserDashboardController::class, 'notifications'])->name('user.notifications');
    Route::get('/contribution', [UserDashboardController::class, 'contribution'])->name('user.contribution');

    // Booking flow: place -> service -> counter -> token
    Route::get('/queue/place', [QueueController::class, 'place'])->name('queue.place');
    Route::get('/queue/service', [QueueController::class, 'service'])->name('queue.service');
    Route::get('/queue/counter', [QueueController::class, 'counter'])->name('queue.counter');
    Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');

    Route::get('/queue/active', [QueueController::class, 'active'])->name('queue.active');
    Route::get('/queue/status', [QueueController::class, 'status'])->name('queue.status');
    Route::get('/queue/success/{token}', [QueueController::class, 'success'])->name('queue.success');
    Route::get('/queue/progress/{token}', [QueueController::class, 'progress'])->name('queue.progress');
    Route::get('/queue/ticket/{token}', [QueueController::class, 'ticket'])->name('queue.ticket');
    Route::get('/queue/cancel/{token}', [QueueController::class, 'cancel'])->name('queue.cancel');

    Route::get('/queue/update', [QueueController::class, 'editUpdate'])->name('queue.update');
    Route::patch('/queue/update', [QueueController::class, 'update'])->name('queue.update.save');
});

/* ------------------------------------------------------------------ */
/*  PROFILE (all roles)                                                */
/* ------------------------------------------------------------------ */
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
