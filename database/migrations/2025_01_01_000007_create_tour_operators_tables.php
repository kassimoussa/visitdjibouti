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
        // Tour Operators
        Schema::create('tour_operators', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->text('phones')->nullable()->comment('Numéros séparés par |');
            $table->text('emails')->nullable()->comment('Emails séparés par |');
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('logo_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('featured')->default(false)->comment('Opérateur mis en avant');
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['is_active', 'featured']);
        });

        // Tour Operator Translations
        Schema::create('tour_operator_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('address_translated')->nullable()->comment('Adresse traduite');
            $table->timestamps();

            $table->unique(['tour_operator_id', 'locale']);
            $table->index(['locale', 'name']);
        });

        // Tour Operator Media
        Schema::create('tour_operator_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->integer('order')->default(0)->comment("Ordre d'affichage");
            $table->timestamps();

            $table->unique(['tour_operator_id', 'media_id']);
            $table->index(['tour_operator_id', 'order']);
        });

        // Tour Operator Users
        Schema::create('tour_operator_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('position')->nullable();
            $table->string('avatar')->nullable();
            $table->string('language_preference', 5)->default('fr');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['tour_operator_id', 'is_active']);
            $table->index('email');
            $table->index('username');
        });

        // Operator Password Reset Tokens
        Schema::create('operator_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // POI-Tour Operator Pivot
        Schema::create('poi_tour_operator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->enum('service_type', ['guide', 'transport', 'full_package', 'accommodation', 'activity', 'other'])->default('guide');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['poi_id', 'tour_operator_id'], 'unique_poi_tour_operator');
            $table->index(['poi_id', 'is_active']);
            $table->index(['tour_operator_id', 'is_active']);
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poi_tour_operator');
        Schema::dropIfExists('operator_password_reset_tokens');
        Schema::dropIfExists('tour_operator_users');
        Schema::dropIfExists('tour_operator_media');
        Schema::dropIfExists('tour_operator_translations');
        Schema::dropIfExists('tour_operators');
    }
};
