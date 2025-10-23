<?php

use App\Http\Controllers\Student\AuthenticatedSessionController;
use App\Http\Controllers\Student\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    Route::get('student/login', [LoginController::class, 'create'])
        ->name('student.login.con');

    Route::post('student/login', [AuthenticatedSessionController::class, 'store'])->name('student.login');
});

Route::middleware('auth')->group(function () {

    Route::post('student/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('student.logout');
});
