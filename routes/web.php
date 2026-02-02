<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DataSurveyController;
use App\Http\Controllers\Admin\SystemTestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. HALAMAN DEPAN
Route::get('/', function () {
    return view('welcome');
});

// 2. GROUP ROUTE SURVEY (Pengunjung)
Route::prefix('survey')->name('survey.')->group(function () {
    Route::get('/start', [SurveyController::class, 'start'])->name('start');
    Route::post('/process', [SurveyController::class, 'process'])->name('process');
    Route::get('/questions', [SurveyController::class, 'questions'])->name('questions');
    Route::post('/store', [SurveyController::class, 'store'])->name('store');
    Route::get('/result', [SurveyController::class, 'result'])->name('result');
});

// 3. AUTHENTICATION (LOGIN MANUAL)
// Route ini terbuka untuk umum agar admin bisa login
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// 4. ROUTE ADMIN (DIPROTEKSI MIDDLEWARE)
// Semua route di dalam grup ini WAJIB login dulu baru bisa akses
Route::middleware(['auth'])->group(function () {

    // Dashboard Utama
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Halaman Statistik
    Route::get('/admin/statistics', [DashboardController::class, 'statistics'])->name('statistics');
    Route::get('/admin/data-survey', [DataSurveyController::class, 'index'])->name('data-survey');
    Route::get('/admin/uji-sistem', [SystemTestController::class, 'index'])->name('admin.uji-sistem');
    Route::post('/admin/uji-sistem/process', [SystemTestController::class, 'process'])->name('admin.uji-sistem.test');
    // Route untuk Halaman Riwayat
    Route::get('/uji-sistem/riwayat', [SystemTestController::class, 'riwayat'])
        ->name('admin.uji-sistem.history');
});
