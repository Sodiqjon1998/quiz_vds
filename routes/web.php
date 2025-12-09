<?php

use App\Http\Controllers\WebSocket\ChatController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

// ✅ SANCTUM CSRF COOKIE ROUTE
Route::middleware('web')->group(function () {
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });
});


require __DIR__ . '/teacher/route.php';
require __DIR__ . '/teacher/auth.php';
require __DIR__ . '/koordinator/auth.php';
require __DIR__ . '/koordinator/route.php';
require __DIR__ . '/backend/auth.php';
require __DIR__ . '/backend/route.php';
require __DIR__ . '/frontend/route.php';
// require __DIR__.'/frontend/auth.php';
require __DIR__ . '/student/auth.php';
require __DIR__ . '/student/route.php';
