<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Embassy extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'country_code',
        'phones',
        'emails',
        'fax',
        'website',
        'ld',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Les types d'ambassades disponibles
     */
    public const TYPES = [
        'foreign_in_djibouti' => 'Ambassades étrangères à Djibouti',
        'djiboutian_abroad' => 'Ambassades djiboutiennes à l\'étranger',
    ];

    /**
     * Récupérer toutes les traductions
     */
    public function translations(): HasMany
    {
        return $this->hasMany(EmbassyTranslation::class);
    }
    
    /**
     * Obtenir la traduction dans la langue spécifiée
     */
    public function translation($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        return $this->translations()
                    ->where('locale', $locale)
                    ->first() 
                ?? $this->translations()
                      ->where('locale', config('app.fallback_locale', 'fr'))
                      ->first();
    }

    public function getTranslation(string $locale)
    {
        $translation = $this->translations->firstWhere('locale', $locale);
        
        if (!$translation) {
            $translation = $this->translations->firstWhere('locale', config('app.fallback_locale', 'fr'));
        }
        
        if (!$translation) {
            return new EmbassyTranslation([
                'name' => '',
                'ambassador_name' => '',
                'address' => '',
                'postal_box' => '',
            ]);
        }
        
        return $translation;
    }

    /**
     * Obtenir le nom traduit avec fallback
     */
    public function getTranslatedName(string $locale = 'fr')
    {
        $translation = $this->translations->firstWhere('locale', $locale);
        if ($translation && $translation->name) {
            return $translation->name;
        }
        
        // Fallback vers français
        $fallback = $this->translations->firstWhere('locale', 'fr');
        return $fallback ? $fallback->name : '';
    }

    /**
     * Obtenir le nom de l'ambassadeur traduit avec fallback
     */
    public function getTranslatedAmbassadorName(string $locale = 'fr')
    {
        $translation = $this->translations->firstWhere('locale', $locale);
        if ($translation && $translation->ambassador_name) {
            return $translation->ambassador_name;
        }
        
        // Fallback vers français
        $fallback = $this->translations->firstWhere('locale', 'fr');
        return $fallback ? $fallback->ambassador_name : '';
    }

    /**
     * Accesseurs pour les attributs traduits
     */
    public function getNameAttribute()
    {
        return $this->translation() ? $this->translation()->name : '';
    }
    
    public function getAmbassadorNameAttribute()
    {
        return $this->translation() ? $this->translation()->ambassador_name : '';
    }
    
    public function getAddressAttribute()
    {
        return $this->translation() ? $this->translation()->address : '';
    }
    
    public function getPostalBoxAttribute()
    {
        return $this->translation() ? $this->translation()->postal_box : '';
    }

    /**
     * Obtenir les téléphones sous forme de tableau
     */
    public function getPhonesArrayAttribute()
    {
        return $this->phones ? explode('|', $this->phones) : [];
    }

    /**
     * Obtenir les emails sous forme de tableau
     */
    public function getEmailsArrayAttribute()
    {
        return $this->emails ? explode('|', $this->emails) : [];
    }

    /**
     * Obtenir les LD sous forme de tableau
     */
    public function getLdArrayAttribute()
    {
        return $this->ld ? explode('|', $this->ld) : [];
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForeignInDjibouti($query)
    {
        return $query->where('type', 'foreign_in_djibouti');
    }

    public function scopeDjiboutianAbroad($query)
    {
        return $query->where('type', 'djiboutian_abroad');
    }

    /**
     * Obtenir le libellé du type
     */
    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    /**
     * Obtenir l'URL du site web formatée
     */
    public function getWebsiteUrlAttribute()
    {
        if (!$this->website) {
            return null;
        }

        // Si l'URL commence déjà par http:// ou https://, la retourner telle quelle
        if (preg_match('/^https?:\/\//', $this->website)) {
            return $this->website;
        }

        // Sinon, ajouter https:// par défaut
        return 'https://' . $this->website;
    }
}