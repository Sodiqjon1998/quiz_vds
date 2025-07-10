<?php

use App\Http\Controllers\Teacher\AuthenticatedSessionController;
use App\Http\Controllers\Teacher\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('teacher/login', [LoginController::class, 'create'])
        ->name('teacher.login.con');

    Route::post('teacher/login', [AuthenticatedSessionController::class, 'store'])->name('teacher.login');
});

Route::middleware('auth')->group(function () {

    Route::post('teacher/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('teacher.logout');
});
