<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationInfo extends Model
{
    use HasFactory;

    protected $table = 'organization_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'logo_id',
        'email',
        'phone',
        'address',
        'opening_hours',
    ];

    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(OrganizationInfoTranslation::class, 'organization_info_id');
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
     * Get translated name for a specific locale.
     */
    public function getTranslatedName($locale = null)
    {
        $translation = $this->translation($locale);

        return $translation ? $translation->name : '';
    }

    /**
     * Get translated description for a specific locale.
     */
    public function getTranslatedDescription($locale = null)
    {
        $translation = $this->translation($locale);

        return $translation ? $translation->description : '';
    }

    public function getOpeningHoursTranslatedAttribute()
    {
        $translation = $this->translation();

        // Si on a une traduction des horaires, on l'utilise, sinon on prend les horaires par défaut
        if ($translation && $translation->opening_hours_translated) {
            return $translation->opening_hours_translated;
        }

        return $this->opening_hours;
    }

    /**
     * Get translated opening hours for a specific locale.
     */
    public function getTranslatedOpeningHours($locale = null)
    {
        $translation = $this->translation($locale);

        // Si on a une traduction des horaires pour cette locale, on l'utilise
        if ($translation && $translation->opening_hours_translated) {
            return $translation->opening_hours_translated;
        }

        // Sinon on retourne les horaires par défaut
        return $this->opening_hours;
    }

    /**
     * Get the logo.
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    /**
     * Get the links for this organization.
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class, 'organization_info_id')->orderBy('order');
    }

    /**
     * Get website link.
     */
    public function websiteLink()
    {
        return $this->links()->where('platform', 'website')->first();
    }

    /**
     * Get social media links.
     */
    public function socialLinks()
    {
        return $this->links()->whereIn('platform', ['facebook', 'instagram', 'twitter', 'linkedin', 'youtube']);
    }
}
