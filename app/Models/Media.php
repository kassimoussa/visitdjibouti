<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'thumbnail_path',
        'type',
        'dimensions',
        'is_optimized',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'is_optimized' => 'boolean',
    ];
    
    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(MediaTranslation::class);
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

    public function getTranslation(string $locale)
    {
        // Essayer de trouver la traduction dans la langue demandée
        $translation = $this->translations->firstWhere('locale', $locale);
        
        // Si pas trouvé, utiliser la traduction française (par défaut)
        if (!$translation) {
            $translation = $this->translations->firstWhere('locale', config('app.fallback_locale', 'fr'));
        }
        
        // Si toujours pas de traduction, renvoyer un objet vide
        if (!$translation) {
            return new MediaTranslation([
                'title' => $this->filename,
                'alt_text' => '',
                'description' => '',
            ]);
        }
        
        return $translation;
    }

    
    /**
     * Accesseurs pour les attributs traduits.
     */
    public function getTitleAttribute()
    {
        return $this->translation() ? $this->translation()->title : '';
    }
    
    public function getAltTextAttribute()
    {
        return $this->translation() ? $this->translation()->alt_text : '';
    }
    
    public function getDescriptionAttribute()
    {
        return $this->translation() ? $this->translation()->description : '';
    }
    
    /**
     * Obtenir l'URL complète du média
     */
    public function getUrlAttribute()
    {
        if (!$this->path) {
            return null;
        }
        
        // Si le path commence par http, c'est déjà une URL complète
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        
        // Si le path commence déjà par storage/, utiliser asset directement
        if (str_starts_with($this->path, 'storage/')) {
            return asset($this->path);
        }
        
        // Si le path commence par media/, ajouter storage/
        if (str_starts_with($this->path, 'media/')) {
            return asset('storage/' . $this->path);
        }
        
        // Pour tous les autres cas, construire le chemin complet
        return asset('storage/media/' . ltrim($this->path, '/'));
    }
    
    /**
     * Obtenir l'URL de la vignette
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail_path) {
            return $this->url; // Retour à l'image principale si pas de vignette
        }
        
        // Si le path commence par http, c'est déjà une URL complète
        if (str_starts_with($this->thumbnail_path, 'http')) {
            return $this->thumbnail_path;
        }
        
        // Si le path commence déjà par storage/, utiliser asset directement
        if (str_starts_with($this->thumbnail_path, 'storage/')) {
            return asset($this->thumbnail_path);
        }
        
        // Si le path commence par media/, ajouter storage/
        if (str_starts_with($this->thumbnail_path, 'media/')) {
            return asset('storage/' . $this->thumbnail_path);
        }
        
        // Pour tous les autres cas, construire le chemin complet
        return asset('storage/media/' . ltrim($this->thumbnail_path, '/'));
    }
    
    /**
     * Méthode getImageUrl() pour la compatibilité avec les contrôleurs API
     */
    public function getImageUrl()
    {
        return $this->url;
    }
    
    /**
     * Relation avec les POIs.
     */
    public function pois()
    {
        return $this->belongsToMany(Poi::class, 'media_poi')
                    ->withPivot('order');
    }
}