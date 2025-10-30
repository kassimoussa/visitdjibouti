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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->foreignId('created_by_operator_user_id')->nullable()->constrained('tour_operator_users')->onDelete('set null');
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->onDelete('set null');

            // Informations de base
            $table->string('slug')->unique();
            $table->enum('status', ['draft', 'active', 'inactive'])->default('active');
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('DJF');
            $table->integer('duration_hours')->nullable(); // Durée en heures
            $table->integer('duration_minutes')->nullable(); // Minutes supplémentaires
            $table->enum('difficulty_level', ['easy', 'moderate', 'difficult', 'expert'])->default('easy');

            // Participants
            $table->integer('min_participants')->default(1);
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);

            // Localisation
            $table->string('location_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('region')->nullable();

            // Restrictions et prérequis
            $table->boolean('has_age_restrictions')->default(false);
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->text('physical_requirements')->nullable(); // JSON: condition physique requise
            $table->text('certifications_required')->nullable(); // JSON: certifications nécessaires

            // Équipement
            $table->text('equipment_provided')->nullable(); // JSON: équipement fourni
            $table->text('equipment_required')->nullable(); // JSON: équipement à apporter

            // Autres informations
            $table->text('includes')->nullable(); // JSON: ce qui est inclus
            $table->boolean('weather_dependent')->default(false);
            $table->text('cancellation_policy')->nullable();
            $table->boolean('is_featured')->default(false);

            // Statistiques
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('registrations_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('tour_operator_id');
            $table->index('status');
            $table->index('difficulty_level');
            $table->index('region');
            $table->index(['latitude', 'longitude']);
        });

        Schema::create('activity_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->string('locale', 5); // fr, en, ar
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->text('description');
            $table->text('what_to_bring')->nullable(); // Quoi apporter
            $table->text('meeting_point_description')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();

            $table->unique(['activity_id', 'locale']);
            $table->index('locale');
        });

        Schema::create('activity_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->onDelete('set null');

            // Informations de contact (au cas où app_user_id est null)
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();

            // Détails de l'inscription
            $table->integer('number_of_people')->default(1);
            $table->date('preferred_date')->nullable(); // Date préférée suggérée par l'utilisateur
            $table->text('special_requirements')->nullable();
            $table->text('medical_conditions')->nullable();

            // Statuts
            $table->enum('status', [
                'pending',           // En attente de confirmation
                'confirmed',         // Confirmé par l'opérateur
                'completed',         // Activité réalisée
                'cancelled_by_user', // Annulé par l'utilisateur
                'cancelled_by_operator', // Annulé par l'opérateur
            ])->default('pending');

            // Paiement
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();

            // Dates importantes
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('activity_id');
            $table->index('app_user_id');
            $table->index('status');
            $table->index('payment_status');
        });

        // Pivot table pour les médias (galerie)
        Schema::create('activity_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['activity_id', 'media_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_media');
        Schema::dropIfExists('activity_registrations');
        Schema::dropIfExists('activity_translations');
        Schema::dropIfExists('activities');
    }
};
