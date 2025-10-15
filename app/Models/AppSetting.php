<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'type',
        'media_id',
        'content',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the media file associated with this setting.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /**
     * Scope to get only active settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get settings by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get translated content for a specific locale.
     */
    public function getTranslatedContent(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'fr');

        $content = $this->content ?? [];

        // If content has translations structure
        if (isset($content['translations']) && is_array($content['translations'])) {
            // Try to get content in requested locale
            $translatedContent = $content['translations'][$locale] ?? $content['translations'][$fallbackLocale] ?? [];

            // Merge with other non-translated content
            $result = array_merge($content, $translatedContent);
            unset($result['translations']); // Remove translations key from final result

            return $result;
        }

        // If content has direct locale keys
        foreach ($content as $key => $value) {
            if (is_array($value) && isset($value[$locale])) {
                $content[$key] = $value[$locale];
            } elseif (is_array($value) && isset($value[$fallbackLocale])) {
                $content[$key] = $value[$fallbackLocale];
            }
        }

        return $content;
    }

    /**
     * Get setting value for a specific key and locale.
     */
    public static function getValue(string $key, ?string $locale = null): mixed
    {
        $setting = static::active()->where('key', $key)->first();

        if (! $setting) {
            return null;
        }

        return $setting->getTranslatedContent($locale);
    }

    /**
     * Get all active settings grouped by type.
     */
    public static function getAllByType(?string $locale = null): array
    {
        $settings = static::active()->with('media')->get();

        $result = [];

        foreach ($settings as $setting) {
            $translatedContent = $setting->getTranslatedContent($locale);

            // Add media URL if media exists
            if ($setting->media) {
                $translatedContent['media_url'] = $setting->media->url;
                $translatedContent['thumbnail_url'] = $setting->media->thumbnail_url;
            }

            $result[$setting->type][$setting->key] = $translatedContent;
        }

        return $result;
    }

    /**
     * Get all active settings as flat array.
     */
    public static function getAllFlat(?string $locale = null): array
    {
        $settings = static::active()->with('media')->get();

        $result = [];

        foreach ($settings as $setting) {
            $translatedContent = $setting->getTranslatedContent($locale);

            // Add media URL if media exists
            if ($setting->media) {
                $translatedContent['media_url'] = $setting->media->url;
                $translatedContent['thumbnail_url'] = $setting->media->thumbnail_url;
            }

            $result[$setting->key] = $translatedContent;
        }

        return $result;
    }

    /**
     * Set or update a setting.
     */
    public static function setSetting(string $key, string $type, array $content, ?int $mediaId = null, bool $isActive = true): AppSetting
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'type' => $type,
                'content' => $content,
                'media_id' => $mediaId,
                'is_active' => $isActive,
            ]
        );
    }
}
