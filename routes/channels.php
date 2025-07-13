<?php

use Illuminate\Support\Facades\Auth;
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

// Canal public pour les messages de chat
Broadcast::channel('chat-messages', function () {
    return true; // Canal public, accessible à tous
});

// Presence channel for tracemap users with guest support
Broadcast::channel('tracemap-presence', function ($user = null) {
    // Toujours autoriser l'accès au canal de présence
    
    // Si l'utilisateur est authentifié, retourner ses informations
    if (Auth::check()) {
        $authenticatedUser = Auth::user();
        return [
            'id' => $authenticatedUser->id,
            'name' => $authenticatedUser->name,
            'type' => 'user'
        ];
    }
   
    // Pour les utilisateurs invités, générer un ID unique basé sur la session
    $sessionId = session()->getId();
    $guestId = 'guest-' . substr(md5($sessionId), 0, 8);
    
    return [
        'id' => $guestId,
        'name' => 'Invité',
        'type' => 'guest'
    ];
});
