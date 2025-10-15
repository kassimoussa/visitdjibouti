<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationInfoTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organization_info_id',
        'locale',
        'name',
        'description',
        'opening_hours_translated',
    ];

    /**
     * Get the organization info that owns the translation.
     */
    public function organizationInfo(): BelongsTo
    {
        return $this->belongsTo(OrganizationInfo::class);
    }
}
