<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourOperatorUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tour_operator_id',
        'name',
        'username',
        'email',
        'password',
        'phone_number',
        'position',
        'avatar',
        'is_active',
        'language_preference',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'permissions' => 'array',
    ];

    /**
     * Get the email address used for password resets.
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    /**
     * Find the user for authentication by username or email.
     */
    public static function findForAuth(string $login): ?self
    {
        return static::where('username', $login)
            ->orWhere('email', $login)
            ->first();
    }

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    /**
     * Get the tour operator that owns this user.
     */
    public function tourOperator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class);
    }

    /**
     * Get all events managed by this operator user.
     */
    public function managedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'tour_operator_id', 'tour_operator_id');
    }

    /**
     * Get all tours managed by this operator user.
     */
    public function managedTours(): HasMany
    {
        return $this->hasMany(Tour::class, 'tour_operator_id', 'tour_operator_id');
    }

    /**
     * Get all tour schedules managed by this operator user.
     */
    public function managedTourSchedules()
    {
        return TourSchedule::whereHas('tour', function ($query) {
            $query->where('tour_operator_id', $this->tour_operator_id);
        });
    }

    /**
     * Get all reservations for this operator's events and tours.
     */
    public function managedReservations()
    {
        return Reservation::where(function ($query) {
            // Reservations for events
            $query->where('reservable_type', Event::class)
                  ->whereHas('reservable', function ($q) {
                      $q->where('tour_operator_id', $this->tour_operator_id);
                  });
        })->orWhere(function ($query) {
            // Reservations for tour schedules
            $query->where('reservable_type', TourSchedule::class)
                  ->whereHas('reservable.tour', function ($q) {
                      $q->where('tour_operator_id', $this->tour_operator_id);
                  });
        });
    }

    /**
     * Check if the operator user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if (!is_array($this->permissions)) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Check if the operator user can manage events.
     */
    public function canManageEvents(): bool
    {
        return $this->hasPermission('manage_events') || $this->hasPermission('all');
    }

    /**
     * Check if the operator user can manage tours.
     */
    public function canManageTours(): bool
    {
        return $this->hasPermission('manage_tours') || $this->hasPermission('all');
    }

    /**
     * Check if the operator user can view reservations.
     */
    public function canViewReservations(): bool
    {
        return $this->hasPermission('view_reservations') || $this->hasPermission('all');
    }

    /**
     * Check if the operator user can manage profile.
     */
    public function canManageProfile(): bool
    {
        return $this->hasPermission('manage_profile') || $this->hasPermission('all');
    }

    /**
     * Get display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get preferred language for the interface.
     */
    public function getPreferredLanguageAttribute(): string
    {
        return $this->language_preference ?? config('app.fallback_locale', 'fr');
    }

    /**
     * Record the last login timestamp.
     */
    public function recordLogin(): void
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Get statistics for this operator user.
     */
    public function getStatistics(): array
    {
        $stats = [
            'total_events' => 0,
            'active_events' => 0,
            'total_tours' => 0,
            'active_tours' => 0,
            'total_reservations' => 0,
            'confirmed_reservations' => 0,
            'pending_reservations' => 0,
            'revenue_this_month' => 0,
        ];

        if ($this->canManageEvents()) {
            $stats['total_events'] = $this->managedEvents()->count();
            $stats['active_events'] = $this->managedEvents()
                ->where('status', 'published')
                ->where('start_date', '>=', now()->toDateString())
                ->count();
        }

        if ($this->canManageTours()) {
            $stats['total_tours'] = $this->managedTours()->count();
            $stats['active_tours'] = $this->managedTours()
                ->where('status', 'active')
                ->count();
        }

        if ($this->canViewReservations()) {
            $reservations = $this->managedReservations();
            $stats['total_reservations'] = $reservations->count();
            $stats['confirmed_reservations'] = $reservations->confirmed()->count();
            $stats['pending_reservations'] = $reservations->pending()->count();

            $stats['revenue_this_month'] = $reservations
                ->confirmed()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('payment_amount');
        }

        return $stats;
    }

    /**
     * Scope to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by tour operator.
     */
    public function scopeForOperator($query, int $operatorId)
    {
        return $query->where('tour_operator_id', $operatorId);
    }
}