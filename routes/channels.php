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



Broadcast::channel('presence-online', function ($user) {
    if ($user) {
        return [
            'id' => (string) $user->id,
            'info' => [
                'name' => trim($user->first_name . ' ' . $user->last_name),
                'first_name' => $user->first_name,
                // ✅ Avatar olib tashlandi
            ]
        ];
    }
    return false;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    \Log::info('Broadcasting Auth', [
        'user_id' => $user->id,
        'requested_userId' => $userId,
        'match' => (int) $user->id === (int) $userId
    ]);
    
    return (int) $user->id === (int) $userId;
});