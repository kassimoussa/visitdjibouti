<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class NewsTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug'
    ];

    /**
     * Generate a unique slug for new tags
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            if (empty($tag->slug) && request()->has('translations')) {
                $defaultLocale = config('app.fallback_locale', 'fr');
                $name = request()->input("translations.{$defaultLocale}.name");
                if ($name) {
                    $tag->slug = Str::slug($name);
                    
                    // Ensure uniqueness
                    $originalSlug = $tag->slug;
                    $counter = 1;
                    while (static::where('slug', $tag->slug)->exists()) {
                        $tag->slug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                }
            }
        });
    }
    
    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(NewsTagTranslation::class);
    }
    
    /**
     * Obtenir la traduction dans la langue spécifiée.
     */
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations()
                    ->where('locale', $locale)
                    ->first() 
                ?? $this->translations()
                      ->where('locale', config('app.fallback_locale'))
                      ->first();
    }
    
    /**
     * Accesseurs pour les attributs traduits.
     */
    public function getNameAttribute()
    {
        return $this->translation() ? $this->translation()->name : '';
    }
    
    /**
     * Relation avec les actualités
     */
    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_news_tag');
    }
    
    /**
     * Compter les actualités
     */
    public function getNewsCountAttribute(): int
    {
        return $this->news()->published()->count();
    }
}