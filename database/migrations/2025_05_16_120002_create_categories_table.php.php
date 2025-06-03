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
        // 1. Création de la table principale des catégories sans les champs traduisibles
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps(); 
        });

        // 2. Création de la table de traductions pour les catégories
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // fr, en, ar, etc.
            
            // Champs traduisibles qui ont été retirés de la table principale
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons de traduction pour une même catégorie et langue
            $table->unique(['category_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // L'ordre est important pour respecter les contraintes de clé étrangère
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('categories');
    }
};