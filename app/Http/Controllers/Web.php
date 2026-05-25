<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class web extends Controller
{
    public function index()
    {
        return view('v-index');
    }
    public function about()
    {
        return view('v-about');
    }
}
