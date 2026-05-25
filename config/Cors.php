<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    | Ini ngizinin FE (HTML/JS) bisa hit API Laravel dari domain/port beda
    | Misal FE jalan di localhost:5500, backend di localhost:8000
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'], // GET, POST, PUT, DELETE, dll

    'allowed_origins' => [
        'http://localhost',
        'http://localhost:3000',    // FE port 3000
        'http://localhost:5500',    // FE Live Server VS Code
        'http://127.0.0.1:5500',
        // tambah URL FE di sini kalau udah deploy
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // wajib true untuk Sanctum

];