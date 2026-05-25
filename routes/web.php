<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web;

Route::get('web', [web::class, 'web']);
Route::get('web/about', [web::class, 'about'])->name('web.about');