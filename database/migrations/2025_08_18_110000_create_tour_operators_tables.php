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
        // Table principale des opérateurs de tour
        Schema::create('tour_operators', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('license_number')->nullable()->comment('Numéro de licence touristique');
            $table->enum('certification_type', ['local', 'national', 'international'])->default('local');
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('fax')->nullable();
            $table->text('languages_spoken')->nullable()->comment('Langues parlées séparées par |');
            
            // Géolocalisation
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Médias et statut
            $table->unsignedBigInteger('logo_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false)->comment('Opérateur mis en avant');
            
            // Tarification
            $table->decimal('min_price', 8, 2)->nullable();
            $table->decimal('max_price', 8, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('price_range', ['budget', 'mid-range', 'luxury', 'premium'])->nullable();
            
            // Évaluation
            $table->decimal('rating', 3, 2)->default(0)->comment('Note moyenne sur 5');
            $table->integer('reviews_count')->default(0);
            
            // Informations métier
            $table->text('opening_hours')->nullable();
            $table->integer('years_experience')->nullable();
            $table->integer('max_group_size')->nullable();
            $table->boolean('emergency_contact_available')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Index et contraintes
            $table->foreign('logo_id')->references('id')->on('media')->onDelete('set null');
            $table->index(['is_active', 'featured']);
            $table->index(['rating', 'reviews_count']);
            $table->index(['min_price', 'max_price']);
            $table->index(['latitude', 'longitude']);
        });

        // Table des traductions
        Schema::create('tour_operator_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_operator_id');
            $table->string('locale', 5);
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address_translated')->nullable();
            $table->text('services')->nullable()->comment('Services proposés');
            $table->text('specialties')->nullable()->comment('Spécialités');
            $table->text('about_text')->nullable()->comment('À propos de l\'opérateur');
            $table->text('booking_conditions')->nullable()->comment('Conditions de réservation');
            
            $table->timestamps();
            
            // Contraintes
            $table->foreign('tour_operator_id')->references('id')->on('tour_operators')->onDelete('cascade');
            $table->unique(['tour_operator_id', 'locale']);
            $table->index(['locale']);
        });

        // Table des services/catégories d'opérateurs
        Schema::create('tour_operator_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_operator_id');
            $table->string('service_type');
            $table->boolean('is_primary')->default(false)->comment('Service principal');
            $table->timestamps();
            
            $table->foreign('tour_operator_id')->references('id')->on('tour_operators')->onDelete('cascade');
            $table->unique(['tour_operator_id', 'service_type']);
        });

        // Table pivot pour les médias (photos des bureaux, équipes, etc.)
        Schema::create('tour_operator_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tour_operator_id');
            $table->unsignedBigInteger('media_id');
            $table->enum('media_type', ['gallery', 'office', 'team', 'equipment', 'certificate'])->default('gallery');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('tour_operator_id')->references('id')->on('tour_operators')->onDelete('cascade');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->unique(['tour_operator_id', 'media_id']);
            $table->index(['tour_operator_id', 'media_type', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_operator_media');
        Schema::dropIfExists('tour_operator_services');
        Schema::dropIfExists('tour_operator_translations');
        Schema::dropIfExists('tour_operators');
    }
};