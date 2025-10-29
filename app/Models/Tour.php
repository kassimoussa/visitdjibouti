<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'created_by_operator_user_id',
        'approved_by_admin_id',
        'type',
        'target_id',
        'target_type',
        'start_date',
        'end_date',
        'price',
        'currency',
        'max_participants',
        'min_participants',
        'duration_hours',
        'age_restriction_min',
        'age_restriction_max',
        'current_participants',
        'difficulty_level',
        'includes',
        'requirements',
        'meeting_point_latitude',
        'meeting_point_longitude',
        'meeting_point_address',
        'status',
        'submitted_at',
        'approved_at',
        'rejection_reason',
        'is_featured',
        'weather_dependent',
        'cancellation_policy',
        'featured_image_id',
        'views_count',
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
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tour) {
            // Générer un slug si vide
            if (empty($tour->slug)) {
                $tour->slug = 'tour-'.uniqid();
            }
        });

        static::updating(function ($tour) {
            // Si le tour est approuvé et qu'il y a des modifications substantielles,
            // repasser en pending_approval (sauf si la modification vient d'un admin)
            if ($tour->isDirty() &&
                $tour->getOriginal('status') === 'approved' &&
                $tour->created_by_operator_user_id !== null &&
                !auth()->guard('admin')->check()) {

                // Vérifier si ce sont des modifications substantielles (pas juste views_count)
                $substantialFields = [
                    'type', 'target_id', 'target_type', 'start_date', 'end_date',
                    'price', 'max_participants', 'min_participants', 'duration_hours',
                    'difficulty_level', 'meeting_point_latitude', 'meeting_point_longitude'
                ];

                $hasSubstantialChanges = collect($substantialFields)
                    ->some(fn($field) => $tour->isDirty($field));

                if ($hasSubstantialChanges) {
                    $tour->status = 'pending_approval';
                    $tour->submitted_at = now();
                    $tour->approved_at = null;
                    $tour->approved_by_admin_id = null;
                }
            }
        });
    }

    /**
     * Mettre à jour le slug basé sur le titre de la traduction française
     */
    public function updateSlugFromTranslation()
    {
        $translation = $this->translations()->where('locale', 'fr')->first();
        if ($translation && $translation->title) {
            $slug = Str::slug($translation->title);
            // Assurer l'unicité du slug
            $count = 1;
            $originalSlug = $slug;
            while (self::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
                $slug = $originalSlug.'-'.$count;
                $count++;
            }
            $this->update(['slug' => $slug]);
        }
    }

    /**
     * Get all translations.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TourTranslation::class, 'tour_id');
    }

    /**
     * Get translation for specific locale.
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
     * Translated attributes accessors.
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

    public function getItineraryAttribute()
    {
        return $this->translation() ? $this->translation()->itinerary : '';
    }

    public function getMeetingPointDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->meeting_point_description : '';
    }

    /**
     * Get the tour operator.
     */
    public function tourOperator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class);
    }

    /**
     * Get the target (POI or Event).
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the featured image.
     */
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    /**
     * Get the media (images) for this tour.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_tour')
            ->withPivot('order')
            ->orderBy('order');
    }

    /**
     * Get all reservations for this tour (new system).
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(TourReservation::class, 'tour_id');
    }

    /**
     * Get confirmed reservations.
     */
    public function confirmedReservations()
    {
        return $this->reservations()->where('status', 'completed');
    }

    /**
     * Get the operator user who created this tour.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(TourOperatorUser::class, 'created_by_operator_user_id');
    }

    /**
     * Get the admin who approved this tour.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'approved_by_admin_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval')
            ->orderBy('submitted_at', 'desc');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeByOperator($query, int $operatorId)
    {
        return $query->where('tour_operator_id', $operatorId);
    }

    public function scopeInPriceRange($query, ?float $minPrice = null, ?float $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    public function scopeByDuration($query, ?int $maxHours = null, ?int $maxDays = null)
    {
        if ($maxHours !== null) {
            $query->where('duration_hours', '<=', $maxHours);
        }
        if ($maxDays !== null) {
            $query->where('duration_days', '<=', $maxDays);
        }

        return $query;
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->selectRaw('
                *,
                (6371 * acos(cos(radians(?)) * cos(radians(meeting_point_latitude)) * cos(radians(meeting_point_longitude) - radians(?)) + sin(radians(?)) * sin(radians(meeting_point_latitude)))) AS distance
            ', [$latitude, $longitude, $latitude])
            ->whereNotNull('meeting_point_latitude')
            ->whereNotNull('meeting_point_longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    /**
     * Computed attributes
     */
    public function getFormattedDurationAttribute(): string
    {
        $parts = [];

        // Calculer la durée en jours basée sur les dates
        if ($this->start_date && $this->end_date) {
            $days = $this->start_date->diffInDays($this->end_date) + 1; // +1 pour inclure le jour de fin
            if ($days > 0) {
                $parts[] = $days.' jour'.($days > 1 ? 's' : '');
            }
        }

        if ($this->duration_hours > 0) {
            $parts[] = $this->duration_hours.' heure'.($this->duration_hours > 1 ? 's' : '');
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
        if (! $this->start_date) {
            return 'Dates non définies';
        }

        if (! $this->end_date || $this->start_date->eq($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }

        return $this->start_date->format('d/m/Y').' - '.$this->end_date->format('d/m/Y');
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price == 0) {
            return 'Gratuit';
        }

        return number_format($this->price, 0, ',', ' ').' '.($this->currency ?? 'DJF');
    }

    public function getIsFreeAttribute(): bool
    {
        return $this->price == 0;
    }

    public function getHasAgeRestrictionsAttribute(): bool
    {
        return $this->age_restriction_min > 0 || $this->age_restriction_max > 0;
    }

    public function getAgeRestrictionsTextAttribute(): string
    {
        if (! $this->has_age_restrictions) {
            return 'Tous âges';
        }

        $parts = [];
        if ($this->age_restriction_min > 0) {
            $parts[] = "à partir de {$this->age_restriction_min} ans";
        }
        if ($this->age_restriction_max > 0) {
            $parts[] = "jusqu'à {$this->age_restriction_max} ans";
        }

        return implode(', ', $parts);
    }

    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->max_participants - $this->current_participants);
    }

    /**
     * Methods
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function isAvailableForBooking(): bool
    {
        return $this->status === 'active' && $this->available_spots > 0;
    }

    /**
     * Get difficulty level label.
     */
    public function getDifficultyLabelAttribute(): string
    {
        return match ($this->difficulty_level) {
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
        return match ($this->type) {
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

    /**
     * Approval Workflow Methods
     */

    /**
     * Submit tour for admin approval.
     */
    public function submitForApproval(): bool
    {
        if (!in_array($this->status, ['draft', 'rejected'])) {
            return false;
        }

        return $this->update([
            'status' => 'pending_approval',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Approve tour by admin.
     */
    public function approve(int $adminId): bool
    {
        if ($this->status !== 'pending_approval') {
            return false;
        }

        return $this->update([
            'status' => 'approved',
            'approved_by_admin_id' => $adminId,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject tour by admin.
     */
    public function reject(int $adminId, string $reason): bool
    {
        if ($this->status !== 'pending_approval') {
            return false;
        }

        return $this->update([
            'status' => 'rejected',
            'approved_by_admin_id' => $adminId,
            'approved_at' => null,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Check if tour is pending approval.
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if tour is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if tour is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if tour is in draft status.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get status label with color badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft' => '<span class="badge bg-secondary">Brouillon</span>',
            'pending_approval' => '<span class="badge bg-warning">En attente d\'approbation</span>',
            'approved' => '<span class="badge bg-success">Approuvé</span>',
            'rejected' => '<span class="badge bg-danger">Rejeté</span>',
            'active' => '<span class="badge bg-primary">Actif</span>',
            'inactive' => '<span class="badge bg-secondary">Inactif</span>',
            'suspended' => '<span class="badge bg-warning">Suspendu</span>',
            'archived' => '<span class="badge bg-dark">Archivé</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>'
        };
    }

    /**
     * Get status label text only.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Brouillon',
            'pending_approval' => 'En attente d\'approbation',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
            'archived' => 'Archivé',
            default => ucfirst($this->status)
        };
    }
}
