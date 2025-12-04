<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReadingController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\QuizController; // <-- Qo'shing
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ======================
// === PUBLIC ROUTES ===
// ======================

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// ======================
// === PROTECTED ROUTES ===
// ======================

Route::middleware('auth:sanctum')->group(function () {

    // === User Info ===
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    })->name('api.user');

    Route::get('/user/profile', [AuthController::class, 'profile'])->name('api.user.profile');
    Route::post('/user/profile/update', [AuthController::class, 'updateProfile'])->name('api.user.profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // === Quiz Management ===
    Route::prefix('quiz')->name('api.quiz.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');

        // 1️⃣ BIRINCHI - Aniq route (avval tekshiriladi)
        Route::get('/{subjectId}/{quizId}', [QuizController::class, 'show'])
            ->where(['subjectId' => '[0-9]+', 'quizId' => '[0-9]+'])
            ->name('show');

        // 2️⃣ IKKINCHI - Umimiy route (keyin tekshiriladi)  
        Route::get('/{id}/start', [SiteController::class, 'start'])->name('start');

        Route::post('/{subjectId}/{quizId}/submit', [QuizController::class, 'submitQuiz'])->name('submit.quiz');
    });

    // === Reading Management ===
    Route::prefix('readings')->name('api.readings.')->group(function () {
        Route::get('/', [ReadingController::class, 'index'])->name('index');
        Route::post('/upload', [ReadingController::class, 'upload'])->name('upload');
        Route::delete('/{id}', [ReadingController::class, 'delete'])->name('delete');
    });

    // === Tasks Management ===
    Route::prefix('tasks')->name('api.tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'getTasks'])->name('get');
        Route::post('/{taskId}/toggle', [TaskController::class, 'toggleTask'])->name('toggle');
        Route::post('/', [TaskController::class, 'createTask'])->name('create');
        Route::delete('/{taskId}', [TaskController::class, 'deleteTask'])->name('delete');
    });

    // === Statistics ===
    Route::prefix('stats')->name('api.stats.')->group(function () {
        Route::get('/monthly', [TaskController::class, 'monthlyStats'])->name('monthly');
    });
});
