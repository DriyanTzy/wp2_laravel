<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('photo')->nullable();        // foto profil
            $table->text('bio')->nullable();            // bio profil
            $table->integer('points')->default(0);      // poin buat ambil dataset
            $table->timestamp('email_verified_at')->nullable();  // Tambah ini
            $table->rememberToken();                             // Tambah ini
    
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};