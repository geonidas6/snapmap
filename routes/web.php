<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\TracemapController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;


//Route::get('/', function () {
//    return view('welcome');
//});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route personnalisée pour l'authentification des canaux de broadcasting
Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    // Récupérer le nom du canal depuis la requête
    $channelName = $request->input('channel_name');

    if ($channelName === 'presence-tracemap-presence') {
        // Toujours autoriser l'accès au canal de présence
        if (Auth::check()) {
            $user = Auth::user();
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'type' => 'user'
            ];
        } else {
            // Pour les utilisateurs invités
            $sessionId = session()->getId();
            $guestId = 'guest-' . substr(md5($sessionId), 0, 8);
            $userData = [
                'id' => $guestId,
                'name' => 'Invité',
                'type' => 'guest'
            ];
        }

        // Générer la signature d'authentification pour Pusher
        $pusher = new \Pusher\Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );

        $socketId = $request->input('socket_id');
        $auth = $pusher->authorizePresenceChannel($channelName, $socketId, $userData['id'], $userData);

        return $auth;

    }

    return response()->json(['error' => 'Unauthorized'], 403);
})->name('broadcasting.auth');

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Routes pour le mode maintenance
    Route::post('/maintenance/on', [AdminController::class, 'enableMaintenance'])->name('admin.maintenance.on');
    Route::post('/maintenance/off', [AdminController::class, 'disableMaintenance'])->name('admin.maintenance.off');

    // Route pour réinitialiser la base de données
    Route::post('/admin/reset', [AdminController::class, 'resetDatabase'])->name('admin.reset');

    // Routes pour la gestion des tracemaps
    Route::get('/admin/tracemaps', [AdminController::class, 'tracemaps'])->name('admin.tracemaps');
    Route::get('/admin/tracemaps/{tracemap}/edit', [AdminController::class, 'editTracemap'])->name('admin.tracemaps.edit');
    Route::put('/admin/tracemaps/{tracemap}', [AdminController::class, 'updateTracemap'])->name('admin.tracemaps.update');
    Route::post('/admin/tracemaps/delete', [AdminController::class, 'deleteTracemaps'])->name('admin.tracemaps.delete');

    // Routes pour la gestion des messages
    Route::get('/admin/messages', [AdminController::class, 'messages'])->name('admin.messages');
    Route::post('/admin/messages/delete', [AdminController::class, 'deleteMessages'])->name('admin.messages.delete');
    Route::post('/admin/messages/delete-all', [AdminController::class, 'deleteAllMessages'])->name('admin.messages.delete.all');
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

// Routes pour la gestion des messages du chat
Route::get('/messages', [TracemapController::class, 'getMessages'])->name('messages.get');
Route::post('/messages', [TracemapController::class, 'storeMessage'])->name('messages.store');




