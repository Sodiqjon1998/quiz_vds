<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated users can listen to the channel.
|
*/


// ⚠️ Eslatma: Laravel token orqali tekshiradi (Auth::check() muhim!)
Broadcast::channel('presence-online', function ($user) {
    if ($user) {
        // Bu ma'lumotlar frontendga qaytadi va onlineUsers state'iga yoziladi
        return [
            'id' => $user->id, 
            'name' => $user->name, 
            'first_name' => $user->first_name, // ✅ QO'SHILDI
            'last_name' => $user->last_name,   // ✅ QO'SHILDI
            'avatar' => $user->avatar ?? null 
        ];
    }
});

// Shuningdek, Duelga chaqiruv kanali ham ruxsat berilishi kerak
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
