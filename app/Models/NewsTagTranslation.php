<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsTagTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'news_tag_id',
        'locale',
        'name'
    ];

    /**
     * Get the news tag that owns the translation.
     */
    public function newsTag(): BelongsTo
    {
        return $this->belongsTo(NewsTag::class);
    }
}