<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal public pour les mises à jour de tracemap
Broadcast::channel('tracemap-updates', function () {
    return true; // Canal public, accessible à tous
});

// Presence channel for tracemap users with guest support
Broadcast::channel('tracemap-presence', function ($user) {
    if ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }

    $guest = session('guest_id');
    if (! $guest) {
        $guest = 'guest-' . Str::random(8);
        session(['guest_id' => $guest]);
    }

    return [        'id' => $guest,
        'name' => 'Guest',
    ];
});
