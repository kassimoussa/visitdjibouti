<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoiTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'poi_id',
        'locale',
        'name',
        'description',
        'short_description',
        'address',
        'opening_hours',
        'entry_fee',
        'tips'
    ];

    /**
     * Get the POI that owns the translation.
     */
    public function poi(): BelongsTo
    {
        return $this->belongsTo(Poi::class);
    }
}