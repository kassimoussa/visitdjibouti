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
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Relation avec tour operator
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');

            // Type et cible du tour
            $table->string('type'); // poi, event, mixed, cultural, adventure, nature, gastronomic
            $table->morphs('target'); // target_id et target_type (POI ou Event)

            // Dates
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Durée
            $table->integer('duration_hours')->nullable();
            $table->integer('duration_days')->nullable();

            // Participants
            $table->integer('max_participants')->nullable();
            $table->integer('min_participants')->default(1);

            // Prix
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('DJF');

            // Difficulté
            $table->enum('difficulty_level', ['easy', 'moderate', 'difficult', 'expert'])->default('easy');

            // Inclusions et prérequis
            $table->json('includes')->nullable(); // transport, guide, repas, etc.
            $table->json('requirements')->nullable(); // âge, condition physique, équipement

            // Point de rendez-vous
            $table->decimal('meeting_point_latitude', 10, 8)->nullable();
            $table->decimal('meeting_point_longitude', 11, 8)->nullable();
            $table->string('meeting_point_address')->nullable();

            // Statut et options
            $table->enum('status', ['active', 'suspended', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_pattern')->nullable(); // pour les tours récurrents
            $table->boolean('weather_dependent')->default(false);

            // Restrictions d'âge
            $table->integer('age_restriction_min')->nullable();
            $table->integer('age_restriction_max')->nullable();

            // Politique d'annulation
            $table->text('cancellation_policy')->nullable();

            // Image mise en avant
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();

            // Statistiques
            $table->integer('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Index pour performance
            $table->index(['tour_operator_id']);
            $table->index(['type']);
            $table->index(['status']);
            $table->index(['is_featured']);
            $table->index(['difficulty_level']);
            $table->index(['price']);
            $table->index(['meeting_point_latitude', 'meeting_point_longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};