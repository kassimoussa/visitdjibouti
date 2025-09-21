<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tour_id',
        'locale',
        'title',
        'description',
        'short_description',
        'itinerary',
        'meeting_point_description',
        'highlights',
        'what_to_bring',
        'cancellation_policy_text'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'highlights' => 'array',
        'what_to_bring' => 'array'
    ];

    /**
     * Get the tour that owns the translation.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Get the locale name.
     */
    public function getLocaleNameAttribute()
    {
        $locales = [
            'fr' => 'Français',
            'en' => 'English',
            'ar' => 'العربية',
        ];

        return $locales[$this->locale] ?? $this->locale;
    }

    /**
     * Check if the translation is complete.
     */
    public function getIsCompleteAttribute()
    {
        return !empty($this->title) && !empty($this->description);
    }

    /**
     * Get description excerpt.
     */
    public function getDescriptionExcerptAttribute($limit = 150)
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->description), $limit);
    }

    /**
     * Get formatted highlights as string.
     */
    public function getHighlightsTextAttribute(): string
    {
        if (!$this->highlights || !is_array($this->highlights)) {
            return '';
        }

        return implode(' • ', $this->highlights);
    }

    /**
     * Get formatted what to bring as string.
     */
    public function getWhatToBringTextAttribute(): string
    {
        if (!$this->what_to_bring || !is_array($this->what_to_bring)) {
            return '';
        }

        return implode(' • ', $this->what_to_bring);
    }
}