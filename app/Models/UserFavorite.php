<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFavorite extends Model
{
    use HasFactory;

    protected $table = 'user_favorites';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_user_id',
        'favoritable_id',
        'favoritable_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who favorited the item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'app_user_id');
    }

    /**
     * Get the favoritable model (POI, Event, etc.).
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get favorites for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('app_user_id', $userId);
    }

    /**
     * Scope to get favorites of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('favoritable_type', $type);
    }

    /**
     * Scope to get POI favorites.
     */
    public function scopePois($query)
    {
        return $query->where('favoritable_type', Poi::class);
    }

    /**
     * Scope to get Event favorites.
     */
    public function scopeEvents($query)
    {
        return $query->where('favoritable_type', Event::class);
    }

    /**
     * Scope to get Tour favorites.
     */
    public function scopeTours($query)
    {
        return $query->where('favoritable_type', Tour::class);
    }

    /**
     * Scope to get Activity favorites.
     */
    public function scopeActivities($query)
    {
        return $query->where('favoritable_type', Activity::class);
    }

    /**
     * Check if a specific item is favorited by a user.
     */
    public static function isFavorited($userId, $favoritableId, $favoritableType): bool
    {
        return static::where('app_user_id', $userId)
            ->where('favoritable_id', $favoritableId)
            ->where('favoritable_type', $favoritableType)
            ->exists();
    }

    /**
     * Add or remove favorite (toggle).
     */
    public static function toggle($userId, $favoritableId, $favoritableType): array
    {
        $favorite = static::where('app_user_id', $userId)
            ->where('favoritable_id', $favoritableId)
            ->where('favoritable_type', $favoritableType)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return ['action' => 'removed', 'is_favorited' => false];
        } else {
            static::create([
                'app_user_id' => $userId,
                'favoritable_id' => $favoritableId,
                'favoritable_type' => $favoritableType,
            ]);

            return ['action' => 'added', 'is_favorited' => true];
        }
    }
}
