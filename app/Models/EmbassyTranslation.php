<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmbassyTranslation extends Model
{
    protected $fillable = [
        'embassy_id',
        'locale',
        'name',
        'ambassador_name',
        'address',
        'postal_box',
    ];

    /**
     * Relation avec l'ambassade
     */
    public function embassy(): BelongsTo
    {
        return $this->belongsTo(Embassy::class);
    }
}
