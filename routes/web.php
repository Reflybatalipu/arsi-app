<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TugasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('welcome');
});

// Guest routes.
Route::middleware('guest')->group(function () {
    Route::get('/registrasi', [AuthController::class, 'create'])->name('register');
    Route::post('/registrasi', [AuthController::class, 'store'])->name('register.store');

    Route::get('/masuk', [AuthController::class, 'login'])->name('login');
    Route::post('/masuk', [AuthController::class, 'authenticate'])
        ->middleware('throttle:login')
        ->name('login.attempt');
});

// Authenticated routes.
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('tugas', TugasController::class)->parameters(['tugas' => 'tugas']);
    Route::post('/tugas/{tugas}/selesai', [TugasController::class, 'tandaiSelesai'])->name('tugas.selesai');

    Route::resource('agenda', AgendaController::class);
});
