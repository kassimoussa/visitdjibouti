<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes, Commentable;

    protected $fillable = [
        'tour_operator_id',
        'created_by_operator_user_id',
        'featured_image_id',
        'slug',
        'status',
        'price',
        'currency',
        'duration_hours',
        'duration_minutes',
        'difficulty_level',
        'min_participants',
        'max_participants',
        'current_participants',
        'location_address',
        'latitude',
        'longitude',
        'region',
        'has_age_restrictions',
        'min_age',
        'max_age',
        'physical_requirements',
        'certifications_required',
        'equipment_provided',
        'equipment_required',
        'includes',
        'weather_dependent',
        'cancellation_policy',
        'is_featured',
        'views_count',
        'registrations_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'has_age_restrictions' => 'boolean',
        'weather_dependent' => 'boolean',
        'is_featured' => 'boolean',
        'physical_requirements' => 'array',
        'certifications_required' => 'array',
        'equipment_provided' => 'array',
        'equipment_required' => 'array',
        'includes' => 'array',
    ];

    /**
     * Relations
     */
    public function tourOperator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(OperatorUser::class, 'created_by_operator_user_id');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ActivityTranslation::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'activity_media')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('order');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class)->where('status', 'confirmed');
    }

    public function pendingRegistrations(): HasMany
    {
        return $this->hasMany(ActivityRegistration::class)->where('status', 'pending');
    }

    /**
     * Get translation for specific locale
     */
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()
            ->where('locale', $locale)
            ->first() ?? $this->translations()->where('locale', 'fr')->first();
    }

    /**
     * Accessors
     */
    public function getTitleAttribute()
    {
        return $this->translation()?->title ?? 'Sans titre';
    }

    public function getDescriptionAttribute()
    {
        return $this->translation()?->description ?? '';
    }

    public function getShortDescriptionAttribute()
    {
        return $this->translation()?->short_description ?? '';
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ').' '.$this->currency;
    }

    public function getFormattedDurationAttribute()
    {
        $duration = '';
        if ($this->duration_hours) {
            $duration .= $this->duration_hours.'h';
        }
        if ($this->duration_minutes) {
            $duration .= ($duration ? ' ' : '').$this->duration_minutes.'min';
        }

        return $duration ?: 'Non spécifié';
    }

    public function getDifficultyLabelAttribute()
    {
        $labels = [
            'easy' => 'Facile',
            'moderate' => 'Modéré',
            'difficult' => 'Difficile',
            'expert' => 'Expert',
        ];

        return $labels[$this->difficulty_level] ?? 'Non spécifié';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Brouillon</span>',
            'active' => '<span class="badge bg-success">Actif</span>',
            'inactive' => '<span class="badge bg-warning">Inactif</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getAgeRestrictionsTextAttribute()
    {
        if (! $this->has_age_restrictions) {
            return 'Aucune restriction';
        }

        $text = '';
        if ($this->min_age && $this->max_age) {
            $text = "De {$this->min_age} à {$this->max_age} ans";
        } elseif ($this->min_age) {
            $text = "À partir de {$this->min_age} ans";
        } elseif ($this->max_age) {
            $text = "Jusqu'à {$this->max_age} ans";
        }

        return $text ?: 'Restrictions définies';
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForOperator($query, $operatorId)
    {
        return $query->where('tour_operator_id', $operatorId);
    }

    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * Methods
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateParticipantsCount()
    {
        $count = $this->confirmedRegistrations()->sum('number_of_people');
        $this->update(['current_participants' => $count]);
    }

    public function hasAvailableSpots($requestedSpots = 1)
    {
        if (! $this->max_participants) {
            return true; // Pas de limite
        }

        return ($this->current_participants + $requestedSpots) <= $this->max_participants;
    }

    public function canUserRegister($userId)
    {
        // Vérifier si l'utilisateur a déjà une inscription active
        return ! $this->registrations()
            ->where('app_user_id', $userId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug if not provided
        static::creating(function ($activity) {
            if (empty($activity->slug)) {
                $activity->slug = \Str::slug($activity->title ?? 'activity-'.uniqid());
            }
        });
    }
}
