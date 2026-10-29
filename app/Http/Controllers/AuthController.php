<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir autentikasi login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login pengguna (Mahasiswa atau Admin).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'NIM atau Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $identifier = trim($credentials['identifier']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Cek login via email atau identifier (NIM)
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $attemptField = $isEmail ? 'email' : 'identifier';

        if (Auth::attempt([$attemptField => $identifier, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang di Portal Administrator Kampus, '.$user->name.'!');
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        return back()->withErrors([
            'identifier' => 'Identitas (NIM/Email) atau kata sandi yang Anda masukkan tidak sesuai.',
        ])->onlyInput('identifier');
    }

    /**
     * Proses keluar (logout) dari sesi aplikasi.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem SuaraKampus.');
    }
}
