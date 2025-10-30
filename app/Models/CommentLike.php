<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'app_user_id',
        'guest_identifier',
    ];

    /**
     * Get the comment that owns the like
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the user who liked
     */
    public function appUser(): BelongsTo
    {
        return $this->belongsTo(AppUser::class);
    }
}
