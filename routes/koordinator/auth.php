<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Koordinator\AuthenticatedSessionController;
use App\Http\Controllers\Koordinator\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //             ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('koordinator/login', [LoginController::class, 'create'])
                ->name('koordinator.login.con');

    Route::post('koordinator/login', [AuthenticatedSessionController::class, 'store'])->name('koordinator.login');
});

Route::middleware('auth')->group(function () {

    // Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('koordinator/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('koordinator.logout');
});
