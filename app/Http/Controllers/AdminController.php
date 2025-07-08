<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    /**
     * Reset the database and clear storage.
     */
    public function resetDatabase()
    {
        Artisan::call('optimize:clear');

        File::deleteDirectory(storage_path('app/public/tracemaps'));

        Artisan::call('storage:link');
        Artisan::call('migrate:fresh --seed');

        return response('Migration terminée');
    }
}
