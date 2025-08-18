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
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');
            $table->unsignedBigInteger('favoritable_id');
            $table->string('favoritable_type'); // Poi::class, Event::class, etc.
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['app_user_id', 'favoritable_type']);
            $table->index(['favoritable_id', 'favoritable_type']);
            
            // Contrainte unique pour éviter les doublons
            $table->unique(['app_user_id', 'favoritable_id', 'favoritable_type'], 'unique_user_favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};