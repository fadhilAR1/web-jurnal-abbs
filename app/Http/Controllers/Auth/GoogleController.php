<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Validasi domain email
            $email = $googleUser->getEmail();
            $allowedDomains = ['smaabbs.sch.id', 'gmail.com']; // Tambah domain sekolah
            
            $domain = substr(strrchr($email, "@"), 1);
            
            if (!in_array($domain, $allowedDomains)) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Email harus menggunakan domain sekolah yang valid']);
            }
            
            // Cek apakah user sudah ada
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                // Register new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'guru', // Default role untuk pendaftar baru
                ]);
            } else {
                // Update google_id jika belum ada
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }
            
            Auth::login($user);
            
            // Redirect berdasarkan status
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isGuru()) {
                return redirect()->route('dashboard');
            } else {
                return redirect()->route('dashboard');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['error' => 'Autentikasi Google gagal: ' . $e->getMessage()]);
        }
    }
}