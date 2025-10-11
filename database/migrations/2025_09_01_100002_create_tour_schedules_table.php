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
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');

            // Dates et horaires
            $table->date('start_date');
            $table->date('end_date')->nullable(); // pour tours multi-jours
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Gestion des places
            $table->integer('available_spots');
            $table->integer('booked_spots')->default(0);

            // Statut du créneau
            $table->enum('status', ['available', 'full', 'cancelled', 'completed'])->default('available');

            // Guide
            $table->string('guide_name')->nullable();
            $table->string('guide_contact')->nullable(); // téléphone ou email
            $table->json('guide_languages')->nullable(); // langues parlées par le guide

            // Notes spéciales pour ce créneau
            $table->text('special_notes')->nullable();

            // Statut météo (pour tours weather_dependent)
            $table->enum('weather_status', ['unknown', 'favorable', 'unfavorable', 'cancelled_weather'])->default('unknown');

            // Surcharges possibles
            $table->text('meeting_point_override')->nullable(); // point de RDV différent
            $table->decimal('price_override', 10, 2)->nullable(); // prix spécial pour ce créneau

            // Délai d'annulation
            $table->timestamp('cancellation_deadline')->nullable();

            // Qui a créé ce créneau
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admin_users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Index pour performance
            $table->index(['tour_id']);
            $table->index(['start_date']);
            $table->index(['status']);
            $table->index(['start_date', 'status']);
            $table->index(['guide_name']);
            $table->index(['available_spots', 'booked_spots']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_schedules');
    }
};