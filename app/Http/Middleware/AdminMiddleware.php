<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Middleware pour vérifier si l'utilisateur est un administrateur.
     * Redirige vers la page d'accueil si l'utilisateur n'est pas un administrateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté et est un administrateur
        if (Auth::check() && Auth::user()->is_admin) {
           
            // Rediriger vers la page d'accueil avec un message d'erreur
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'avez pas les droits d\'accès à cette page.');
        }

        return $next($request);
    }
}