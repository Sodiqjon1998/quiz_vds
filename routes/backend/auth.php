<?php

use App\Http\Controllers\Backend\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Backend\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('backend/login', [LoginController::class, 'create'])
                ->name('backend.login.con');

    Route::post('backend/login', [AuthenticatedSessionController::class, 'store'])->name('backend.login');
});

Route::middleware('auth')->group(function () {

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('backend/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('backend.logout');
});