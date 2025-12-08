<?php

use Illuminate\Support\Facades\Broadcast;

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

Broadcast::channel('App.Models.Teacher.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ✅ YANGI: Buni qo'shing (Duel o'yini uchun)
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


// Ulanishga ruxsat berish va onlayn foydalanuvchi ma'lumotlarini qaytarish
Broadcast::channel('presence-online', function ($user) {
    if ($user) {
        // Frontedga yuboriladigan ma'lumotlar
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'avatar' => $user->avatar ?? null // Agar avatarlar bo'lsa
        ];
    }
});
