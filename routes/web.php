<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use app\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


/*
| Guest routes (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/google',          [GoogleAuthController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
});

/*
|--------------------------------------------------------------------------
| Authenticated Blade routes
| Data diambil dari API (/api/*) via fetch() di dalam blade masing-masing.
| Controller di sini hanya return view(), tanpa query DB.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // ── Blade pages (shell only, data dari API) ───────────────────────
    Route::get('/dashboard', fn() => view('dashboard.index'))->name('dashboard');
    Route::get('/home', fn() => view('home.index'))->name('home');
    Route::get('/datasets',  fn() => view('datasets.index'))->name('datasets.index');
    Route::get('/profile',   fn() => view('profile.show'))->name('profile.show');
    Route::get('/datasets',  fn() => view('datasets.index'))->name('datasets.index');
    Route::get('/datasets/{id}', fn() => view('datasets.show'))->name('datasets.show');
    Route::get('/logout',    fn() => view('dashboard.logout'))->name('logout.confirm');

    // ── Web logout (hapus session & cookie) ──────────────────────────
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Public profile (bisa untuk diri sendiri atau user lain)
    Route::get('/u/{username}', [ProfileController::class, 'showPublic'])->name('profile.public');

    // Settings (edit profil) – ganti dari sebelumnya yang mungkin pakai 'profile.show'
    Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.settings');
    Route::put('/settings', [ProfileController::class, 'update'])->name('profile.update');

    // Home
    Route::get('/home', fn() => view('home.index'))->name('home');

});