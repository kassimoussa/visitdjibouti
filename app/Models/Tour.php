<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'tour_operator_id',
        'type',
        'target_id',
        'target_type',
        'start_date',
        'end_date',
        'price',
        'currency',
        'max_participants',
        'current_participants',
        'difficulty_level',
        'includes',
        'requirements',
        'meeting_point_latitude',
        'meeting_point_longitude',
        'meeting_point_address',
        'status',
        'is_featured',
        'weather_dependent',
        'cancellation_policy',
        'featured_image_id',
        'views_count'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'meeting_point_latitude' => 'decimal:8',
        'meeting_point_longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'weather_dependent' => 'boolean',
        'views_count' => 'integer',
        'includes' => 'array',
        'requirements' => 'array',
    ];

    /**
     * Get all reservations for this tour.
     */
    public function reservations()
    {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    /**
     * Get the tour operator that owns the tour.
     */
    public function tourOperator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class);
    }

    /**
     * Get the translations for the tour.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TourTranslation::class);
    }

    /**
     * Get the translation for a specific locale.
     */
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('locale', $locale)->first()
            ?? $this->translations->where('locale', config('app.fallback_locale', 'fr'))->first();
    }

    /**
     * Get the media associated with the tour.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_tour')
            ->withTimestamps();
    }

    /**
     * Get the featured image.
     */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /**
     * Get the parent target model (Poi or Event).
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * Get difficulty level label.
     */
    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty_level) {
            'easy' => 'Facile',
            'moderate' => 'Modéré',
            'difficult' => 'Difficile',
            'expert' => 'Expert',
            default => 'Non spécifié'
        };
    }

    /**
     * Computed attributes
     */
    public function getFormattedDurationAttribute(): string
    {
        $parts = [];

        // Calculer la durée en jours basée sur les dates
        if ($this->start_date && $this->end_date) {
            $days = $this->start_date->diffInDays($this->end_date) + 1; // +1 pour inclure le jour de f>
            if ($days > 0) {
                $parts[] = $days . ' jour' . ($days > 1 ? 's' : '');
            }
        }

        if ($this->duration_hours > 0) {
            $parts[] = $this->duration_hours . ' heure' . ($this->duration_hours > 1 ? 's' : '');
        }

        return implode(' ', $parts) ?: 'Durée non spécifiée';
    }

    public function getDurationInDaysAttribute(): int
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }
        return 0;
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if (!$this->start_date) {
            return 'Dates non définies';
        }

        if (!$this->end_date || $this->start_date->eq($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }

        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'poi' => 'Visite de site',
            'event' => 'Accompagnement événement',
            'mixed' => 'Circuit mixte',
            'cultural' => 'Culturel',
            'adventure' => 'Aventure',
            'nature' => 'Nature',
            'gastronomic' => 'Gastronomique',
            default => 'Tour'
        };
    }
}