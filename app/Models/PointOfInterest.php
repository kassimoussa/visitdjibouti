<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PointOfInterest extends Model
{
    use HasFactory;

    protected $table = 'points_of_interest';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'latitude',
        'longitude',
        'address',
        'region',
        'opening_hours',
        'entry_fee',
        'contact',
        'website',
        'tips',
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
        
        // Génération automatique du slug
        static::creating(function ($poi) {
            if (empty($poi->slug)) {
                $poi->slug = Str::slug($poi->name);
            }
        });
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
        return $this->belongsToMany(Category::class, 'category_point_of_interest');
    }

    /**
     * Get the media (images) for this point of interest.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_point_of_interest')
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