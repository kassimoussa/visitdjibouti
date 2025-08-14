<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'news_id',
        'locale',
        'title',
        'excerpt',
        'meta_title',
        'meta_description',
        'seo_keywords'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'seo_keywords' => 'array',
    ];

    /**
     * Get the news that owns the translation.
     */
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}