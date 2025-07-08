<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\TracemapController;
use App\Http\Controllers\AdminController;



//Route::get('/', function () {
//    return view('welcome');
//});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware([ 'verified'])->name('dashboard');

    // Routes pour le mode maintenance
    Route::post('/maintenance/on', [AdminController::class, 'enableMaintenance'])->name('admin.maintenance.on');
    Route::post('/maintenance/off', [AdminController::class, 'disableMaintenance'])->name('admin.maintenance.off');

    // Route pour réinitialiser la base de données
    Route::post('/admin/reset', [AdminController::class, 'resetDatabase'])->name('admin.reset');
});

// Route pour le mode maintenance
Route::get('/maintenance/{secret}', [AdminController::class, 'maintenance'])->name('admin.maintenance');

require __DIR__.'/auth.php';



// Route principale qui redirige vers la page d'accueil des tracemaps
//Route::get('/', function () {
//    return redirect()->route('tracemap.index');
//});

// Routes pour les tracemaps
Route::get('/', [TracemapController::class, 'index'])->name('tracemap.index');
Route::post('/tracemaps', [TracemapController::class, 'store'])->name('tracemap.store');

// Route pour le téléversement AJAX
Route::post('/tracemaps/ajax', [TracemapController::class, 'storeAjax'])->name('tracemap.store.ajax');




