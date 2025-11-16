<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TourOperator extends Model
{
    use HasFactory, Commentable;

    protected $fillable = [
        'slug',
        'phones',
        'emails',
        'website',
        'address',
        'latitude',
        'longitude',
        'logo_id',
        'is_active',
        'featured',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'phones' => 'array',
        'emails' => 'array',
    ];

    /**
     * Boot method pour générer le slug automatiquement
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tourOperator) {
            if (! $tourOperator->slug) {
                $tourOperator->slug = Str::random(10);
            }
        });
    }

    /**
     * Relation avec les traductions
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TourOperatorTranslation::class);
    }

    /**
     * Relation avec le logo
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    /**
     * Relation avec les médias (galerie)
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'tour_operator_media')
            ->withPivot(['order'])
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * Obtenir la traduction dans la langue spécifiée
     */
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->first()
                ?? $this->translations()
                    ->where('locale', config('app.fallback_locale', 'fr'))
                    ->first();
    }

    /**
     * Obtenir la traduction avec fallback
     */
    public function getTranslation(string $locale)
    {
        $translation = $this->translations->firstWhere('locale', $locale);

        if (! $translation) {
            $translation = $this->translations->firstWhere('locale', config('app.fallback_locale', 'fr'));
        }

        if (! $translation) {
            return new TourOperatorTranslation([
                'name' => '',
                'description' => '',
                'address_translated' => '',
            ]);
        }

        return $translation;
    }

    /**
     * Accesseurs pour les attributs traduits
     */
    public function getNameAttribute()
    {
        return $this->translation() ? $this->translation()->name : '';
    }

    public function getDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->description : '';
    }

    /**
     * Obtenir le nom traduit avec fallback
     */
    public function getTranslatedName(string $locale = 'fr')
    {
        $translation = $this->translations->firstWhere('locale', $locale);
        if ($translation && $translation->name) {
            return $translation->name;
        }

        $fallback = $this->translations->firstWhere('locale', 'fr');

        return $fallback ? $fallback->name : '';
    }

    /**
     * Obtenir la description traduite avec fallback
     */
    public function getTranslatedDescription(string $locale = 'fr')
    {
        $translation = $this->translations->firstWhere('locale', $locale);
        if ($translation && $translation->description) {
            return $translation->description;
        }

        $fallback = $this->translations->firstWhere('locale', 'fr');

        return $fallback ? $fallback->description : '';
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->selectRaw('
                *,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ', [$latitude, $longitude, $latitude])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    /**
     * Obtenir les téléphones sous forme de tableau
     */
    public function getPhonesArrayAttribute()
    {
        return $this->phones ? explode('|', $this->phones) : [];
    }

    /**
     * Obtenir les emails sous forme de tableau
     */
    public function getEmailsArrayAttribute()
    {
        return $this->emails ? explode('|', $this->emails) : [];
    }

    /**
     * Obtenir le premier téléphone
     */
    public function getFirstPhoneAttribute()
    {
        $phones = $this->phones_array;

        return count($phones) > 0 ? $phones[0] : null;
    }

    /**
     * Obtenir le premier email
     */
    public function getFirstEmailAttribute()
    {
        $emails = $this->emails_array;

        return count($emails) > 0 ? $emails[0] : null;
    }

    /**
     * Obtenir l'URL du site web formatée
     */
    public function getWebsiteUrlAttribute()
    {
        if (! $this->website) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $this->website)) {
            return $this->website;
        }

        return 'https://'.$this->website;
    }

    /**
     * Obtenir la galerie d'images
     */
    public function getGalleryAttribute()
    {
        return $this->media;
    }

    /**
     * Relation many-to-many avec les POIs
     */
    public function pois(): BelongsToMany
    {
        return $this->belongsToMany(Poi::class, 'poi_tour_operator')
            ->withPivot(['service_type', 'is_primary', 'is_active', 'notes'])
            ->withTimestamps()
            ->where('poi_tour_operator.is_active', true)
            ->orderByPivot('is_primary', 'desc');
    }

    /**
     * Obtenir les POIs actifs
     */
    public function activePois(): BelongsToMany
    {
        return $this->pois()
            ->where('pois.status', 'published');
    }

    /**
     * Obtenir les POIs principaux (où ce tour operator est principal)
     */
    public function primaryPois(): BelongsToMany
    {
        return $this->pois()
            ->wherePivot('is_primary', true);
    }

    /**
     * Vérifier si ce tour operator dessert des POIs
     */
    public function hasPois(): bool
    {
        return $this->pois()->exists();
    }

    /**
     * Get all users for this tour operator.
     */
    public function users(): HasMany
    {
        return $this->hasMany(TourOperatorUser::class);
    }

    /**
     * Get all active users for this tour operator.
     */
    public function activeUsers(): HasMany
    {
        return $this->users()->active();
    }

    /**
     * Get all events managed by this tour operator.
     */
    public function managedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'tour_operator_id');
    }

    /**
     * Get all published events managed by this tour operator.
     */
    public function publishedEvents(): HasMany
    {
        return $this->managedEvents()->where('status', 'published');
    }

    /**
     * Get all tours offered by this tour operator.
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'tour_operator_id');
    }

    /**
     * Get all active tours offered by this tour operator.
     */
    public function activeTours(): HasMany
    {
        return $this->tours()->where('status', 'active');
    }

    /**
     * Get all activities offered by this tour operator.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'tour_operator_id');
    }

    /**
     * Get all active activities offered by this tour operator.
     */
    public function activeActivities(): HasMany
    {
        return $this->activities()->where('status', 'active');
    }

    /**
     * Get all reservations for this tour operator's events and tours.
     */
    public function allReservations()
    {
        return Reservation::whereIn('id', function ($query) {
            $query->select('r.id')
                ->from('reservations as r')
                ->leftJoin('events as e', function ($join) {
                    $join->on('r.reservable_id', '=', 'e.id')
                        ->where('r.reservable_type', '=', Event::class);
                })
                ->leftJoin('tour_schedules as ts', function ($join) {
                    $join->on('r.reservable_id', '=', 'ts.id')
                        ->where('r.reservable_type', '=', TourSchedule::class);
                })
                ->leftJoin('tours as t', 'ts.tour_id', '=', 't.id')
                ->where(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->where('r.reservable_type', Event::class)
                            ->where('e.tour_operator_id', $this->id);
                    })
                        ->orWhere(function ($subQ) {
                            $subQ->where('r.reservable_type', TourSchedule::class)
                                ->where('t.tour_operator_id', $this->id);
                        });
                })
                ->whereNull('r.deleted_at');
        });
    }

    /**
     * Check if this tour operator has events.
     */
    public function hasEvents(): bool
    {
        return $this->managedEvents()->exists();
    }

    /**
     * Check if this tour operator has tours.
     */
    public function hasTours(): bool
    {
        return $this->tours()->exists();
    }

    /**
     * Check if this tour operator has users.
     */
    public function hasUsers(): bool
    {
        return $this->users()->exists();
    }

    /**
     * Get statistics for this tour operator.
     */
    public function getStatistics(): array
    {
        return [
            'total_tours' => $this->tours()->count(),
            'active_tours' => $this->activeTours()->count(),
            'total_activities' => $this->activities()->count(),
            'active_activities' => $this->activities()->where('status', 'active')->count(),
            'total_pois' => $this->pois()->count(),
            'total_users' => $this->users()->count(),
            'active_users' => $this->activeUsers()->count(),
            'total_reservations' => $this->allReservations()->count(),
            'confirmed_reservations' => $this->allReservations()->confirmed()->count(),
            'revenue_this_month' => $this->allReservations()
                ->confirmed()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('payment_amount'),
        ];
    }
}
