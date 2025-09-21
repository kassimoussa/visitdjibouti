<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TourSchedule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'available_spots',
        'booked_spots',
        'status',
        'guide_name',
        'guide_contact',
        'guide_languages',
        'special_notes',
        'weather_status',
        'meeting_point_override',
        'price_override',
        'cancellation_deadline',
        'created_by_admin_id'
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
        'available_spots' => 'integer',
        'booked_spots' => 'integer',
        'guide_languages' => 'array',
        'price_override' => 'decimal:2',
        'cancellation_deadline' => 'datetime'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($schedule) {
            if (is_null($schedule->booked_spots)) {
                $schedule->booked_spots = 0;
            }
            if (is_null($schedule->status)) {
                $schedule->status = 'available';
            }
        });
    }

    /**
     * Get the tour.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the admin who created this schedule.
     */
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'created_by_admin_id');
    }

    /**
     * Get reservations for this schedule.
     */
    public function reservations()
    {
        return $this->morphMany(Reservation::class, 'reservable');
    }

    /**
     * Get confirmed reservations.
     */
    public function confirmedReservations()
    {
        return $this->reservations()->confirmed();
    }

    /**
     * Get pending reservations.
     */
    public function pendingReservations()
    {
        return $this->reservations()->pending();
    }

    /**
     * Scopes
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
                    ->where('start_date', '>=', now()->toDateString());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now()->toDateString())
                    ->orderBy('start_date');
    }

    public function scopeByDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('start_date', [$startDate, $endDate]);
        }

        return $query->where('start_date', $startDate);
    }

    public function scopeWithAvailableSpots($query)
    {
        return $query->whereRaw('available_spots > booked_spots');
    }

    public function scopeByGuide($query, string $guideName)
    {
        return $query->where('guide_name', 'LIKE', "%{$guideName}%");
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->whereJsonContains('guide_languages', $language);
    }

    /**
     * Computed attributes
     */
    public function getRemainingSpots(): int
    {
        return max(0, $this->available_spots - $this->booked_spots);
    }

    public function getRemainingSpotAttribute(): int
    {
        return $this->getRemainingSpots();
    }

    public function getIsFullAttribute(): bool
    {
        return $this->booked_spots >= $this->available_spots;
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'available' &&
               !$this->is_full &&
               $this->start_date >= now()->toDateString();
    }

    public function getIsPastAttribute(): bool
    {
        return $this->start_date < now()->toDateString();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date >= now()->toDateString();
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return $this->status !== 'cancelled' &&
               $this->start_date > now()->toDateString();
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d/m/Y');
        }

        return $this->start_date->format('d/m/Y') . ' - ' . $this->end_date->format('d/m/Y');
    }

    public function getFormattedTimeRangeAttribute(): string
    {
        $start = $this->start_time ? $this->start_time->format('H:i') : '';
        $end = $this->end_time ? $this->end_time->format('H:i') : '';

        if ($start && $end) {
            return $start . ' - ' . $end;
        }

        return $start ?: $end ?: 'Horaire à confirmer';
    }

    public function getFormattedDateTimeAttribute(): string
    {
        return $this->formatted_date_range .
               ($this->formatted_time_range !== 'Horaire à confirmer' ?
                ' à ' . $this->formatted_time_range : '');
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->price_override ?? $this->tour->price;
    }

    public function getEffectiveMeetingPointAttribute(): string
    {
        return $this->meeting_point_override ?? $this->tour->meeting_point_address ?? '';
    }

    public function getGuideLanguagesTextAttribute(): string
    {
        if (!$this->guide_languages || !is_array($this->guide_languages)) {
            return '';
        }

        $languages = [
            'fr' => 'Français',
            'en' => 'Anglais',
            'ar' => 'Arabe',
            'es' => 'Espagnol',
            'it' => 'Italien',
            'de' => 'Allemand'
        ];

        $translatedLanguages = array_map(function($lang) use ($languages) {
            return $languages[$lang] ?? $lang;
        }, $this->guide_languages);

        return implode(', ', $translatedLanguages);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'available' => 'success',
            'full' => 'warning',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'available' => 'Disponible',
            'full' => 'Complet',
            'cancelled' => 'Annulé',
            'completed' => 'Terminé',
            default => 'Inconnu'
        };
    }

    /**
     * Methods
     */
    public function addBooking(int $spots = 1): bool
    {
        if ($this->getRemainingSpots() < $spots) {
            return false;
        }

        $this->increment('booked_spots', $spots);

        if ($this->getRemainingSpots() === 0) {
            $this->update(['status' => 'full']);
        }

        return true;
    }

    public function removeBooking(int $spots = 1): bool
    {
        if ($this->booked_spots < $spots) {
            return false;
        }

        $this->decrement('booked_spots', $spots);

        if ($this->status === 'full' && $this->getRemainingSpots() > 0) {
            $this->update(['status' => 'available']);
        }

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        $this->update([
            'status' => 'cancelled',
            'special_notes' => $this->special_notes .
                             ($reason ? "\n\nAnnulé: " . $reason : '')
        ]);

        // Cancel all related reservations
        $this->reservations()->whereIn('status', ['pending', 'confirmed'])
             ->update([
                 'status' => 'cancelled',
                 'cancellation_reason' => $reason ?? 'Tour annulé',
                 'cancelled_at' => now()
             ]);

        return true;
    }

    public function markAsCompleted(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    public function canAcceptReservation(int $spotsRequested = 1): bool
    {
        return $this->is_available &&
               $this->getRemainingSpots() >= $spotsRequested &&
               ($this->cancellation_deadline === null ||
                $this->cancellation_deadline > now());
    }

    public function meetsMinimumParticipants(): bool
    {
        $minParticipants = $this->tour->min_participants ?? 1;
        return $this->booked_spots >= $minParticipants;
    }

    public function getDaysUntilTour(): int
    {
        return now()->diffInDays($this->start_date, false);
    }

    public function getHoursUntilTour(): int
    {
        $tourDateTime = $this->start_date->copy();
        if ($this->start_time) {
            $tourDateTime = $tourDateTime->setTimeFrom($this->start_time);
        }

        return now()->diffInHours($tourDateTime, false);
    }
}