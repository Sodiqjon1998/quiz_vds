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
