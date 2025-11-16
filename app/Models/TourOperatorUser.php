<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TourOperatorUser extends Authenticatable
{
    use CanResetPassword, HasApiTokens, HasFactory, Notifiable;

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
     * Get all activities managed by this operator user.
     */
    public function managedActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'tour_operator_id', 'tour_operator_id');
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
        // Utiliser une sous-requête SQL pour éviter les problèmes de cache
        return Reservation::whereIn('id', function ($query) {
            $query->select('r1.id')
                ->from('reservations as r1')
                ->leftJoin('events as e', function ($join) {
                    $join->on('r1.reservable_id', '=', 'e.id')
                        ->where('r1.reservable_type', '=', Event::class);
                })
                ->leftJoin('tour_schedules as ts', function ($join) {
                    $join->on('r1.reservable_id', '=', 'ts.id')
                        ->where('r1.reservable_type', '=', TourSchedule::class);
                })
                ->leftJoin('tours as t', 'ts.tour_id', '=', 't.id')
                ->where(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->where('r1.reservable_type', Event::class)
                            ->where('e.tour_operator_id', $this->tour_operator_id);
                    })
                        ->orWhere(function ($subQ) {
                            $subQ->where('r1.reservable_type', TourSchedule::class)
                                ->where('t.tour_operator_id', $this->tour_operator_id);
                        });
                })
                ->whereNull('r1.deleted_at');
        });
    }

    /**
     * Check if the operator user can manage events.
     * All active operator users have full access.
     */
    public function canManageEvents(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the operator user can manage tours.
     * All active operator users have full access.
     */
    public function canManageTours(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the operator user can manage activities.
     * All active operator users have full access.
     */
    public function canManageActivities(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the operator user can view reservations.
     * All active operator users have full access.
     */
    public function canViewReservations(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if the operator user can manage profile.
     * All active operator users have full access.
     */
    public function canManageProfile(): bool
    {
        return $this->is_active;
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
            'total_tours' => 0,
            'active_tours' => 0,
            'total_activities' => 0,
            'active_activities' => 0,
            'total_reservations' => 0,
            'confirmed_reservations' => 0,
            'pending_reservations' => 0,
            'revenue_this_month' => 0,
        ];

        if ($this->canManageTours()) {
            $stats['total_tours'] = $this->managedTours()->count();
            $stats['active_tours'] = $this->managedTours()
                ->where('status', 'active')
                ->count();
        }

        if ($this->canManageActivities()) {
            $stats['total_activities'] = $this->managedActivities()->count();
            $stats['active_activities'] = $this->managedActivities()
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
