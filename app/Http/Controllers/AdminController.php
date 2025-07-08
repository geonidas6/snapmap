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

        return response('Migration terminée');
)
  /**
     * Display the login form for admin users.
     */
    public function showLoginForm()
    {
        return view('admin.login');
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
}
