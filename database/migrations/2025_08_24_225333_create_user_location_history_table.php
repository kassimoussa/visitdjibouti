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
        Schema::create('user_location_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('app_user_id')->constrained('app_users')->onDelete('cascade');
            
            // Coordonnées GPS
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable(); // Précision en mètres
            $table->decimal('altitude', 8, 2)->nullable(); // Altitude en mètres
            $table->decimal('speed', 6, 2)->nullable(); // Vitesse en m/s
            $table->decimal('heading', 6, 2)->nullable(); // Direction 0-360°
            
            // Informations contextuelles
            $table->string('location_source', 30)->nullable(); // 'gps', 'network', 'passive'
            $table->string('activity_type', 50)->nullable(); // 'walking', 'driving', 'stationary'
            $table->integer('confidence_level')->nullable(); // 0-100% confiance dans l'activité
            
            // Géocodage inverse
            $table->string('address', 500)->nullable(); // Adresse complète
            $table->string('street', 200)->nullable(); // Rue
            $table->string('city', 100)->nullable(); // Ville
            $table->string('region', 100)->nullable(); // Région/État
            $table->string('country', 100)->nullable(); // Pays
            $table->string('postal_code', 20)->nullable(); // Code postal
            $table->string('place_name', 200)->nullable(); // Nom du lieu (restaurant, hôtel, etc.)
            $table->string('place_category', 50)->nullable(); // Catégorie du lieu
            
            // Métadonnées
            $table->string('timezone', 50)->nullable(); // Fuseau horaire local
            $table->boolean('is_indoor')->default(false); // Localisation intérieure
            $table->json('nearby_pois')->nullable(); // POIs à proximité lors de la capture
            $table->string('weather_condition', 50)->nullable(); // Conditions météo
            $table->decimal('temperature', 5, 2)->nullable(); // Température en °C
            
            // Tracking
            $table->timestamp('recorded_at'); // Timestamp de l'enregistrement
            $table->string('session_id', 100)->nullable(); // ID de session
            $table->string('trigger', 50)->nullable(); // Ce qui a déclenché l'enregistrement
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('app_user_id');
            $table->index(['latitude', 'longitude']);
            $table->index('recorded_at');
            $table->index('city');
            $table->index('activity_type');
            $table->index(['app_user_id', 'recorded_at']);
            
            // Index géospatial pour les requêtes de proximité (un seul champ à la fois)
            // Note: MySQL spatial index nécessite une colonne spatiale dédiée
            // $table->spatialIndex('latitude'); // Commenté car nécessite POINT/GEOMETRY
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_location_history');
    }
};
