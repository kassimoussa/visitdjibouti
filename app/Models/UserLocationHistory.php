<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLocationHistory extends Model
{
    use HasFactory;

    protected $table = 'user_location_history';

    protected $fillable = [
        'app_user_id',
        'latitude',
        'longitude',
        'accuracy',
        'altitude',
        'speed',
        'heading',
        'location_source',
        'activity_type',
        'confidence_level',
        'address',
        'street',
        'city',
        'region',
        'country',
        'postal_code',
        'place_name',
        'place_category',
        'timezone',
        'is_indoor',
        'nearby_pois',
        'weather_condition',
        'temperature',
        'recorded_at',
        'session_id',
        'trigger',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'accuracy' => 'decimal:2',
            'altitude' => 'decimal:2',
            'speed' => 'decimal:2',
            'heading' => 'decimal:2',
            'confidence_level' => 'integer',
            'is_indoor' => 'boolean',
            'nearby_pois' => 'array',
            'temperature' => 'decimal:2',
            'recorded_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns this location record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    /**
     * Scope pour récupérer les locations dans un rayon donné.
     */
    public function scopeWithinRadius($query, float $latitude, float $longitude, float $radiusKm = 1.0)
    {
        $radiusMeters = $radiusKm * 1000;

        return $query->whereRaw(
            '(6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) <= ?',
            [$latitude, $longitude, $latitude, $radiusMeters / 1000]
        );
    }

    /**
     * Obtenir la distance entre cette location et des coordonnées données.
     */
    public function distanceTo(float $latitude, float $longitude): float
    {
        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    /**
     * Vérifier si cette location est proche d'une autre.
     */
    public function isNear(float $latitude, float $longitude, float $radiusKm = 0.1): bool
    {
        return $this->distanceTo($latitude, $longitude) <= $radiusKm;
    }
}
