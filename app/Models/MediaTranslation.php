<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_id',
        'locale',
        'title',
        'alt_text',
        'description',
    ];

    /**
     * Get the media that owns the translation.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
