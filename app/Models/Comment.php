<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'app_user_id',
        'guest_name',
        'guest_email',
        'parent_id',
        'comment',
        'is_approved',
        'likes_count',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'likes_count' => 'integer',
    ];

    /**
     * Get the parent commentable model (POI, Event, Tour, TourOperator, Activity)
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who wrote the comment
     */
    public function appUser(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }

    /**
     * Get the parent comment (for nested comments)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * Get the likes for the comment
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Get the author name (user or guest)
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->appUser ? $this->appUser->name : ($this->guest_name ?? 'Anonyme');
    }

    /**
     * Check if the comment is liked by a user
     */
    public function isLikedBy($userId = null, $guestIdentifier = null): bool
    {
        $query = $this->likes();

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
     * Increment likes count
     */
    public function incrementLikesCount(): void
    {
        $this->increment('likes_count');
    }

    /**
     * Decrement likes count
     */
    public function decrementLikesCount(): void
    {
        if ($this->likes_count > 0) {
            $this->decrement('likes_count');
        }
    }

    /**
     * Scope: Only approved comments
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope: Only root comments (not replies)
     */
    public function scopeRootOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Order by most liked
     */
    public function scopeMostLiked($query)
    {
        return $query->orderBy('likes_count', 'desc');
    }
}
