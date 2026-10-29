<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const LOGIN_MAX_ATTEMPTS = 3;

    private const LOGIN_LOCKOUT_SECONDS = 120;

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9.-]+$/', 'unique:users,nim'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create($validated);

        return redirect()->route('login')->with('status', 'Akun berhasil dibuat. Silakan masuk ke portal.');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
        $identity = trim($validated['identity']);
        $identityField = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'nim';
        $userId = User::query()->where($identityField, $identity)->value('id');
        $throttleIdentity = $userId === null ? $identity : 'user:'.$userId;
        $throttleKey = $this->loginThrottleKey($throttleIdentity, $request);
        $lockoutKey = $throttleKey.':lockout';

        if (RateLimiter::tooManyAttempts($lockoutKey, 1)) {
            $retryAfter = RateLimiter::availableIn($lockoutKey);

            return back()->withErrors([
                'identity' => $this->loginLockoutMessage($retryAfter),
            ])->onlyInput('identity');
        }

        if (! Auth::attempt([
            $identityField => $identity,
            'password' => $validated['password'],
        ], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, self::LOGIN_LOCKOUT_SECONDS);

            if (RateLimiter::tooManyAttempts($throttleKey, self::LOGIN_MAX_ATTEMPTS)) {
                RateLimiter::clear($throttleKey);
                RateLimiter::hit($lockoutKey, self::LOGIN_LOCKOUT_SECONDS);
                $retryAfter = RateLimiter::availableIn($lockoutKey);

                return back()->withErrors([
                    'identity' => $this->loginLockoutMessage($retryAfter),
                ])->onlyInput('identity');
            }

            return back()->withErrors([
                'identity' => 'NIM/email atau kata sandi tidak sesuai.',
            ])->onlyInput('identity');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    private function loginThrottleKey(string $identity, Request $request): string
    {
        return 'login:'.Str::transliterate(Str::lower($identity).'|'.$request->ip());
    }

    private function loginLockoutMessage(int $retryAfter): string
    {
        $minutes = max(1, (int) ceil($retryAfter / 60));

        return "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$minutes} menit.";
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
