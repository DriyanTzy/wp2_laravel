<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Survey yang diupload user
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('link');                     // link Google Form dll
            $table->boolean('is_active')->default(true);
            $table->integer('target_responses')->default(0);
            $table->timestamps();
        });

        // Tracking siapa aja yang isi survey
        Schema::create('responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // yang ngisi
            $table->timestamps();

            $table->unique(['survey_id', 'user_id']); // 1 user cuma bisa isi 1x
        });

        // Tracking siapa aja yang ambil dataset
        Schema::create('dataset_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['dataset_id', 'user_id']); // 1 user cuma bisa ambil 1x
        });

        // Post di profil user
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('survey_link')->nullable();  // link survey di post
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('dataset_access');
        Schema::dropIfExists('responses');
        Schema::dropIfExists('surveys');
    }
};