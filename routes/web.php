<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\API\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DatasetController;

Route::get('web', [Web::class, 'web']);
Route::get('web/about', [Web::class, 'about'])->name('web.about');

/*
|--------------------------------------------------------------------------
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

    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
});

/*
|--------------------------------------------------------------------------
| API routes (accessible via web session)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    Route::get('/dashboard', fn() => view('Dashboard.Index'))->name('dashboard');
    Route::get('/datasets',  fn() => view('Datasets.datasets-index'))->name('datasets.index');
    Route::get('/datasets/{id}', fn() => view('Datasets.Datasets-show'))->name('datasets.show');
    Route::get('/home', fn() => view('Home.Home-index'))->name('home');
    Route::get('/logout', fn() => view('Dashboard.Logout'))->name('logout.confirm');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/u/{username}', [ProfileController::class, 'showPublic'])->name('profile.public');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.settings');
    Route::put('/settings', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/api/dashboard', [DashboardController::class, 'index']);
    Route::get('/api/datasets', [DatasetController::class, 'index']);
    Route::get('/api/datasets/{id}', [DatasetController::class, 'show']);
});
