<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventReview extends Model
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
        'rating',
        'title',
        'comment',
        'status',
        'admin_reply',
        'admin_reply_by',
        'admin_reply_at',
        'is_verified_attendee',
        'helpful_count',
        'report_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'integer',
        'is_verified_attendee' => 'boolean',
        'helpful_count' => 'integer',
        'report_count' => 'integer',
        'admin_reply_at' => 'datetime',
    ];

    /**
     * Get the event that this review belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who wrote this review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who replied to this review.
     */
    public function adminReplier(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_reply_by');
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'approved' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            'spam' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get rating stars as HTML.
     */
    public function getRatingStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }

        return $stars;
    }

    /**
     * Check if review is approved.
     */
    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if review has admin reply.
     */
    public function getHasAdminReplyAttribute()
    {
        return ! empty($this->admin_reply);
    }

    /**
     * Get review excerpt.
     */
    public function getCommentExcerptAttribute($limit = 100)
    {
        return \Illuminate\Support\Str::limit($this->comment, $limit);
    }

    /**
     * Scope for approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified attendees.
     */
    public function scopeVerifiedAttendees($query)
    {
        return $query->where('is_verified_attendee', true);
    }

    /**
     * Approve the review.
     */
    public function approve()
    {
        $this->update(['status' => 'approved']);
    }

    /**
     * Reject the review.
     */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * Add admin reply.
     */
    public function addAdminReply($reply, $adminId)
    {
        $this->update([
            'admin_reply' => $reply,
            'admin_reply_by' => $adminId,
            'admin_reply_at' => now(),
        ]);
    }

    /**
     * Increment helpful count.
     */
    public function markAsHelpful()
    {
        $this->increment('helpful_count');
    }

    /**
     * Increment report count.
     */
    public function report()
    {
        $this->increment('report_count');
    }
}
