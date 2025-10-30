<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewHelpfulVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'app_user_id',
        'guest_identifier',
    ];

    /**
     * Get the review that owns the vote
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * Get the user who voted
     */
    public function appUser(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }
}
