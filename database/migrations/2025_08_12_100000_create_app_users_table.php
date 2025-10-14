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
        Schema::create('app_users', function (Blueprint $table) {
            $table->id();
            
            // Informations de base
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            // Colonnes pour l'authentification sociale
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            // Colonnes de profil supplémentaires
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('preferred_language', 2)->default('fr');
            $table->string('city')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('push_notifications_enabled')->default(true);
            $table->boolean('email_notifications_enabled')->default(true);

            // Dernières colonnes de la factory
            $table->string('country')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();

            $table->boolean('is_active')->default(true);

            // Colonnes pour le support anonyme
            $table->boolean('is_anonymous')->default(false);
            $table->string('device_id')->nullable()->unique();

            // Colonnes pour les informations de l'appareil
            $table->string('device_os')->nullable();
            $table->string('device_os_version')->nullable();
            $table->string('device_model')->nullable();
            $table->string('app_version')->nullable();
            $table->string('fcm_token')->nullable();

            $table->rememberToken();
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['provider', 'provider_id']);
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_users');
    }
};