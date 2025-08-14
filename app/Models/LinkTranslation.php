<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkTranslation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'link_id',
        'locale',
        'name',
    ];

    /**
     * Get the link that owns the translation.
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}