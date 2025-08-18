<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    public function eventReservations()
    {
        return $this->hasMany(EventReservation::class, 'app_user_id');
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
     * API Resource transformation.
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // Ajouter des attributs calculés pour l'API
        $data['avatar_url'] = $this->avatar_url;
        $data['age'] = $this->age;
        $data['is_social_user'] = $this->isSocialUser();
        
        return $data;
    }
}