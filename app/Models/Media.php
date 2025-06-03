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
    /* public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations()
                    ->where('locale', $locale)
                    ->first() 
                ?? $this->translations()
                      ->where('locale', config('app.fallback_locale'))
                      ->first();
    } */

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
     * Relation avec les POIs.
     */
    public function pois()
    {
        return $this->belongsToMany(Poi::class, 'media_poi')
                    ->withPivot('order');
    }
}