<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes, Commentable;

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
        'region',
        'contact_email',
        'contact_phone',
        'website_url',
        'ticket_url',
        'price',
        'max_participants',
        'current_participants',
        'organizer',
        'is_featured',
        'allow_reservations',
        'status',
        'creator_id',
        'tour_operator_id',
        'featured_image_id',
        'views_count',
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
        'allow_reservations' => 'boolean',
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
     * Get the tour operator managing this event.
     */
    public function tourOperator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class, 'tour_operator_id');
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
     * Get event registrations (legacy - use reservations() instead).
     *
     * @deprecated Use reservations() method instead
     */
    public function registrations(): MorphMany
    {
        // Rediriger vers les réservations pour compatibilité
        return $this->reservations();
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
     */
    public function hasStatus(string $status): bool
    {
        return $this->status === $status;
    }

    /**
     * Get all published events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Get all featured events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get upcoming events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString());
    }

    /**
     * Get ongoing events.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
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
        if (! $this->max_participants) {
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

        return $this->start_date->format('d/m/Y').' - '.$this->end_date->format('d/m/Y');
    }

    /**
     * Get full location with details.
     */
    public function getFullLocationAttribute(): string
    {
        $location = $this->location ?? '';
        $details = $this->location_details ?? '';

        if (empty($location)) {
            return $details;
        }

        return $location.($details ? ' - '.$details : '');
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Add a specific number of participants to the event.
     */
    public function addParticipant(int $count = 1)
    {
        $this->increment('current_participants', $count);
    }

    /**
     * Remove a specific number of participants from the event.
     */
    public function removeParticipant(int $count = 1)
    {
        $this->decrement('current_participants', $count);
    }

    /**
     * Get all reservations for this event.
     */
    public function reservations()
    {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    /**
     * Get confirmed reservations for this event.
     */
    public function confirmedReservations()
    {
        return $this->reservations()->confirmed();
    }

    /**
     * Get pending reservations for this event.
     */
    public function pendingReservations()
    {
        return $this->reservations()->pending();
    }

    /**
     * Get upcoming reservations for this event.
     */
    public function upcomingReservations()
    {
        return $this->reservations()->upcoming();
    }

    /**
     * Get confirmed registrations (legacy - use confirmedReservations() instead).
     *
     * @deprecated Use confirmedReservations() method instead
     */
    public function confirmedRegistrations()
    {
        return $this->confirmedReservations();
    }

    /**
     * Get reservations count.
     */
    public function getReservationsCountAttribute(): int
    {
        return $this->reservations()->count();
    }

    /**
     * Get confirmed reservations count.
     */
    public function getConfirmedReservationsCountAttribute(): int
    {
        return $this->confirmedReservations()->count();
    }

    /**
     * Check if event is available for reservations.
     */
    public function isAvailableForReservation(): bool
    {
        return $this->status === 'published'
            && $this->start_date >= now()->toDateString()
            && ($this->max_participants === null || $this->current_participants < $this->max_participants);
    }

    /**
     * Get remaining spots for the event.
     */
    public function getRemainingSpots(): ?int
    {
        if ($this->max_participants === null) {
            return null; // Unlimited spots
        }

        return max(0, $this->max_participants - $this->current_participants);
    }

    /**
     * Check if event is managed by a tour operator.
     */
    public function isManagedByTourOperator(): bool
    {
        return ! is_null($this->tour_operator_id);
    }

    /**
     * Check if event is managed by admin.
     */
    public function isManagedByAdmin(): bool
    {
        return is_null($this->tour_operator_id);
    }

    /**
     * Get the manager (tour operator or admin) of this event.
     */
    public function getManagerAttribute(): ?object
    {
        if ($this->isManagedByTourOperator()) {
            return $this->tourOperator;
        }

        return $this->creator;
    }

    /**
     * Get the manager type of this event.
     */
    public function getManagerTypeAttribute(): string
    {
        return $this->isManagedByTourOperator() ? 'tour_operator' : 'admin';
    }

    /**
     * Get tours that target this event (polymorphic relation).
     */
    public function tours()
    {
        return $this->morphMany(Tour::class, 'target');
    }
}
