<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Arr;

class AppUser extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'app_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'date_of_birth',
        'gender',
        'preferred_language',
        'push_notifications_enabled',
        'email_notifications_enabled',
        'provider',
        'provider_id',
        'city',
        'country',
        'is_active',
        'last_login_at',
        'last_login_ip',
        // Champs pour utilisateurs anonymes
        'is_anonymous',
        'anonymous_id',
        'device_id',
        'converted_at',
        'conversion_source',
        // Informations techniques appareil
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'push_notifications_enabled' => 'boolean',
            'email_notifications_enabled' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            // Champs pour utilisateurs anonymes
            'is_anonymous' => 'boolean',
            'converted_at' => 'datetime',
            'conversion_source' => 'array',
            // Informations appareil
            'app_debug_mode' => 'boolean',
            'screen_density' => 'decimal:2',
            'is_roaming' => 'boolean',
            'total_memory' => 'integer',
            'available_memory' => 'integer',
            'total_storage' => 'integer',
            'available_storage' => 'integer',
            'battery_level' => 'decimal:2',
            'is_charging' => 'boolean',
            'is_low_power_mode' => 'boolean',
            // Localisation
            'current_latitude' => 'decimal:8',
            'current_longitude' => 'decimal:8',
            'location_accuracy' => 'decimal:2',
            'altitude' => 'decimal:2',
            'speed' => 'decimal:2',
            'heading' => 'decimal:2',
            'location_updated_at' => 'datetime',
            // Permissions
            'location_permission' => 'boolean',
            'camera_permission' => 'boolean',
            'contacts_permission' => 'boolean',
            'storage_permission' => 'boolean',
            'notification_permission' => 'boolean',
            // Paramètres
            'device_languages' => 'array',
            'dark_mode_enabled' => 'boolean',
            'accessibility_enabled' => 'boolean',
            // Tracking
            'ad_tracking_enabled' => 'boolean',
            'session_count' => 'integer',
            'first_install_at' => 'datetime',
            'last_app_update_at' => 'datetime',
            'installed_apps' => 'array',
            // Métriques
            'total_app_launches' => 'integer',
            'total_time_spent' => 'integer',
            'crashes_count' => 'integer',
            'last_crash_at' => 'datetime',
            'feature_usage' => 'array',
            // Sécurité
            'is_jailbroken_rooted' => 'boolean',
            'developer_mode_enabled' => 'boolean',
            'mock_location_enabled' => 'boolean',
            'device_info_updated_at' => 'datetime',
        ];
    }

    /**
     * Check if the user registered via social login.
     */
    public function isSocialUser(): bool
    {
        return !is_null($this->provider) && in_array($this->provider, ['google', 'facebook']);
    }

    /**
     * Check if the user registered via email.
     */
    public function isEmailUser(): bool
    {
        return $this->provider === 'email' || is_null($this->provider);
    }

    /**
     * Get the user's full avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        // Si c'est une URL complète (pour les avatars sociaux)
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        // Sinon, c'est un fichier local
        return asset('storage/' . $this->avatar);
    }

    /**
     * Get user's age from date of birth.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    /**
     * Record user login activity.
     */
    public function recordLogin(?string $ipAddress = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get users by provider.
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Get the user's preferred language.
     */
    public function getPreferredLanguage(): string
    {
        return $this->preferred_language ?? config('app.locale', 'fr');
    }

    /**
     * Relations
     */

    /**
     * Get all event reservations for this user.
     */
    /* public function eventReservations()
    {
        return $this->hasMany(EventReservation::class, 'app_user_id');
    } */

    /**
     * Get all reservations (new unified system) for this user.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'app_user_id');
    }

    /**
     * Get all location history for this user.
     */
    public function locationHistory()
    {
        return $this->hasMany(UserLocationHistory::class, 'app_user_id');
    }

    /**
     * Get all favorites for this user.
     */
    public function favorites()
    {
        return $this->hasMany(UserFavorite::class, 'app_user_id');
    }

    /**
     * Get favorite POIs.
     */
    public function favoritePois()
    {
        return $this->belongsToMany(Poi::class, 'user_favorites', 'app_user_id', 'favoritable_id')
                    ->where('user_favorites.favoritable_type', Poi::class)
                    ->withTimestamps();
    }

    /**
     * Get favorite events.
     */
    public function favoriteEvents()
    {
        return $this->belongsToMany(Event::class, 'user_favorites', 'app_user_id', 'favoritable_id')
                    ->where('user_favorites.favoritable_type', Event::class)
                    ->withTimestamps();
    }

    /**
     * Check if user has favorited a specific item.
     */
    public function hasFavorited($model): bool
    {
        return UserFavorite::isFavorited(
            $this->id,
            $model->id,
            get_class($model)
        );
    }

    /**
     * Add item to favorites.
     */
    public function addToFavorites($model): bool
    {
        if ($this->hasFavorited($model)) {
            return false; // Already favorited
        }

        UserFavorite::create([
            'app_user_id' => $this->id,
            'favoritable_id' => $model->id,
            'favoritable_type' => get_class($model),
        ]);

        return true;
    }

    /**
     * Remove item from favorites.
     */
    public function removeFromFavorites($model): bool
    {
        return UserFavorite::where('app_user_id', $this->id)
                         ->where('favoritable_id', $model->id)
                         ->where('favoritable_type', get_class($model))
                         ->delete() > 0;
    }

    /**
     * Toggle favorite status for an item.
     */
    public function toggleFavorite($model): array
    {
        return UserFavorite::toggle(
            $this->id,
            $model->id,
            get_class($model)
        );
    }

    /**
     * Méthodes pour la gestion des utilisateurs anonymes
     */

    /**
     * Vérifier si l'utilisateur est anonyme.
     */
    public function isAnonymous(): bool
    {
        return $this->is_anonymous;
    }

    /**
     * Vérifier si l'utilisateur est complet (non anonyme).
     */
    public function isComplete(): bool
    {
        return !$this->is_anonymous;
    }

    /**
     * Créer un utilisateur anonyme.
     */
    public static function createAnonymous(?string $deviceId = null): self
    {
        $anonymousId = 'anon_' . uniqid() . '_' . time();
        
        return self::create([
            'is_anonymous' => true,
            'anonymous_id' => $anonymousId,
            'device_id' => $deviceId,
            'is_active' => true,
            'preferred_language' => config('app.locale', 'fr'),
        ]);
    }

    /**
     * Convertir un utilisateur anonyme en utilisateur complet.
     */
    public function convertToComplete(array $userData, string $source = 'manual'): bool
    {
        if (!$this->is_anonymous) {
            return false; // Déjà un utilisateur complet
        }

        $updateData = [
            'is_anonymous' => false,
            'converted_at' => now(),
            'conversion_source' => ['source' => $source, 'timestamp' => now()],
        ];

        // Ajouter les données utilisateur
        $updateData = array_merge($updateData, $userData);

        return $this->update($updateData);
    }

    /**
     * Scope pour récupérer uniquement les utilisateurs anonymes.
     */
    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    /**
     * Scope pour récupérer uniquement les utilisateurs complets.
     */
    public function scopeComplete($query)
    {
        return $query->where('is_anonymous', false);
    }

    /**
     * Trouver un utilisateur anonyme par son ID anonyme.
     */
    public static function findByAnonymousId(string $anonymousId): ?self
    {
        return self::where('anonymous_id', $anonymousId)->first();
    }

    /**
     * Trouver un utilisateur anonyme par device ID.
     */
    public static function findByDeviceId(string $deviceId): ?self
    {
        return self::where('device_id', $deviceId)
                   ->where('is_anonymous', true)
                   ->first();
    }

    /**
     * Obtenir le nom d'affichage pour l'utilisateur (anonyme ou complet).
     */
    public function getDisplayName(): string
    {
        if ($this->is_anonymous) {
            return 'Utilisateur anonyme';
        }

        return $this->name ?? 'Utilisateur';
    }

    /**
     * Obtenir l'identifiant unique pour l'utilisateur (ID ou anonymous_id).
     */
    public function getUniqueIdentifier(): string
    {
        return $this->is_anonymous ? $this->anonymous_id : (string) $this->id;
    }

    /**
     * API Resource transformation.
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // Ajouter des attributs calculés pour l'API
        $data['avatar_url'] = $this->avatar_url;
        $data['age'] = $this->age;
        $data['is_social_user'] = $this->isSocialUser();
        $data['display_name'] = $this->getDisplayName();
        $data['unique_identifier'] = $this->getUniqueIdentifier();
        
        // Masquer certains champs pour les utilisateurs anonymes
        if ($this->is_anonymous) {
            $data = Arr::except($data, ['email', 'phone', 'date_of_birth', 'gender', 'city', 'country']);
        }
        
        return $data;
    }
}