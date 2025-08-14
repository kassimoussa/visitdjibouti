<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExternalLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope pour les liens actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope pour les liens inactifs
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }
}