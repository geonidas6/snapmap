<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Utiliser une route personnalisée pour l'authentification des canaux de broadcasting
        // Les routes par défaut sont désactivées car nous avons créé notre propre route
        // Broadcast::routes(['middleware' => ['web']]);

        require base_path('routes/channels.php');
    }}