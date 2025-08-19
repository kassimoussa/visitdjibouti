<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourOperatorTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_operator_id',
        'locale',
        'name',
        'description',
        'address_translated',
    ];

    /**
     * Relation avec l'opérateur de tour
     */
    public function tourOperator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class);
    }

    /**
     * Scope pour filtrer par locale
     */
    public function scopeForLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Obtenir le texte d'un champ avec fallback
     */
    public function getFieldWithFallback($field, $fallbackLocale = 'fr')
    {
        if ($this->$field) {
            return $this->$field;
        }

        // Si le champ est vide, chercher dans la langue de fallback
        if ($this->locale !== $fallbackLocale) {
            $fallbackTranslation = $this->tourOperator
                ->translations()
                ->where('locale', $fallbackLocale)
                ->first();
            
            if ($fallbackTranslation && $fallbackTranslation->$field) {
                return $fallbackTranslation->$field;
            }
        }

        return '';
    }
}