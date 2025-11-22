<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReadingController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ======================
// === PUBLIC ROUTES ===
// ======================

/**
 * Login endpoint
 * POST /api/login
 */
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// ======================
// === PROTECTED ROUTES (Auth Required) ===
// ======================

Route::middleware('auth:sanctum')->group(function () {

    // === User Info ===
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    })->name('api.user');

    // === Logout ===
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // === Quiz Management ===
    Route::prefix('quiz')->name('api.quiz.')->group(function () {

        /**
         * Get all available quizzes for current user
         * GET /api/quiz
         */
        Route::get('/', [SiteController::class, 'index'])->name('index');

        /**
         * Start a specific quiz
         * GET /api/quiz/{id}/start
         */
        Route::get('/{id}/start', [SiteController::class, 'start'])->name('start');

        /**
         * Submit quiz answers
         * POST /api/quiz/{id}/submit
         */
        Route::post('/{id}/submit', [SiteController::class, 'submit'])->name('submit');
    });


   Route::prefix('readings')->group(function () {
        Route::get('/', [ReadingController::class, 'index']);
        Route::post('/upload', [ReadingController::class, 'upload']);
        Route::delete('/{id}', [ReadingController::class, 'delete']);
    });


    // === Daily Tasks Management ===
    Route::prefix('tasks')->name('api.tasks.')->group(function () {

        /**
         * Get tasks for a specific date
         * GET /api/tasks?date=2024-11-22
         */
        Route::get('/', [TaskController::class, 'getTasks'])->name('get');

        /**
         * Toggle task completion status
         * POST /api/tasks/{taskId}/toggle
         * Body: { "date": "2024-11-22" }
         */
        Route::post('/{taskId}/toggle', [TaskController::class, 'toggleTask'])->name('toggle');
    });

    // === Statistics ===
    Route::prefix('stats')->name('api.stats.')->group(function () {

        /**
         * Get monthly statistics
         * GET /api/stats/monthly?year=2024&month=11
         */
        Route::get('/monthly', [TaskController::class, 'monthlyStats'])->name('monthly');
    });
});
