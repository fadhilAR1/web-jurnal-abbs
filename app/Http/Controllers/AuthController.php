<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            // Redirect admin ke admin dashboard, guru ke guru dashboard
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        // Check password - admin uses bcrypt, guru uses plaintext
        if ($user) {
            $passwordMatch = false;
            
            if ($user->role === 'admin') {
                // Admin: use bcrypt verification
                $passwordMatch = password_verify($credentials['password'], $user->password);
            } else {
                // Guru: use plaintext comparison
                $passwordMatch = $user->password === $credentials['password'];
            }
            
            if ($passwordMatch) {
                Auth::login($user);
                $request->session()->regenerate();
                
                // Redirect ke admin dashboard jika user adalah admin
                if ($user->isAdmin()) {
                    return redirect()->intended(route('admin.dashboard'));
                }
                
                return redirect()->intended(route('dashboard'));
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
