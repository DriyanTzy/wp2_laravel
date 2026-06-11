<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web;
use App\Http\Controllers\AuthController;

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('web', [web::class, 'web']);
Route::get('web/about', [web::class, 'about'])->name('web.about');
