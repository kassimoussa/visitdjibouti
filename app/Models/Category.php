<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'slug',
        'icon',
        'color',
        'sort_order',
        'level',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Mutateur pour parent_id - convertit les chaînes vides en null
     */
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = empty($value) ? null : $value;
    }

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

            // Auto-calcul du niveau hiérarchique
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                $category->level = $parent ? $parent->level + 1 : 0;
            } else {
                $category->level = 0;
            }

            // Auto-définition de sort_order si pas défini
            if (! $category->sort_order) {
                $maxOrder = static::where('parent_id', $category->parent_id)->max('sort_order') ?? 0;
                $category->sort_order = $maxOrder + 1;
            }
        });

        static::updating(function ($category) {
            // Recalcul du niveau si le parent change
            if ($category->isDirty('parent_id')) {
                if ($category->parent_id) {
                    $parent = static::find($category->parent_id);
                    $category->level = $parent ? $parent->level + 1 : 0;
                } else {
                    $category->level = 0;
                }

                // Mettre à jour les niveaux des enfants
                $category->updateChildrenLevels();
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
     * Relations hiérarchiques
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('allChildren')->orderBy('sort_order');
    }

    /**
     * Scopes pour la hiérarchie
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    public function scopeChildrenOf($query, $parentId)
    {
        return $query->where('parent_id', $parentId)->orderBy('sort_order');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level)->orderBy('sort_order');
    }

    /**
     * Méthodes utilitaires pour la hiérarchie
     */
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function isChild(): bool
    {
        return $this->parent_id !== null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function isDescendantOf(Category $category): bool
    {
        if ($this->parent_id === $category->id) {
            return true;
        }

        if ($this->parent) {
            return $this->parent->isDescendantOf($category);
        }

        return false;
    }

    public function getAncestors()
    {
        $ancestors = collect();
        $current = $this->parent;

        while ($current) {
            $ancestors->prepend($current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    public function getBreadcrumb($separator = ' > ')
    {
        $ancestors = $this->getAncestors();
        $ancestors->push($this);

        return $ancestors->pluck('name')->implode($separator);
    }

    public function updateChildrenLevels()
    {
        $this->children->each(function ($child) {
            $child->level = $this->level + 1;
            $child->save();
            $child->updateChildrenLevels();
        });
    }

    public function getTreeAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'level' => $this->level,
            'sort_order' => $this->sort_order,
            'children' => $this->children->map(function ($child) {
                return $child->tree;
            }),
        ];
    }

    /**
     * Relation avec les POIs.
     */
    public function pois()
    {
        return $this->belongsToMany(Poi::class, 'category_poi');
    }

    /**
     * Relation avec les événements.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class, 'category_event');
    }
}
