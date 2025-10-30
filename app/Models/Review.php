<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'poi_id',
        'app_user_id',
        'guest_name',
        'guest_email',
        'rating',
        'title',
        'comment',
        'is_verified',
        'is_approved',
        'helpful_count',
        'operator_response',
        'operator_response_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'helpful_count' => 'integer',
        'operator_response_at' => 'datetime',
    ];

    /**
     * Get the POI that owns the review
     */
    public function poi(): BelongsTo
    {
        return $this->belongsTo(Poi::class);
    }

    /**
     * Get the user who wrote the review
     */
    public function appUser(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }

    /**
     * Get the helpful votes for the review
     */
    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    /**
     * Get the author name (user or guest)
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->appUser ? $this->appUser->name : ($this->guest_name ?? 'Anonyme');
    }

    /**
     * Check if the review has been marked helpful by a user
     */
    public function isHelpfulBy($userId = null, $guestIdentifier = null): bool
    {
        $query = $this->helpfulVotes();

        if ($userId) {
            $query->where('app_user_id', $userId);
        } elseif ($guestIdentifier) {
            $query->where('guest_identifier', $guestIdentifier);
        } else {
            return false;
        }

        return $query->exists();
    }

    /**
     * Increment helpful count
     */
    public function incrementHelpfulCount(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Decrement helpful count
     */
    public function decrementHelpfulCount(): void
    {
        if ($this->helpful_count > 0) {
            $this->decrement('helpful_count');
        }
    }

    /**
     * Scope: Only approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Only verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: By rating
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope: Order by most helpful
     */
    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }
}
