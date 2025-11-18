<?php

use App\Http\Controllers\WebSocket\ChatController;
use Illuminate\Support\Facades\Route;



// Chat sahifasi
Route::get('/chat', [ChatController::class, 'index'])->name('chat');

// Xabar yuborish
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

// Xonaga kirish (ixtiyoriy)
Route::post('/chat/join', [ChatController::class, 'joinRoom'])->name('chat.join');


require __DIR__ . '/teacher/route.php';
require __DIR__ . '/teacher/auth.php';
require __DIR__ . '/backend/auth.php';
require __DIR__ . '/backend/route.php';
require __DIR__ . '/frontend/route.php';
// require __DIR__.'/frontend/auth.php';
require __DIR__ . '/student/auth.php';
require __DIR__ . '/student/route.php';
