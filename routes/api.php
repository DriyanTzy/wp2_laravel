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
    Route::post('/datasets',                    [DatasetController::class, 'store']);
    Route::put('/datasets/{dataset}',             [DatasetController::class, 'update']);
    Route::post('/datasets/{dataset}/access',   [DatasetController::class, 'access']);
    Route::delete('/datasets/{dataset}',        [DatasetController::class, 'destroy']);

    // Surveys
    Route::post('/surveys',                     [SurveyController::class, 'store']);
    Route::put('/surveys/{survey}',             [SurveyController::class, 'update']);
    Route::delete('/surveys/{survey}',          [SurveyController::class, 'destroy']);
    Route::post('/surveys/{survey}/respond',    [SurveyController::class, 'respond']);

    // Posts (di profil)
    Route::post('/posts',           [PostController::class, 'store']);
    Route::put('/posts/{post}',     [PostController::class, 'update']);
    Route::delete('/posts/{post}',  [PostController::class, 'destroy']);
});