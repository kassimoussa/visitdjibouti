<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Link extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_info_id',
        'url',
        'platform',
        'order',
    ];

    /**
     * Récupérer toutes les traductions.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(LinkTranslation::class, 'link_id');
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
     * Accesseur pour le nom traduit.
     */
    public function getNameAttribute()
    {
        return $this->translation() ? $this->translation()->name : '';
    }

    /**
     * Get the organization info that owns the link.
     */
    public function organizationInfo(): BelongsTo
    {
        return $this->belongsTo(OrganizationInfo::class);
    }

    /**
     * Get platform icon class for FontAwesome.
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'website' => 'fas fa-globe',
            'facebook' => 'fab fa-facebook',
            'instagram' => 'fab fa-instagram',
            'twitter' => 'fab fa-twitter',
            'linkedin' => 'fab fa-linkedin',
            'youtube' => 'fab fa-youtube',
            'tiktok' => 'fab fa-tiktok',
            'whatsapp' => 'fab fa-whatsapp',
        ];

        return $icons[$this->platform] ?? 'fas fa-link';
    }

    /**
     * Get platform color for display.
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'website' => 'primary',
            'facebook' => 'primary',
            'instagram' => 'danger',
            'twitter' => 'info',
            'linkedin' => 'primary',
            'youtube' => 'danger',
            'tiktok' => 'dark',
            'whatsapp' => 'success',
        ];

        return $colors[$this->platform] ?? 'secondary';
    }
}