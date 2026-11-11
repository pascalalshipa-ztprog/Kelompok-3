<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ComplaintReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/forgot-password', [PasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
});

use App\Http\Controllers\Api\ComplaintController as ApiComplaintController;

Route::middleware('auth')->group(function () {
    Route::put('/profile/password', [PasswordController::class, 'updateStudentPassword'])->name('profile.password.update');
    Route::get('/user/dashboard/password', [PasswordController::class, 'editStudentPassword'])->name('dashboard.password.edit');

    Route::get('/user', DashboardController::class)
        ->middleware('can:user-area')
        ->name('dashboard');

    Route::prefix('user')->middleware('can:user-area')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/profile/photo', [ProfileController::class, 'photo'])->name('profile.photo');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/dashboard/password', [PasswordController::class, 'updateStudentPassword'])->name('dashboard.password.update');
        Route::resource('complaints', ComplaintController::class);
    });

    Route::prefix('admin')->name('admin.')->middleware('can:admin-area')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/profile/photo', [ProfileController::class, 'photo'])->name('profile.photo');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/complaints/export', [ComplaintReportController::class, 'export'])->name('complaints.export');
        Route::get('/complaints/export/pdf', [ComplaintReportController::class, 'exportPdf'])->name('complaints.export-pdf');
        Route::patch('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('complaints.update-status');
        Route::resource('complaints', ComplaintController::class)->except(['create', 'store']);

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/complaints/export', [ComplaintReportController::class, 'export'])
        ->name('legacy.complaints.export');
    Route::patch('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])
        ->name('legacy.complaints.update-status');
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('legacy.complaints.index');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('legacy.complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('legacy.complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('legacy.complaints.show');
    Route::get('/complaints/{complaint}/edit', [ComplaintController::class, 'edit'])->name('legacy.complaints.edit');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('legacy.complaints.update');
    Route::patch('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('legacy.complaints.patch');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('legacy.complaints.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('legacy.users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('legacy.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('legacy.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('legacy.users.destroy');

    Route::get('/api/complaints', [ApiComplaintController::class, 'index'])->name('api.complaints.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
