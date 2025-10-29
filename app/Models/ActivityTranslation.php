<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'locale',
        'title',
        'short_description',
        'description',
        'what_to_bring',
        'meeting_point_description',
        'additional_info',
    ];

    /**
     * Relations
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
