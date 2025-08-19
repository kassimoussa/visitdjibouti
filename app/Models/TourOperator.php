<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class TourOperator extends Model
{
    use HasFactory;

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
    ];

    /**
     * Boot method pour générer le slug automatiquement
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tourOperator) {
            if (!$tourOperator->slug) {
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
        
        if (!$translation) {
            $translation = $this->translations->firstWhere('locale', config('app.fallback_locale', 'fr'));
        }
        
        if (!$translation) {
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
        return $query->selectRaw("
                *,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
            ", [$latitude, $longitude, $latitude])
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
        if (!$this->website) {
            return null;
        }

        if (preg_match('/^https?:\/\//', $this->website)) {
            return $this->website;
        }

        return 'https://' . $this->website;
    }

    /**
     * Obtenir la galerie d'images
     */
    public function getGalleryAttribute()
    {
        return $this->media;
    }
}