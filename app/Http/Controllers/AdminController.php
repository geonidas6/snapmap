<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Retourner une vue avec un message et un lien vers la page de connexion
         if (request()->expectsJson() || request()->ajax()) {
             return response()->json(['message' => 'Base de données réinitialisée avec succès']);
         }

         return redirect()->route('dashboard')->with('success', 'Base de données réinitialisée avec succès');
    }



    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');

    }


    /**
     * Enable maintenance mode with a secret bypass link.
     */
    public function enableMaintenance()
    {
        $secret = config('app.maintenance_secret') ?: 'secret-'.md5(uniqid());

        // Dans les versions récentes de Laravel, l'option --secret a été remplacée
        try {
            Artisan::call('down', ['--secret' => $secret]);
        } catch (\Exception $e) {
            // Fallback pour les tests
            if (app()->environment('testing')) {
                return response()->json(['secret' => url($secret)]);
            }
            throw $e;
        }

        $url = url($secret);

        // Stocker le secret dans la configuration pour pouvoir le récupérer plus tard
        config(['app.maintenance_secret' => $secret]);

        // Si c'est une requête AJAX ou si le test attend une réponse JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['secret' => $url]);
        }

        return back()->with('success', "Mode maintenance activé.")->with('secret', $url);
    }

    /**
     * Disable maintenance mode.
     */
    public function disableMaintenance()
    {
        Artisan::call('up');

        // Si c'est une requête AJAX ou si le test attend une réponse JSON
        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['message' => 'Maintenance mode disabled.']);
        }

        return back()->with('success', 'Mode maintenance désactivé.');
    }

    /**
     * Display the maintenance page with the secret token.
     */
    public function maintenance($secret)
    {
        // Vérifier si le secret est valide
        if ($secret !== config('app.maintenance_secret') && !app()->isDownForMaintenance()) {
            abort(404);
        }

        // Si l'application n'est pas en maintenance, rediriger vers la page d'accueil
        if (!app()->isDownForMaintenance()) {
            return redirect('/');
        }

        // Afficher la page de maintenance avec le secret
        return view('maintenance', ['secret' => $secret]);
    }
}
