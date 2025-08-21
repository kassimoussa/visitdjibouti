<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reservable_id',
        'reservable_type',
        'app_user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'reservation_date',
        'reservation_time',
        'number_of_people',
        'status',
        'confirmation_number',
        'contact_info',
        'special_requirements',
        'notes',
        'payment_status',
        'payment_amount',
        'payment_reference',
        'cancelled_at',
        'cancellation_reason',
        'reminder_sent_at',
        'confirmation_sent_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'contact_info' => 'array',
        'payment_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Génération automatique du numéro de confirmation
        static::creating(function ($reservation) {
            if (empty($reservation->confirmation_number)) {
                $reservation->confirmation_number = $reservation->generateConfirmationNumber();
            }
        });
    }

    /**
     * Get the reservable model (POI or Event).
     */
    public function reservable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who made the reservation.
     */
    public function appUser(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    /**
     * Generate a unique confirmation number.
     */
    private function generateConfirmationNumber(): string
    {
        $prefix = $this->reservable_type === Poi::class ? 'POI' : 'EVT';
        
        do {
            $number = $prefix . '-' . strtoupper(Str::random(8));
        } while (self::where('confirmation_number', $number)->exists());
        
        return $number;
    }

    /**
     * Check if the reservation is for a guest (non-authenticated user).
     */
    public function isGuestReservation(): bool
    {
        return is_null($this->app_user_id);
    }

    /**
     * Get the user's name (from user or guest).
     */
    public function getUserNameAttribute(): string
    {
        if ($this->appUser) {
            return $this->appUser->name;
        }
        
        return $this->guest_name ?? '';
    }

    /**
     * Get the user's email (from user or guest).
     */
    public function getUserEmailAttribute(): string
    {
        if ($this->appUser) {
            return $this->appUser->email;
        }
        
        return $this->guest_email ?? '';
    }

    /**
     * Get the user's phone (from user or guest).
     */
    public function getUserPhoneAttribute(): string
    {
        if ($this->appUser && $this->appUser->phone) {
            return $this->appUser->phone;
        }
        
        return $this->guest_phone ?? '';
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by confirmed reservations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope to filter by pending reservations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter by cancelled reservations.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope to filter by user (authenticated).
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('app_user_id', $userId);
    }

    /**
     * Scope to filter by guest email.
     */
    public function scopeForGuest($query, string $email)
    {
        return $query->where('guest_email', $email)->whereNull('app_user_id');
    }

    /**
     * Scope to filter by reservable type (POI or Event).
     */
    public function scopeForType($query, string $type)
    {
        return $query->where('reservable_type', $type);
    }

    /**
     * Scope to filter POI reservations.
     */
    public function scopeForPois($query)
    {
        return $query->where('reservable_type', Poi::class);
    }

    /**
     * Scope to filter Event reservations.
     */
    public function scopeForEvents($query)
    {
        return $query->where('reservable_type', Event::class);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('reservation_date', [$startDate, $endDate]);
        }
        
        return $query->where('reservation_date', $startDate);
    }

    /**
     * Scope to filter upcoming reservations.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', now()->toDateString())
                    ->whereNotIn('status', ['cancelled', 'completed']);
    }

    /**
     * Cancel the reservation.
     */
    public function cancel(string $reason = null): bool
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        
        if ($reason) {
            $this->cancellation_reason = $reason;
        }
        
        return $this->save();
    }

    /**
     * Confirm the reservation.
     */
    public function confirm(): bool
    {
        $this->status = 'confirmed';
        $this->confirmation_sent_at = now();
        
        return $this->save();
    }

    /**
     * Mark reservation as completed.
     */
    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        
        return $this->save();
    }

    /**
     * Check if reservation can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) 
            && $this->reservation_date > now()->toDateString();
    }

    /**
     * Check if reservation is active (not cancelled or completed).
     */
    public function isActive(): bool
    {
        return !in_array($this->status, ['cancelled', 'completed']);
    }

    /**
     * Check if reservation requires payment.
     */
    public function requiresPayment(): bool
    {
        return !is_null($this->payment_amount) 
            && $this->payment_amount > 0 
            && $this->payment_status !== 'not_required';
    }

    /**
     * Check if payment is completed.
     */
    public function isPaymentCompleted(): bool
    {
        return $this->payment_status === 'paid';
    }
}