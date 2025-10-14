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
        // User Favorites
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');
            $table->unsignedBigInteger('favoritable_id');
            $table->string('favoritable_type');
            $table->timestamps();

            $table->unique(['app_user_id', 'favoritable_id', 'favoritable_type'], 'unique_user_favorite');
            $table->index(['app_user_id', 'favoritable_type']);
            $table->index(['favoritable_id', 'favoritable_type']);
        });

        // User Location History
        Schema::create('user_location_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');

            // GPS
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('speed', 6, 2)->nullable();
            $table->decimal('heading', 6, 2)->nullable();

            // Contexte
            $table->string('location_source', 30)->nullable();
            $table->string('activity_type', 50)->nullable();
            $table->integer('confidence_level')->nullable();

            // Géocodage
            $table->string('address', 500)->nullable();
            $table->string('street', 200)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('place_name', 200)->nullable();
            $table->string('place_category', 50)->nullable();

            // Métadonnées
            $table->string('timezone', 50)->nullable();
            $table->boolean('is_indoor')->default(false);
            $table->json('nearby_pois')->nullable();
            $table->string('weather_condition', 50)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();

            // Tracking
            $table->timestamp('recorded_at')->useCurrent();
            $table->string('session_id', 100)->nullable();
            $table->string('trigger', 50)->nullable();

            $table->timestamps();

            // Index
            $table->index('app_user_id');
            $table->index(['latitude', 'longitude']);
            $table->index('recorded_at');
            $table->index('city');
            $table->index('activity_type');
            $table->index(['app_user_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_location_history');
        Schema::dropIfExists('user_favorites');
    }
};
