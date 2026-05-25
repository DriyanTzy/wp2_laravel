<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('class');                    // misal "X PPLG 1"
            $table->string('thumbnail')->nullable();    // path gambar thumbnail
            $table->text('description')->nullable();
            $table->string('file_path');                // path file dataset
            $table->integer('points_required')->default(5); // poin yang dibutuhkan
            $table->integer('present_count')->default(0);   // berapa kali diambil
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};