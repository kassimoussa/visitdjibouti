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
        Schema::table('app_users', function (Blueprint $table) {
            // ===== INFORMATIONS TECHNIQUES APPAREIL =====
            $table->string('device_type', 50)->nullable()->after('device_id'); // 'ios', 'android', 'web', 'tablet'
            $table->string('device_brand', 100)->nullable()->after('device_type'); // 'Apple', 'Samsung', 'Google', 'Huawei'
            $table->string('device_model', 150)->nullable()->after('device_brand'); // 'iPhone 15 Pro Max', 'Galaxy S24 Ultra'
            $table->string('device_name', 150)->nullable()->after('device_model'); // Nom donné par l'utilisateur
            $table->string('device_os', 50)->nullable()->after('device_name'); // 'iOS', 'Android', 'HarmonyOS'
            $table->string('device_os_version', 50)->nullable()->after('device_os'); // '17.1.2', 'Android 14'
            $table->string('device_platform', 50)->nullable()->after('device_os_version'); // 'native', 'flutter', 'react-native'
            
            // ===== INFORMATIONS APPLICATION =====
            $table->string('app_version', 30)->nullable()->after('device_platform'); // '1.2.3'
            $table->string('app_build', 30)->nullable()->after('app_version'); // '142', 'build-2024.1'
            $table->string('app_bundle_id', 150)->nullable()->after('app_build'); // 'com.visitdjibouti.app'
            $table->boolean('app_debug_mode')->default(false)->after('app_bundle_id'); // Mode debug actif
            
            // ===== CARACTÉRISTIQUES ÉCRAN =====
            $table->string('screen_resolution', 30)->nullable()->after('app_debug_mode'); // '1170x2532'
            $table->decimal('screen_density', 4, 2)->nullable()->after('screen_resolution'); // 3.0, 2.5
            $table->string('screen_size', 20)->nullable()->after('screen_density'); // '6.1 inch'
            $table->string('orientation', 20)->nullable()->after('screen_size'); // 'portrait', 'landscape'
            
            // ===== CAPACITÉS RÉSEAU =====
            $table->string('network_type', 30)->nullable()->after('orientation'); // 'wifi', '5G', '4G', '3G'
            $table->string('carrier_name', 100)->nullable()->after('network_type'); // 'Orange', 'Telecom'
            $table->string('connection_type', 30)->nullable()->after('carrier_name'); // 'cellular', 'wifi'
            $table->boolean('is_roaming')->default(false)->after('connection_type');
            
            // ===== INFORMATIONS SYSTÈME =====
            $table->bigInteger('total_memory')->nullable()->after('is_roaming'); // RAM totale en bytes
            $table->bigInteger('available_memory')->nullable()->after('total_memory'); // RAM disponible
            $table->bigInteger('total_storage')->nullable()->after('available_memory'); // Stockage total
            $table->bigInteger('available_storage')->nullable()->after('total_storage'); // Stockage disponible
            $table->decimal('battery_level', 5, 2)->nullable()->after('available_storage'); // 85.5%
            $table->boolean('is_charging')->default(false)->after('battery_level');
            $table->boolean('is_low_power_mode')->default(false)->after('is_charging');
            
            // ===== LOCALISATION ET GÉOGRAPHIE =====
            $table->decimal('current_latitude', 10, 8)->nullable()->after('is_low_power_mode');
            $table->decimal('current_longitude', 11, 8)->nullable()->after('current_latitude');
            $table->decimal('location_accuracy', 8, 2)->nullable()->after('current_longitude'); // Précision en mètres
            $table->decimal('altitude', 8, 2)->nullable()->after('location_accuracy'); // Altitude en mètres
            $table->decimal('speed', 6, 2)->nullable()->after('altitude'); // Vitesse en m/s
            $table->decimal('heading', 6, 2)->nullable()->after('speed'); // Direction 0-360°
            $table->timestamp('location_updated_at')->nullable()->after('heading');
            $table->string('location_source', 30)->nullable()->after('location_updated_at'); // 'gps', 'network', 'passive'
            $table->string('current_address', 500)->nullable()->after('location_source'); // Adresse géocodée
            $table->string('current_city', 100)->nullable()->after('current_address');
            $table->string('current_country', 100)->nullable()->after('current_city');
            $table->string('current_timezone', 50)->nullable()->after('current_country'); // 'Africa/Djibouti'
            
            // ===== NOTIFICATIONS ET PERMISSIONS =====
            $table->string('push_token', 500)->nullable()->after('current_timezone'); // FCM/APNS token
            $table->string('push_provider', 20)->nullable()->after('push_token'); // 'fcm', 'apns'
            $table->boolean('location_permission')->default(false)->after('push_provider');
            $table->boolean('camera_permission')->default(false)->after('location_permission');
            $table->boolean('contacts_permission')->default(false)->after('camera_permission');
            $table->boolean('storage_permission')->default(false)->after('contacts_permission');
            $table->boolean('notification_permission')->default(false)->after('storage_permission');
            
            // ===== PARAMÈTRES UTILISATEUR =====
            $table->json('device_languages')->nullable()->after('notification_permission'); // ['fr', 'en', 'ar']
            $table->string('keyboard_language', 10)->nullable()->after('device_languages'); // 'fr', 'en'
            $table->string('number_format', 10)->nullable()->after('keyboard_language'); // 'fr_FR', 'en_US'
            $table->string('currency_format', 10)->nullable()->after('number_format'); // 'DJF', 'EUR'
            $table->boolean('dark_mode_enabled')->default(false)->after('currency_format');
            $table->boolean('accessibility_enabled')->default(false)->after('dark_mode_enabled');
            
            // ===== TRACKING ET ANALYTICS =====
            $table->string('user_agent', 500)->nullable()->after('accessibility_enabled');
            $table->string('advertising_id', 100)->nullable()->after('user_agent'); // IDFA/GAID
            $table->boolean('ad_tracking_enabled')->default(true)->after('advertising_id');
            $table->integer('session_count')->default(0)->after('ad_tracking_enabled'); // Nombre de sessions
            $table->timestamp('first_install_at')->nullable()->after('session_count');
            $table->timestamp('last_app_update_at')->nullable()->after('first_install_at');
            $table->json('installed_apps')->nullable()->after('last_app_update_at'); // Apps installées (si permission)
            
            // ===== MÉTRIQUES D'UTILISATION =====
            $table->integer('total_app_launches')->default(0)->after('installed_apps');
            $table->integer('total_time_spent')->default(0)->after('total_app_launches'); // Temps total en secondes
            $table->integer('crashes_count')->default(0)->after('total_time_spent');
            $table->timestamp('last_crash_at')->nullable()->after('crashes_count');
            $table->json('feature_usage')->nullable()->after('last_crash_at'); // Utilisation des fonctionnalités
            
            // ===== CONFORMITÉ ET SÉCURITÉ =====
            $table->boolean('is_jailbroken_rooted')->default(false)->after('feature_usage');
            $table->boolean('developer_mode_enabled')->default(false)->after('is_jailbroken_rooted');
            $table->boolean('mock_location_enabled')->default(false)->after('developer_mode_enabled');
            $table->string('device_fingerprint', 200)->nullable()->after('mock_location_enabled'); // Empreinte unique
            $table->timestamp('device_info_updated_at')->nullable()->after('device_fingerprint');
            
            // Index pour les recherches fréquentes
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
        Schema::table('app_users', function (Blueprint $table) {
            $table->dropColumn([
                // Informations techniques
                'device_type', 'device_brand', 'device_model', 'device_name',
                'device_os', 'device_os_version', 'device_platform',
                
                // Application
                'app_version', 'app_build', 'app_bundle_id', 'app_debug_mode',
                
                // Écran
                'screen_resolution', 'screen_density', 'screen_size', 'orientation',
                
                // Réseau
                'network_type', 'carrier_name', 'connection_type', 'is_roaming',
                
                // Système
                'total_memory', 'available_memory', 'total_storage', 'available_storage',
                'battery_level', 'is_charging', 'is_low_power_mode',
                
                // Localisation
                'current_latitude', 'current_longitude', 'location_accuracy',
                'altitude', 'speed', 'heading', 'location_updated_at', 'location_source',
                'current_address', 'current_city', 'current_country', 'current_timezone',
                
                // Notifications et permissions
                'push_token', 'push_provider', 'location_permission', 'camera_permission',
                'contacts_permission', 'storage_permission', 'notification_permission',
                
                // Paramètres utilisateur
                'device_languages', 'keyboard_language', 'number_format', 'currency_format',
                'dark_mode_enabled', 'accessibility_enabled',
                
                // Tracking
                'user_agent', 'advertising_id', 'ad_tracking_enabled', 'session_count',
                'first_install_at', 'last_app_update_at', 'installed_apps',
                
                // Métriques
                'total_app_launches', 'total_time_spent', 'crashes_count', 'last_crash_at',
                'feature_usage',
                
                // Sécurité
                'is_jailbroken_rooted', 'developer_mode_enabled', 'mock_location_enabled',
                'device_fingerprint', 'device_info_updated_at'
            ]);
        });
    }
};
