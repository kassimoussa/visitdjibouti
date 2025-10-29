<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_id',
        'app_user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'number_of_people',
        'preferred_date',
        'special_requirements',
        'medical_conditions',
        'status',
        'total_price',
        'payment_status',
        'payment_method',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'total_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function appUser(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'confirmed' => 'Confirmé',
            'completed' => 'Terminé',
            'cancelled_by_user' => 'Annulé par l\'utilisateur',
            'cancelled_by_operator' => 'Annulé par l\'opérateur',
        ];

        return $labels[$this->status] ?? 'Inconnu';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">En attente</span>',
            'confirmed' => '<span class="badge bg-success">Confirmé</span>',
            'completed' => '<span class="badge bg-info">Terminé</span>',
            'cancelled_by_user' => '<span class="badge bg-danger">Annulé</span>',
            'cancelled_by_operator' => '<span class="badge bg-secondary">Annulé</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'En attente',
            'paid' => 'Payé',
            'refunded' => 'Remboursé',
        ];

        return $labels[$this->payment_status] ?? 'Inconnu';
    }

    public function getCustomerNameAttribute()
    {
        return $this->appUser?->name ?? $this->guest_name ?? 'N/A';
    }

    public function getCustomerEmailAttribute()
    {
        return $this->appUser?->email ?? $this->guest_email ?? 'N/A';
    }

    public function getCustomerPhoneAttribute()
    {
        return $this->appUser?->phone ?? $this->guest_phone ?? 'N/A';
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->whereIn('status', ['cancelled_by_user', 'cancelled_by_operator']);
    }

    public function scopeForActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('app_user_id', $userId);
    }

    /**
     * Methods
     */
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Mettre à jour le nombre de participants
        $this->activity->updateParticipantsCount();

        return true;
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return true;
    }

    public function cancel($reason = null, $cancelledBy = 'user')
    {
        $status = $cancelledBy === 'operator' ? 'cancelled_by_operator' : 'cancelled_by_user';

        $this->update([
            'status' => $status,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        // Mettre à jour le nombre de participants si c'était confirmé
        if ($this->getOriginal('status') === 'confirmed') {
            $this->activity->updateParticipantsCount();
        }

        return true;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled()
    {
        return in_array($this->status, ['cancelled_by_user', 'cancelled_by_operator']);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Calculer le prix total lors de la création
        static::creating(function ($registration) {
            if (! $registration->total_price || $registration->total_price == 0) {
                $registration->total_price = $registration->activity->price * $registration->number_of_people;
            }
        });
    }
}
