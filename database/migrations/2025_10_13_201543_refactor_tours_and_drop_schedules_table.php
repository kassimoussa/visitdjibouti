<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crée la table 'tours' avec le schéma final souhaité
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->string('type');
            $table->morphs('target');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('DJF');
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->enum('difficulty_level', ['easy', 'moderate', 'difficult', 'expert'])->default('easy');
            $table->json('includes')->nullable();
            $table->json('requirements')->nullable();
            $table->decimal('meeting_point_latitude', 10, 8)->nullable();
            $table->decimal('meeting_point_longitude', 11, 8)->nullable();
            $table->string('meeting_point_address')->nullable();
            $table->enum('status', ['active', 'suspended', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('weather_dependent')->default(false);
            $table->text('cancellation_policy')->nullable();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Crée la table pour les traductions des tours
        Schema::create('tour_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->text('itinerary')->nullable();
            $table->text('meeting_point_description')->nullable();
            $table->json('highlights')->nullable();
            $table->json('what_to_bring')->nullable();
            $table->text('cancellation_policy_text')->nullable();
            $table->timestamps();
            $table->unique(['tour_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Inverser les opérations dans l'ordre inverse

        // Recréer la colonne 'max_participants_new' pour la renommer
        Schema::table('tours', function (Blueprint $table) {
            $table->renameColumn('max_participants', 'max_participants_new');
        });

        // Rajouter les anciennes colonnes à 'tours'
        Schema::table('tours', function (Blueprint $table) {
            $table->integer('duration_hours')->nullable();
            $table->integer('duration_days')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('min_participants')->default(1);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_pattern')->nullable();
            $table->integer('age_restriction_min')->nullable();
            $table->integer('age_restriction_max')->nullable();
        });

        // Recréer la table 'tour_schedules'
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('available_spots');
            $table->integer('booked_spots')->default(0);
            $table->enum('status', ['available', 'full', 'cancelled', 'completed'])->default('available');
            $table->string('guide_name')->nullable();
            $table->string('guide_contact')->nullable();
            $table->json('guide_languages')->nullable();
            $table->text('special_notes')->nullable();
            $table->enum('weather_status', ['unknown', 'favorable', 'unfavorable', 'cancelled_weather'])->default('unknown');
            $table->text('meeting_point_override')->nullable();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->timestamp('cancellation_deadline')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Supprimer les nouvelles colonnes de 'tours'
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'end_date',
                'start_time',
                'end_time',
                'max_participants_new',
                'current_participants'
            ]);
        });
    }
};