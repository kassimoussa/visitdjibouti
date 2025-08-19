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
        // Table principale des opérateurs de tour (simplifiée)
        Schema::create('tour_operators', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            
            // Contact (champs multiples séparés par |)
            $table->text('phones')->nullable()->comment('Numéros de téléphone séparés par |');
            $table->text('emails')->nullable()->comment('Emails séparés par |');
            $table->string('website')->nullable();
            
            // Géolocalisation
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Médias et statut
            $table->unsignedBigInteger('logo_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false)->comment('Opérateur mis en avant');
            
            $table->timestamps();
            
            // Index pour la recherche géographique
            $table->index(['latitude', 'longitude']);
            $table->index(['is_active', 'featured']);
            
            // Clé étrangère pour le logo
            $table->foreign('logo_id')->references('id')->on('media')->onDelete('set null');
        });

        // Table des traductions (simplifiée)
        Schema::create('tour_operator_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_operator_id');
            $table->string('locale', 5); // fr, en, ar
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address_translated')->nullable()->comment('Adresse traduite dans la langue locale');
            $table->timestamps();
            
            $table->foreign('tour_operator_id')->references('id')->on('tour_operators')->onDelete('cascade');
            $table->unique(['tour_operator_id', 'locale']);
            $table->index(['locale', 'name']);
        });

        // Table pour les médias (galerie d'images)
        Schema::create('tour_operator_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_operator_id');
            $table->unsignedBigInteger('media_id');
            $table->integer('order')->default(0)->comment('Ordre d\'affichage');
            $table->timestamps();
            
            $table->foreign('tour_operator_id')->references('id')->on('tour_operators')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->unique(['tour_operator_id', 'media_id']);
            $table->index(['tour_operator_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_operator_media');
        Schema::dropIfExists('tour_operator_translations');
        Schema::dropIfExists('tour_operators');
    }
};