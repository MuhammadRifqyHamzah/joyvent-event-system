<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show Login Page
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('admin.auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | Login Process
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        // Validasi
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cek login
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Verifikasi role admin
            if (!$user || $user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akses ditolak. Hanya admin yang diizinkan masuk.'
                ])->onlyInput('email');
            }

            // Regenerate session
            $request->session()->regenerate();

            // Redirect dashboard
            return redirect('/admin/dashboard')
                ->with('success', 'Login berhasil');
        }

        // Jika gagal
        return back()->withErrors([
            'email' => 'Email atau password salah'
        ])->onlyInput('email');
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}