<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Ajout pour compatibilité Livewire simple
        'slug',
        'description', // Ajout pour compatibilité Livewire simple
        'icon',
        'color',
        'parent_id',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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
                    
                    // Ensure uniqueness
                    $originalSlug = $category->slug;
                    $counter = 1;
                    while (static::where('slug', $category->slug)->exists()) {
                        $category->slug = $originalSlug . '-' . $counter;
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
        return $this->hasMany(NewsCategoryTranslation::class);
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
    
    public function getMetaTitleAttribute()
    {
        return $this->translation() ? $this->translation()->meta_title : '';
    }
    
    public function getMetaDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->meta_description : '';
    }
    
    /**
     * Relations hiérarchiques
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'parent_id');
    }
    
    public function children(): HasMany
    {
        return $this->hasMany(NewsCategory::class, 'parent_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order');
    }
    
    public function allChildren(): HasMany
    {
        return $this->hasMany(NewsCategory::class, 'parent_id')
                    ->orderBy('sort_order');
    }
    
    /**
     * Relation avec les actualités (one-to-many principale)
     */
    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
    
    /**
     * Relation avec les actualités (many-to-many pour catégories multiples)
     */
    public function newsArticles(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_news_category');
    }
    
    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
    
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
    
    /**
     * Méthodes utilitaires
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }
    
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }
    
    public function getDepthAttribute(): int
    {
        $depth = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }
        
        return $depth;
    }
    
    public function getBreadcrumbAttribute(): array
    {
        $breadcrumb = [$this];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($breadcrumb, $parent);
            $parent = $parent->parent;
        }
        
        return $breadcrumb;
    }
    
    public function getFullNameAttribute(): string
    {
        $breadcrumb = $this->breadcrumb;
        return implode(' > ', array_map(fn($cat) => $cat->name, $breadcrumb));
    }
    
    /**
     * Compter les actualités
     */
    public function getNewsCountAttribute(): int
    {
        return $this->news()->published()->count();
    }
    
    public function getTotalNewsCountAttribute(): int
    {
        $count = $this->news_count;
        
        foreach ($this->children as $child) {
            $count += $child->total_news_count;
        }
        
        return $count;
    }
}