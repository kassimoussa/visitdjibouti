<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'icon',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Generate a unique slug for new categories
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug) && request()->has('translations')) {
                $defaultLocale = config('app.fallback_locale', 'fr');
                $name = request()->input("translations.{$defaultLocale}.name");
                if ($name) {
                    $category->slug = Str::slug($name);
                }
            }
        });
    }
    
    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
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
    
    public function getDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->description : '';
    }
    
    /**
     * Relation avec les POIs.
     */
    public function pois()
    {
        return $this->belongsToMany(Poi::class, 'category_poi');
    }
}