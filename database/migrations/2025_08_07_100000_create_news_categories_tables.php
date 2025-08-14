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
        // 1. Création de la table principale des catégories d'actualités
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom principal (pour simplicité Livewire)
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Description principale (pour simplicité Livewire)
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#3498db'); // Hex color
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key pour la hiérarchie
            $table->foreign('parent_id')->references('id')->on('news_categories')->onDelete('cascade');
            $table->index(['parent_id', 'sort_order']);
        });

        // 2. Création de la table de traductions pour les catégories d'actualités
        Schema::create('news_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // fr, en, ar, etc.
            
            // Champs traduisibles
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
            
            // Contrainte d'unicité pour éviter les doublons
            $table->unique(['news_category_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_category_translations');
        Schema::dropIfExists('news_categories');
    }
};