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
        Schema::create('tour_operator_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');

            // Informations de base
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('position')->nullable(); // Poste occupé dans l'entreprise

            // Avatar et préférences
            $table->string('avatar')->nullable();
            $table->string('language_preference', 5)->default('fr'); // fr, en, ar

            // Permissions et statut
            $table->json('permissions')->nullable(); // manage_events, manage_tours, view_reservations, manage_profile, all
            $table->boolean('is_active')->default(true);

            // Métadonnées
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Index
            $table->index(['tour_operator_id', 'is_active']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_operator_users');
    }
};