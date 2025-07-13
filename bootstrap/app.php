<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enregistrer le middleware personnalisé pour l'authentification des canaux de broadcasting
        $middleware->alias([
            'broadcast.auth' => \App\Http\Middleware\BroadcastAuth::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        
        // Exclure les routes de broadcasting de la vérification CSRF
        $middleware->validateCsrfTokens(except: [
            'broadcasting/auth',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
