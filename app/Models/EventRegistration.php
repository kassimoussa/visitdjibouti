<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRegistration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'user_name',
        'user_email',
        'user_phone',
        'participants_count',
        'status',
        'registration_number',
        'payment_status',
        'payment_amount',
        'payment_reference',
        'special_requirements',
        'notes',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'participants_count' => 'integer',
        'payment_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Generate unique registration number on creation.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($registration) {
            if (empty($registration->registration_number)) {
                $registration->registration_number = 'REG-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the event for this registration.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user for this registration (if registered user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            'attended' => 'info',
            'no_show' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get payment status badge class.
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Check if registration is confirmed.
     */
    public function getIsConfirmedAttribute()
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if payment is completed.
     */
    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get total amount for this registration.
     */
    public function getTotalAmountAttribute()
    {
        if (!$this->event->price) {
            return 0;
        }
        
        return $this->event->price * $this->participants_count;
    }

    /**
     * Scope for confirmed registrations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for pending registrations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid registrations.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Confirm the registration.
     */
    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
        $this->event->addParticipant();
    }

    /**
     * Cancel the registration.
     */
    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
        
        $this->event->removeParticipant();
    }
}