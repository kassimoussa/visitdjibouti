<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Poi;
use App\Models\UserFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Get all user's favorites.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $favorites = UserFavorite::forUser($user->id)
            ->with(['favoritable' => function ($query) {
                $query->with(['translations', 'featuredImage', 'categories.translations']);
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedFavorites = $favorites->map(function ($favorite) use ($request) {
            $item = $favorite->favoritable;
            if (! $item) {
                return null;
            }

            $locale = $request->header('Accept-Language', 'fr');
            $translation = $item->translation($locale);

            $data = [
                'id' => $item->id,
                'type' => class_basename($favorite->favoritable_type),
                'slug' => $item->slug,
                'name' => $translation?->name ?? $translation?->title ?? '',
                'description' => $translation?->description ?? '',
                'short_description' => $translation?->short_description ?? '',
                'featured_image' => $item->featuredImage ? [
                    'id' => $item->featuredImage->id,
                    'url' => asset($item->featuredImage->path),
                    'thumbnail_url' => asset($item->featuredImage->thumbnail_path ?? $item->featuredImage->path),
                    'alt_text' => $item->featuredImage->getTranslation($locale)->alt_text ?? '',
                ] : null,
                'categories' => $item->categories->map(function ($category) use ($locale) {
                    return [
                        'id' => $category->id,
                        'name' => $category->translation($locale)?->name ?? '',
                        'color' => $category->color,
                        'icon' => $category->icon,
                    ];
                }),
                'favorited_at' => $favorite->created_at->toISOString(),
            ];

            // Ajouter des champs spécifiques selon le type
            if ($favorite->favoritable_type === Poi::class) {
                $data['region'] = $item->region;
                $data['address'] = $translation?->address ?? '';
                $data['latitude'] = $item->latitude;
                $data['longitude'] = $item->longitude;
            } elseif ($favorite->favoritable_type === Event::class) {
                $data['start_date'] = $item->start_date?->toISOString();
                $data['end_date'] = $item->end_date?->toISOString();
                $data['location'] = $item->location;
                $data['price'] = $item->price;
                $data['organizer'] = $item->organizer;
            }

            return $data;
        })->filter(); // Filtrer les éléments null

        return response()->json([
            'success' => true,
            'data' => [
                'favorites' => $formattedFavorites->values(),
                'total' => $formattedFavorites->count(),
                'pois_count' => $favorites->where('favoritable_type', Poi::class)->count(),
                'events_count' => $favorites->where('favoritable_type', Event::class)->count(),
            ],
        ]);
    }

    /**
     * Get user's favorite POIs only.
     */
    public function pois(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $locale = $request->header('Accept-Language', 'fr');

        $favorites = $user->favoritePois()
            ->with([
                'translations' => function ($query) use ($locale) {
                    $query->where('locale', $locale)
                        ->orWhere('locale', config('app.fallback_locale', 'fr'));
                },
                'featuredImage',
                'categories.translations' => function ($query) use ($locale) {
                    $query->where('locale', $locale)
                        ->orWhere('locale', config('app.fallback_locale', 'fr'));
                },
            ])
            ->published()
            ->get();

        $formattedPois = $favorites->map(function ($poi) use ($locale) {
            $translation = $poi->translation($locale);

            return [
                'id' => $poi->id,
                'slug' => $poi->slug,
                'name' => $translation?->name ?? '',
                'description' => $translation?->description ?? '',
                'short_description' => $translation?->short_description ?? '',
                'address' => $translation?->address ?? '',
                'region' => $poi->region,
                'latitude' => $poi->latitude,
                'longitude' => $poi->longitude,
                'is_featured' => $poi->is_featured,
                'featured_image' => $poi->featuredImage ? [
                    'id' => $poi->featuredImage->id,
                    'url' => asset($poi->featuredImage->path),
                    'thumbnail_url' => asset($poi->featuredImage->thumbnail_path ?? $poi->featuredImage->path),
                ] : null,
                'categories' => $poi->categories->map(function ($category) use ($locale) {
                    return [
                        'id' => $category->id,
                        'name' => $category->translation($locale)?->name ?? '',
                        'color' => $category->color,
                        'icon' => $category->icon,
                    ];
                }),
                'favorites_count' => $poi->favorites_count,
                'is_favorited' => true, // Toujours true dans cette liste
                'favorited_at' => $poi->pivot->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'pois' => $formattedPois,
                'total' => $formattedPois->count(),
            ],
        ]);
    }

    /**
     * Add POI to favorites.
     */
    public function addPoi(Request $request, Poi $poi): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        if ($poi->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Ce point d\'intérêt n\'est pas disponible',
            ], 404);
        }

        $result = $user->toggleFavorite($poi);

        return response()->json([
            'success' => true,
            'message' => $result['action'] === 'added'
                ? 'Point d\'intérêt ajouté aux favoris'
                : 'Point d\'intérêt retiré des favoris',
            'data' => [
                'is_favorited' => $result['is_favorited'],
                'action' => $result['action'],
                'favorites_count' => $poi->favorites_count + ($result['action'] === 'added' ? 1 : -1),
            ],
        ]);
    }

    /**
     * Remove POI from favorites.
     */
    public function removePoi(Request $request, Poi $poi): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $removed = $user->removeFromFavorites($poi);

        return response()->json([
            'success' => true,
            'message' => $removed
                ? 'Point d\'intérêt retiré des favoris'
                : 'Ce point d\'intérêt n\'était pas dans vos favoris',
            'data' => [
                'is_favorited' => false,
                'removed' => $removed,
                'favorites_count' => max(0, $poi->favorites_count - ($removed ? 1 : 0)),
            ],
        ]);
    }

    /**
     * Add Event to favorites.
     */
    public function addEvent(Request $request, Event $event): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        if ($event->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Cet événement n\'est pas disponible',
            ], 404);
        }

        $result = $user->toggleFavorite($event);

        return response()->json([
            'success' => true,
            'message' => $result['action'] === 'added'
                ? 'Événement ajouté aux favoris'
                : 'Événement retiré des favoris',
            'data' => [
                'is_favorited' => $result['is_favorited'],
                'action' => $result['action'],
            ],
        ]);
    }

    /**
     * Remove Event from favorites.
     */
    public function removeEvent(Request $request, Event $event): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $removed = $user->removeFromFavorites($event);

        return response()->json([
            'success' => true,
            'message' => $removed
                ? 'Événement retiré des favoris'
                : 'Cet événement n\'était pas dans vos favoris',
            'data' => [
                'is_favorited' => false,
                'removed' => $removed,
            ],
        ]);
    }

    /**
     * Get favorites statistics for the user.
     */
    public function stats(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $stats = [
            'total_favorites' => $user->favorites()->count(),
            'pois_count' => $user->favorites()->pois()->count(),
            'events_count' => $user->favorites()->events()->count(),
            'recent_favorites' => $user->favorites()
                ->with('favoritable')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($favorite) {
                    return [
                        'id' => $favorite->favoritable->id,
                        'type' => class_basename($favorite->favoritable_type),
                        'name' => $favorite->favoritable->name ?? $favorite->favoritable->title ?? '',
                        'favorited_at' => $favorite->created_at->toISOString(),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
