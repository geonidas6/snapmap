<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\TracemapController;

// Route principale qui redirige vers la page d'accueil des tracemaps
Route::get('/', function () {
    return redirect()->route('tracemap.index');
});

// Routes pour les tracemaps
Route::get('/tracemaps', [TracemapController::class, 'index'])->name('tracemap.index');
Route::post('/tracemaps', [TracemapController::class, 'store'])->name('tracemap.store');

// Route pour le téléversement AJAX
Route::post('/tracemaps/ajax', [TracemapController::class, 'storeAjax'])->name('tracemap.store.ajax');



//route pour faire artisan migrate
Route::get('/migrate', function () {
    Artisan::call('optimize:clear');
    //script pour supprimer les fichier du dossier storage/app/public/tracemaps
    File::deleteDirectory(storage_path('app/public/tracemaps'));

    Artisan::call('storage:link');
    Artisan::call('migrate:fresh --seed');

    return 'Migration terminée';
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);