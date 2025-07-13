<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BroadcastAuth
{
    /**
     * Handle an incoming request pour l'authentification des canaux de broadcasting.
     * Permet l'accès même aux utilisateurs non connectés.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Permettre l'accès aux canaux de broadcasting même pour les utilisateurs non connectés
        // Ceci est nécessaire pour les canaux de présence avec support des invités
        
        // Vérifier si c'est une requête d'authentification pour les canaux de broadcasting
        if ($request->is('broadcasting/auth')) {
            // Toujours autoriser l'accès aux canaux de broadcasting
            // même pour les utilisateurs non connectés
            return $next($request);
        }
        
        return $next($request);
    }
}
