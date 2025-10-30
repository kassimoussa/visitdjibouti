<?php

namespace App\Traits;

use App\Models\Comment;

trait Commentable
{
    /**
     * Get all comments for this model (polymorphic).
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get approved comments only.
     */
    public function approvedComments()
    {
        return $this->comments()->where('is_approved', true);
    }

    /**
     * Get root comments (not replies).
     */
    public function rootComments()
    {
        return $this->comments()->whereNull('parent_id');
    }

    /**
     * Get approved root comments.
     */
    public function approvedRootComments()
    {
        return $this->rootComments()->where('is_approved', true);
    }

    /**
     * Get comments count.
     */
    public function getCommentsCountAttribute(): int
    {
        return $this->approvedComments()->count();
    }
}
