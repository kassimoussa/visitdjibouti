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

            // Support anonyme
            $table->boolean('is_anonymous')->default(false);
            $table->string('anonymous_id')->nullable()->unique();

            // Informations appareil de base
            $table->string('device_id')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('device_brand', 100)->nullable();
            $table->string('device_model', 150)->nullable();
            $table->string('device_name', 150)->nullable();
            $table->string('device_os', 50)->nullable();
            $table->string('device_os_version', 50)->nullable();
            $table->string('device_platform', 50)->nullable();

            // Informations application
            $table->string('app_version', 30)->nullable();
            $table->string('app_build', 30)->nullable();
            $table->string('app_bundle_id', 150)->nullable();
            $table->boolean('app_debug_mode')->default(false);

            // Écran
            $table->string('screen_resolution', 30)->nullable();
            $table->decimal('screen_density', 4, 2)->nullable();
            $table->string('screen_size', 20)->nullable();
            $table->string('orientation', 20)->nullable();

            // Réseau
            $table->string('network_type', 30)->nullable();
            $table->string('carrier_name', 100)->nullable();
            $table->string('connection_type', 30)->nullable();
            $table->boolean('is_roaming')->default(false);

            // Mémoire et stockage
            $table->bigInteger('total_memory')->nullable();
            $table->bigInteger('available_memory')->nullable();
            $table->bigInteger('total_storage')->nullable();
            $table->bigInteger('available_storage')->nullable();

            // Batterie
            $table->decimal('battery_level', 5, 2)->nullable();
            $table->boolean('is_charging')->default(false);
            $table->boolean('is_low_power_mode')->default(false);

            // Localisation
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->decimal('location_accuracy', 8, 2)->nullable();
            $table->decimal('altitude', 8, 2)->nullable();
            $table->decimal('speed', 6, 2)->nullable();
            $table->decimal('heading', 6, 2)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->string('location_source', 30)->nullable();
            $table->string('current_address', 500)->nullable();
            $table->string('current_city', 100)->nullable();
            $table->string('current_country', 100)->nullable();
            $table->string('current_timezone', 50)->nullable();

            // Notifications
            $table->string('push_token', 500)->nullable();
            $table->string('push_provider', 20)->nullable();

            // Permissions
            $table->boolean('location_permission')->default(false);
            $table->boolean('camera_permission')->default(false);
            $table->boolean('contacts_permission')->default(false);
            $table->boolean('storage_permission')->default(false);
            $table->boolean('notification_permission')->default(false);

            // Préférences linguistiques
            $table->json('device_languages')->nullable();
            $table->string('keyboard_language', 10)->nullable();
            $table->string('number_format', 10)->nullable();
            $table->string('currency_format', 10)->nullable();

            // Préférences UI
            $table->boolean('dark_mode_enabled')->default(false);
            $table->boolean('accessibility_enabled')->default(false);

            // Tracking
            $table->string('user_agent', 500)->nullable();
            $table->string('advertising_id', 100)->nullable();
            $table->boolean('ad_tracking_enabled')->default(true);

            // Statistiques
            $table->integer('session_count')->default(0);
            $table->timestamp('first_install_at')->nullable();
            $table->timestamp('last_app_update_at')->nullable();
            $table->json('installed_apps')->nullable();
            $table->integer('total_app_launches')->default(0);
            $table->integer('total_time_spent')->default(0);
            $table->integer('crashes_count')->default(0);
            $table->timestamp('last_crash_at')->nullable();
            $table->json('feature_usage')->nullable();

            // Sécurité
            $table->boolean('is_jailbroken_rooted')->default(false);
            $table->boolean('developer_mode_enabled')->default(false);
            $table->boolean('mock_location_enabled')->default(false);
            $table->string('device_fingerprint', 200)->nullable();
            $table->timestamp('device_info_updated_at')->nullable();

            // Informations utilisateur
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('preferred_language', 2)->default('fr');
            $table->boolean('push_notifications_enabled')->default(true);
            $table->boolean('email_notifications_enabled')->default(true);

            // OAuth
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            // Adresse
            $table->string('city')->nullable();
            $table->string('country')->default('DJ');

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            $table->rememberToken();
            $table->timestamps();

            // Conversion d'anonyme à complet
            $table->timestamp('converted_at')->nullable();
            $table->json('conversion_source')->nullable();

            // Index
            $table->index(['provider', 'provider_id']);
            $table->index('email');
            $table->index('is_active');
            $table->index('device_type');
            $table->index('device_brand');
            $table->index('current_latitude');
            $table->index('current_longitude');
            $table->index(['current_latitude', 'current_longitude']);
            $table->index('location_updated_at');
            $table->index('device_info_updated_at');
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
