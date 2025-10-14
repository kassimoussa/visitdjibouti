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
        'start_time',
        'end_time',
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
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
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

    // ... (Le reste du modèle, en supprimant les méthodes liées aux schedules)


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