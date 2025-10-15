<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',
        'locale',
        'title',
        'description',
        'short_description',
        'location_details',
        'requirements',
        'program',
        'additional_info',
    ];

    /**
     * Get the event that owns the translation.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
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
        return ! empty($this->title) && ! empty($this->description);
    }

    /**
     * Get description excerpt.
     */
    public function getDescriptionExcerptAttribute($limit = 150)
    {
        return \Illuminate\Support\Str::limit(strip_tags($this->description), $limit);
    }
}
