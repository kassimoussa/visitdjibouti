<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Poi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'latitude',
        'longitude',
        'region',
        'contact',
        'website',
        'is_featured',
        'allow_reservations',
        'status',
        'creator_id',
        'featured_image_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_featured' => 'boolean',
        'allow_reservations' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Génération automatique du slug basé sur le nom de la traduction par défaut
        static::creating(function ($poi) {
            if (empty($poi->slug) && request()->has('translations')) {
                $defaultLocale = config('app.fallback_locale', 'fr');
                $name = request()->input("translations.{$defaultLocale}.name");
                if ($name) {
                    $poi->slug = Str::slug($name);
                }
            }
        });
    }

    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(PoiTranslation::class, 'poi_id');
    }
    
    /**
     * Obtenir la traduction dans la langue spécifiée.
     */
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations()
                    ->where('locale', $locale)
                    ->first() 
                ?? $this->translations()
                      ->where('locale', config('app.fallback_locale'))
                      ->first();
    }
    
    /**
     * Accesseurs pour les attributs traduits.
     */
    public function getNameAttribute()
    {
        return $this->translation() ? $this->translation()->name : '';
    }
    
    public function getDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->description : '';
    }
    
    public function getShortDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->short_description : '';
    }
    
    public function getAddressAttribute()
    {
        return $this->translation() ? $this->translation()->address : '';
    }
    
    public function getOpeningHoursAttribute()
    {
        return $this->translation() ? $this->translation()->opening_hours : '';
    }
    
    public function getEntryFeeAttribute()
    {
        return $this->translation() ? $this->translation()->entry_fee : '';
    }
    
    public function getTipsAttribute()
    {
        return $this->translation() ? $this->translation()->tips : '';
    }

    /**
     * Get the creator of this point of interest.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'creator_id');
    }

    /**
     * Get the featured image.
     */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /**
     * Get the categories for this point of interest.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_poi');
    }

    /**
     * Get the media (images) for this point of interest.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_poi')
                    ->withPivot('order')
                    ->orderBy('order');
    }

    /**
     * Check if the POI has a specific status.
     *
     * @param string $status
     * @return bool
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Get all published POIs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get all featured POIs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted address with region.
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        if (empty($this->address)) {
            return $this->region ?? '';
        }
        
        return $this->address . ($this->region ? ', ' . $this->region : '');
    }
}