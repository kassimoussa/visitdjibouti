<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Création de la table principale des médias sans les champs traduisibles
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('path');
            $table->string('thumbnail_path')->nullable();
            $table->string('type'); // images, documents, videos, others
            // Les champs traduisibles sont retirés de cette table
            // $table->string('title')->nullable();
            // $table->string('alt_text')->nullable();
            // $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Création de la table de traductions pour les médias
        Schema::create('media_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // fr, en, ar, etc.
            
            // Champs traduisibles qui ont été retirés de la table principale
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['media_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // L'ordre est important pour respecter les contraintes de clé étrangère
        Schema::dropIfExists('media_translations');
        Schema::dropIfExists('media');
    }
};