<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── Login ────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Support login pakai email atau username
        $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil!');
        }

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors(['username' => 'Username atau password salah.']);
    }

    // ── Register ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Pakai RegisterRequest — validasi sudah include:
     *   name, username (unique, alpha_dash), email (unique), password (confirmed, min8, letters+numbers)
     * Jika gagal → RegisterRequest lempar JSON 422 (untuk API) atau redirect back (untuk form biasa).
     *
     * Catatan: RegisterRequest::failedValidation() throw HttpResponseException (JSON).
     * Untuk form Blade biasa ini tidak ideal — tapi kita override supaya tetap bisa redirect.
     */
    public function register(RegisterRequest $request)
    {
        // Data sudah tervalidasi oleh RegisterRequest
        $validated = $request->validated();

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],   // ← dari input user langsung
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'points'   => 0,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat. Selamat datang!');
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
