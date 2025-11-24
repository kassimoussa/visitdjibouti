<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Poi;
use App\Models\Tour;
use App\Models\Activity;
use App\Models\UserFavorite;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
            ->with(['favoritable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Poi::class => ['translations', 'featuredImage', 'categories.translations'],
                    Event::class => ['translations', 'featuredImage', 'categories.translations'],
                    Tour::class => ['translations', 'featuredImage'],
                    Activity::class => ['translations', 'featuredImage'],
                ]);
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
                'categories' => method_exists($item, 'categories') ? $item->categories->map(function ($category) use ($locale) {
                    return [
                        'id' => $category->id,
                        'name' => $category->translation($locale)?->name ?? '',
                        'color' => $category->color,
                        'icon' => $category->icon,
                    ];
                }) : [],
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

            } elseif ($favorite->favoritable_type === Tour::class) {
                $data['start_date'] = $item->start_date?->toISOString();
                $data['end_date'] = $item->end_date?->toISOString();
                $data['price'] = $item->price;
                $data['duration_hours'] = $item->duration_hours;
                $data['difficulty_level'] = $item->difficulty_level;
            } elseif ($favorite->favoritable_type === Activity::class) {
                $data['price'] = $item->price;
                $data['duration_hours'] = $item->duration_hours;
                $data['difficulty_level'] = $item->difficulty_level;
                $data['region'] = $item->region;
                $data['latitude'] = $item->latitude;
                $data['longitude'] = $item->longitude;
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
                'tours_count' => $favorites->where('favoritable_type', Tour::class)->count(),
                'activities_count' => $favorites->where('favoritable_type', Activity::class)->count(),
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
     * Add Tour to favorites.
     */
    public function addTour(Request $request, Tour $tour): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        if ($tour->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Ce tour n\'est pas disponible',
            ], 404);
        }

        $result = $user->toggleFavorite($tour);

        return response()->json([
            'success' => true,
            'message' => $result['action'] === 'added'
                ? 'Tour ajouté aux favoris'
                : 'Tour retiré des favoris',
            'data' => [
                'is_favorited' => $result['is_favorited'],
                'action' => $result['action'],
            ],
        ]);
    }

    /**
     * Remove Tour from favorites.
     */
    public function removeTour(Request $request, Tour $tour): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $removed = $user->removeFromFavorites($tour);

        return response()->json([
            'success' => true,
            'message' => $removed
                ? 'Tour retiré des favoris'
                : 'Ce tour n\'était pas dans vos favoris',
            'data' => [
                'is_favorited' => false,
                'removed' => $removed,
            ],
        ]);
    }

    /**
     * Add Activity to favorites.
     */
    public function addActivity(Request $request, Activity $activity): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        if ($activity->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cette activité n\'est pas disponible',
            ], 404);
        }

        $result = $user->toggleFavorite($activity);

        return response()->json([
            'success' => true,
            'message' => $result['action'] === 'added'
                ? 'Activité ajoutée aux favoris'
                : 'Activité retirée des favoris',
            'data' => [
                'is_favorited' => $result['is_favorited'],
                'action' => $result['action'],
            ],
        ]);
    }

    /**
     * Remove Activity from favorites.
     */
    public function removeActivity(Request $request, Activity $activity): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié',
            ], 401);
        }

        $removed = $user->removeFromFavorites($activity);

        return response()->json([
            'success' => true,
            'message' => $removed
                ? 'Activité retirée des favoris'
                : 'Cette activité n\'était pas dans vos favoris',
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
            'total_favorites' => UserFavorite::forUser($user->id)->count(),
            'pois_count' => UserFavorite::forUser($user->id)->pois()->count(),
            'events_count' => UserFavorite::forUser($user->id)->events()->count(),
            'tours_count' => UserFavorite::forUser($user->id)->tours()->count(),
            'activities_count' => UserFavorite::forUser($user->id)->activities()->count(),

            'recent_favorites' => UserFavorite::forUser($user->id)
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
