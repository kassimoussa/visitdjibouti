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
            $table->string('password')->nullable(); // Nullable pour les connexions sociales
            $table->string('phone')->nullable();
            
            // Informations personnelles
            $table->string('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            
            // Préférences
            $table->string('preferred_language', 2)->default('fr'); // fr, en, ar
            $table->boolean('push_notifications_enabled')->default(true);
            $table->boolean('email_notifications_enabled')->default(true);
            
            // Authentification sociale
            $table->string('provider')->nullable(); // google, facebook, email
            $table->string('provider_id')->nullable();
            
            // Localisation (optionnel)
            $table->string('city')->nullable();
            $table->string('country')->default('DJ'); // Djibouti par défaut
            
            // Statut et métadonnées
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->ipAddress('last_login_ip')->nullable();
            
            // Tokens et sécurité
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