<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaduanController;
use Illuminate\Support\Facades\Route;

// Rute Autentikasi Login & Logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Portal Mahasiswa
Route::get('/', [PengaduanController::class, 'index'])->name('dashboard');
Route::get('/pengaduan/buat', [PengaduanController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan/buat', [PengaduanController::class, 'store'])->name('pengaduan.store');
Route::get('/pengaduan/riwayat', [PengaduanController::class, 'history'])->name('pengaduan.riwayat');

// Rute Portal Khusus Administrator
Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/pengaduan/status', [AdminController::class, 'updateStatus'])->name('status.update');
});
