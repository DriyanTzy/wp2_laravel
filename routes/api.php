<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\API\DatasetController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SurveyController;
use Illuminate\Support\Facades\Route;

// =====================================================
// PUBLIC — tidak butuh login
// =====================================================

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Profil publik (orang lain bisa lihat)
Route::get('/profile/{username}', [ProfileController::class, 'show']);

// Browse dataset & survey
Route::get('/datasets',     [DatasetController::class, 'index']);
Route::get('/datasets/{dataset}', [DatasetController::class, 'show']);
Route::get('/surveys',      [SurveyController::class, 'index']);

// =====================================================
// PROTECTED — butuh login (token di header)
// =====================================================
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile sendiri
    Route::get('/profile',              [ProfileController::class, 'me']);
    Route::put('/profile',              [ProfileController::class, 'update']);
    Route::put('/profile/password',     [ProfileController::class, 'updatePassword']);

    // Datasets
    Route::get('/datasets', [DatasetController::class, 'index']);
    Route::get('/datasets/{id}', [DatasetController::class, 'show']);
    Route::post('/datasets', [DatasetController::class, 'store']);
    Route::put('/datasets/{id}', [DatasetController::class, 'update']);
    Route::post('/datasets/{id}/access', [DatasetController::class, 'access']);
    Route::delete('/datasets/{id}', [DatasetController::class, 'destroy']);

    // Surveys
    Route::post('/surveys',                     [SurveyController::class, 'store']);
    Route::put('/surveys/{survey}',             [SurveyController::class, 'update']);
    Route::delete('/surveys/{survey}',          [SurveyController::class, 'destroy']);
    Route::post('/surveys/{survey}/respond',    [SurveyController::class, 'respond']);

    // Posts (di profil)
    Route::post('/posts',           [PostController::class, 'store']);
    Route::put('/posts/{post}',     [PostController::class, 'update']);
    Route::delete('/posts/{post}',  [PostController::class, 'destroy']);

    // Search user
    Route::get('/users/search', [ProfileController::class, 'search']);
    Route::get('/profile/{username}', [ProfileController::class, 'show']);

    // Dataset dengan filter user_id (sudah ada, pastikan method index menerima Request)
    Route::get('/datasets', [DatasetController::class, 'index']);

    // Search datasets
    Route::get('/datasets/search', [DatasetController::class, 'search']);

    // Feed (semua post terbaru)
    Route::get('/posts/feed', [PostController::class, 'feed']);
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike']);

});