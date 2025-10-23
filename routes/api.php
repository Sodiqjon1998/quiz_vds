<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\QuizAttemptController;

Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    return $request->user();
});

// Ushbu qatorlardagi middleware('auth:sanctum') ni olib tashlang!
Route::post('/quiz-attempt/save', [QuizAttemptController::class, 'saveAttempt']);
Route::get('/quiz-attempt/load', [QuizAttemptController::class, 'loadAttempt']);

// Yoki: ularni web middleware guruhiga qo'shing, shunda session auth ishlaydi

Route::middleware('web')->group(function () {
    Route::middleware('auth')->group(function () { // 'auth' default web guard uchun
        Route::post('/quiz-attempt/save', [QuizAttemptController::class, 'saveAttempt']);
        Route::get('/quiz-attempt/load', [QuizAttemptController::class, 'loadAttempt']);
    });
});
