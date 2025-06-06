<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'latitude',
        'longitude',
        'contact_email',
        'contact_phone',
        'website_url',
        'ticket_url',
        'price',
        'max_participants',
        'current_participants',
        'organizer',
        'is_featured',
        'status',
        'creator_id',
        'featured_image_id',
        'views_count'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_featured' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'views_count' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Génération automatique du slug basé sur le titre de la traduction par défaut
        static::creating(function ($event) {
            if (empty($event->slug) && request()->has('translations')) {
                $defaultLocale = config('app.fallback_locale', 'fr');
                $title = request()->input("translations.{$defaultLocale}.title");
                if ($title) {
                    $event->slug = Str::slug($title);
                }
            }
        });
    }

    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(EventTranslation::class, 'event_id');
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
    public function getTitleAttribute()
    {
        return $this->translation() ? $this->translation()->title : '';
    }
    
    public function getDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->description : '';
    }
    
    public function getShortDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->short_description : '';
    }
    
    public function getLocationDetailsAttribute()
    {
        return $this->translation() ? $this->translation()->location_details : '';
    }
    
    public function getRequirementsAttribute()
    {
        return $this->translation() ? $this->translation()->requirements : '';
    }
    
    public function getProgramAttribute()
    {
        return $this->translation() ? $this->translation()->program : '';
    }
    
    public function getAdditionalInfoAttribute()
    {
        return $this->translation() ? $this->translation()->additional_info : '';
    }

    /**
     * Get the creator of this event.
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
     * Get the categories for this event.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_event');
    }

    /**
     * Get the media (images) for this event.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_event')
                    ->withPivot('order')
                    ->orderBy('order');
    }

    /**
     * Get event registrations.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Get event reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(EventReview::class);
    }

    /**
     * Check if the event has a specific status.
     *
     * @param string $status
     * @return bool
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Get all published events.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get all featured events.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get upcoming events.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    /**
     * Get ongoing events.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOngoing($query)
    {
        $today = now()->toDateString();
        return $query->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today);
    }

    /**
     * Check if event is currently active.
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'published' && 
               $this->start_date >= now()->toDateString();
    }

    /**
     * Check if event is ongoing.
     */
    public function getIsOngoingAttribute()
    {
        $now = now()->toDateString();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    /**
     * Check if event has ended.
     */
    public function getHasEndedAttribute()
    {
        return $this->end_date < now()->toDateString();
    }

    /**
     * Check if event is sold out.
     */
    public function getIsSoldOutAttribute()
    {
        return $this->max_participants && 
               $this->current_participants >= $this->max_participants;
    }

    /**
     * Get available spots.
     */
    public function getAvailableSpotsAttribute()
    {
        if (!$this->max_participants) {
            return null;
        }
        
        return max(0, $this->max_participants - $this->current_participants);
    }

    /**
     * Get formatted date range.
     */
    public function getFormattedDateRangeAttribute()
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }
        
        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    /**
     * Get full location with details.
     *
     * @return string
     */
    public function getFullLocationAttribute(): string
    {
        $location = $this->location ?? '';
        $details = $this->location_details ?? '';
        
        if (empty($location)) {
            return $details;
        }
        
        return $location . ($details ? ' - ' . $details : '');
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Add a participant to the event.
     */
    public function addParticipant()
    {
        $this->increment('current_participants');
    }

    /**
     * Remove a participant from the event.
     */
    public function removeParticipant()
    {
        if ($this->current_participants > 0) {
            $this->decrement('current_participants');
        }
    }
}