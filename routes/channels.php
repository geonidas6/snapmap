<?php

use Illuminate\Support\Facades\Broadcast;

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

// Presence channel for tracemap users
Broadcast::channel('tracemap-presence', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];});