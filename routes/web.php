<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\TracemapController;
use App\Http\Controllers\AdminController;

// Route principale qui redirige vers la page d'accueil des tracemaps
Route::get('/', function () {
    return redirect()->route('tracemap.index');
});

// Routes pour les tracemaps
Route::get('/tracemaps', [TracemapController::class, 'index'])->name('tracemap.index');
Route::post('/tracemaps', [TracemapController::class, 'store'])->name('tracemap.store');

// Route pour le téléversement AJAX
Route::post('/tracemaps/ajax', [TracemapController::class, 'storeAjax'])->name('tracemap.store.ajax');


Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Maintenance mode routes
Route::middleware('auth')->group(function () {
    Route::post('/maintenance/on', [AdminController::class, 'enableMaintenance'])->name('admin.maintenance.on');
    Route::post('/maintenance/off', [AdminController::class, 'disableMaintenance'])->name('admin.maintenance.off');
});




if (App::environment('local')) {
    Route::middleware('auth')->group(function () {
        Route::get('/queue', function () {
            Artisan::call('queue:work');

            return 'queue';
        });
    });
}

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');
    Route::post('/reset', [AdminController::class, 'resetDatabase'])->name('admin.reset');
});
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
