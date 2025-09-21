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
        Schema::create('tour_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->string('locale', 2); // fr, en, ar

            // Contenu traduit
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->text('itinerary')->nullable(); // programme détaillé
            $table->text('meeting_point_description')->nullable();

            // Points forts et recommandations
            $table->json('highlights')->nullable(); // points forts du tour
            $table->json('what_to_bring')->nullable(); // que apporter

            // Politique d'annulation traduite
            $table->text('cancellation_policy_text')->nullable();

            $table->timestamps();

            // Contraintes d'unicité
            $table->unique(['tour_id', 'locale']);

            // Index
            $table->index(['tour_id']);
            $table->index(['locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_translations');
    }
};