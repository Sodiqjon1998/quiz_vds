<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuizController;
use Illuminate\Support\Facades\Route;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Student\QuizAttemptController;

// Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
//     return $request->user();
// });

// // Ushbu qatorlardagi middleware('auth:sanctum') ni olib tashlang!
// Route::post('/quiz-attempt/save', [QuizAttemptController::class, 'saveAttempt']);
// Route::get('/quiz-attempt/load', [QuizAttemptController::class, 'loadAttempt']);

// // Yoki: ularni web middleware guruhiga qo'shing, shunda session auth ishlaydi

// Route::middleware('web')->group(function () {
//     Route::middleware('auth')->group(function () { // 'auth' default web guard uchun
//         Route::post('/quiz-attempt/save', [QuizAttemptController::class, 'saveAttempt']);
//         Route::get('/quiz-attempt/load', [QuizAttemptController::class, 'loadAttempt']);
//     });
// });

Route::post('/login', [AuthController::class, 'login'])->name('api.login');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // O'quvchi uchun route'lar
    // Route::post('/quiz-attempt/save', [QuizAttemptController::class, 'saveAttempt']);
    // Route::get('/quiz-attempt/load', [QuizAttemptController::class, 'loadAttempt']);

    // Quiz routes
    Route::get('/subjects/{subjectId}/quizzes/{quizId}', [QuizController::class, 'show']);
    Route::post('/subjects/{subjectId}/quizzes/{quizId}/submit', [QuizController::class, 'submitQuiz']);
});
