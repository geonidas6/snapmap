<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    /**
     * Enable maintenance mode with a secret bypass link.
     */
    public function enableMaintenance()
    {
        Artisan::call('down', ['secret' => config('app.maintenance_secret')]);

        $url = url(config('app.maintenance_secret'));

        return back()->with('success', "Maintenance mode enabled. Access via {$url}");
    }

    /**
     * Disable maintenance mode.
     */
    public function disableMaintenance()
    {
        Artisan::call('up');

        return back()->with('success', 'Maintenance mode disabled.');
    }
}
