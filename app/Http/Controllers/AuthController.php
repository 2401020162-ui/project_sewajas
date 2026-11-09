<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function form(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->arahkanBerdasarkanRole(Auth::user());
        }

        return view('auth');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->string('name')->trim()->toString(),
            'email' => Str::lower($request->string('email')->trim()->toString()),
            'nomor_hp' => $request->string('nomor_hp')->trim()->toString(),
            'password' => $request->string('password')->toString(),
            'role' => Role::Customer,
        ]);

        return redirect()->route('login')
            ->with('sukses', 'Registrasi berhasil. Silakan masuk dengan akun baru kamu.');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $key = Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan masuk. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return $this->arahkanBerdasarkanRole($request->user());
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('sukses', 'Kamu berhasil keluar.');
    }

    private function arahkanBerdasarkanRole(User $user): RedirectResponse
    {
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('beranda');
    }
}
