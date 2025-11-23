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
    
    // === User Profile (with class info) ===
    Route::get('/user/profile', [AuthController::class, 'profile'])->name('api.user.profile');

    // === Logout ===
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // === Quiz Management ===
    Route::prefix('quiz')->name('api.quiz.')->group(function () {
        Route::get('/', [SiteController::class, 'index'])->name('index');
        Route::get('/{id}/start', [SiteController::class, 'start'])->name('start');
        Route::post('/{id}/submit', [SiteController::class, 'submit'])->name('submit');
    });

    // === Reading/Kitobxonlik Management ===
    Route::prefix('readings')->name('api.readings.')->group(function () {
        /**
         * Get monthly readings
         * GET /api/readings?month=11&year=2024
         */
        Route::get('/', [ReadingController::class, 'index'])->name('index');

        /**
         * Upload audio file
         * POST /api/readings/upload
         * Body: FormData with 'audio' file
         */
        Route::post('/upload', [ReadingController::class, 'upload'])->name('upload');

        /**
         * Delete recording
         * DELETE /api/readings/{id}
         */
        Route::delete('/{id}', [ReadingController::class, 'delete'])->name('delete');
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
         * Body: { "date": "2024-11-22", "is_completed": true/false }
         */
        Route::post('/{taskId}/toggle', [TaskController::class, 'toggleTask'])->name('toggle');
        
        /**
         * Create new task (for teachers/admins)
         * POST /api/tasks
         * Body: { "name": "Task name", "emoji": "🎯", "description": "..." }
         */
        Route::post('/', [TaskController::class, 'createTask'])->name('create');
        
        /**
         * Delete task
         * DELETE /api/tasks/{taskId}
         */
        Route::delete('/{taskId}', [TaskController::class, 'deleteTask'])->name('delete');
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