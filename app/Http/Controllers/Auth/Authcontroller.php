<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ────────────────────────────────────────────────
    //  Login
    // ────────────────────────────────────────────────

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

        // Attempt login with either username or email
        $field = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login berhasil!');
        }

        return back()
            ->withInput($request->only('username', 'remember'))
            ->withErrors(['username' => 'Username atau password salah.']);
    }

    // ────────────────────────────────────────────────
    //  Register
    // ────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
<<<<<<< HEAD
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::min(8)],
        ]);
=======
{
    $data = $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', Password::min(8)],
    ]);
>>>>>>> 96b14f0ffde98cbe672a27f5c5518971fbe8edb0

    $baseUsername = strtolower(str_replace(' ', '', $data['name']));
    $username     = $baseUsername;
    $counter      = 1;
    while (User::where('username', $username)->exists()) {
        $username = $baseUsername . $counter++;
    }

    $user = User::create([
        'name'     => $data['name'],
        'username' => $username,
        'email'    => $data['email'],
        'password' => Hash::make($data['password']),
    ]);

    Auth::login($user);

    // Ubah di sini
    return redirect()->route('login')
        ->with('success', 'Akun berhasil dibuat. Silakan login.');
}

    // ────────────────────────────────────────────────
    //  Logout
    // ────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
