<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // User admin
        User::create([
            'name' => 'Driyan90',
            'username' => 'driyan90',
            'email' => 'driyanHytam@email.com',
            'password' => Hash::make('password'),
            'bio' => 'Data enthusiast',
            'points' => 100,
            'photo' => null,
        ]);

        // User lain contoh
        User::create([
            'name' => 'Rizal Akbar',
            'username' => 'rizalbar',
            'email' => 'rizalbar@gmail.com',
            'password' => Hash::make('password'),
            'bio' => 'Peneliti independen yang berfokus pada analisis perilaku pengguna dan survei sosial.',
            'points' => 50,
        ]);

        User::create([
            'name' => 'Imam Mahdi',
            'username' => 'imammahdi',
            'email' => 'coex21@gmail.com',
            'password' => Hash::make('password'),
            'bio' => 'Data scientist',
            'points' => 75,
        ]);

        User::create([
            'name' => 'Anet Kosasih',
            'username' => 'anetk',
            'email' => 'anetkasi@gmail.com',
            'password' => Hash::make('password'),
            'bio' => 'Analyst',
            'points' => 30,
        ]);
    }
}