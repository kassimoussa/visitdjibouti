<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poi;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PoiController extends Controller
{
    /**
     * Get all POIs with pagination, filtering, and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Poi::published()
                         ->with(['featuredImage', 'categories.translations', 'translations']);

            // Search by name
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Filter by category (include parent and children)
            if ($request->filled('category_id')) {
                $categoryId = $request->get('category_id');
                $category = Category::find($categoryId);
                
                if ($category) {
                    // Get all category IDs to include (parent + all children)
                    $categoryIds = [$categoryId];
                    
                    // If it's a parent category, include all children
                    if ($category->children()->count() > 0) {
                        $categoryIds = array_merge($categoryIds, $category->children()->pluck('id')->toArray());
                    }
                    
                    $query->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('categories.id', $categoryIds);
                    });
                }
            }

            // Filter by region
            if ($request->filled('region')) {
                $query->where('region', $request->get('region'));
            }

            // Filter by featured
            if ($request->filled('featured')) {
                $query->featured();
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'name') {
                // Sort by translated name
                $locale = $request->header('Accept-Language', 'fr');
                $query->leftJoin('poi_translations', function ($join) use ($locale) {
                    $join->on('pois.id', '=', 'poi_translations.poi_id')
                         ->where('poi_translations.locale', '=', $locale);
                })->orderBy('poi_translations.name', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
            $pois = $query->paginate($perPage);

            // Transform data
            $user = Auth::guard('sanctum')->user();
            $transformedPois = $pois->getCollection()->map(function ($poi) use ($request, $user) {
                return $this->transformPoi($poi, $request->header('Accept-Language', 'fr'), $user);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'pois' => $transformedPois,
                    'pagination' => [
                        'current_page' => $pois->currentPage(),
                        'last_page' => $pois->lastPage(),
                        'per_page' => $pois->perPage(),
                        'total' => $pois->total(),
                        'from' => $pois->firstItem(),
                        'to' => $pois->lastItem(),
                    ],
                    'filters' => [
                        'regions' => $this->getAvailableRegions(),
                        'categories' => $this->getAvailableCategories($request->header('Accept-Language', 'fr'))
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POIs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific POI by ID or slug
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        try {
            $query = Poi::published()
                         ->with([
                             'featuredImage', 
                             'media', 
                             'categories.translations', 
                             'translations'
                         ]);

            // Try to find by ID first, then by slug
            $poi = is_numeric($identifier) 
                ? $query->find($identifier)
                : $query->where('slug', $identifier)->first();

            if (!$poi) {
                return response()->json([
                    'success' => false,
                    'message' => 'POI not found'
                ], 404);
            }

            $locale = $request->header('Accept-Language', 'fr');
            $user = Auth::guard('sanctum')->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'poi' => $this->transformPoiDetailed($poi, $locale, $user)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POI',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get POIs by category
     */
    public function getByCategory(Request $request, int $categoryId): JsonResponse
    {
        try {
            $category = Category::find($categoryId);
            
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $query = Poi::published()
                         ->with(['featuredImage', 'categories.translations', 'translations'])
                         ->whereHas('categories', function ($q) use ($categoryId) {
                             $q->where('categories.id', $categoryId);
                         });

            $perPage = min($request->get('per_page', 15), 50);
            $pois = $query->paginate($perPage);

            $locale = $request->header('Accept-Language', 'fr');
            $user = Auth::guard('sanctum')->user();
            $transformedPois = $pois->getCollection()->map(function ($poi) use ($locale, $user) {
                return $this->transformPoi($poi, $locale, $user);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->translation($locale)->name ?? $category->name,
                        'description' => $category->translation($locale)->description ?? ''
                    ],
                    'pois' => $transformedPois,
                    'pagination' => [
                        'current_page' => $pois->currentPage(),
                        'last_page' => $pois->lastPage(),
                        'per_page' => $pois->perPage(),
                        'total' => $pois->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POIs by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nearby POIs based on coordinates
     */
    public function getNearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100' // radius in kilometers
        ]);

        try {
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 10); // Default 10km radius

            $query = Poi::published()
                         ->with(['featuredImage', 'categories.translations', 'translations'])
                         ->selectRaw("
                             *,
                             (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                             * cos(radians(longitude) - radians(?)) 
                             + sin(radians(?)) * sin(radians(latitude)))) AS distance
                         ", [$latitude, $longitude, $latitude])
                         ->having('distance', '<=', $radius)
                         ->orderBy('distance');

            $limit = min($request->get('limit', 20), 50);
            $pois = $query->limit($limit)->get();

            $locale = $request->header('Accept-Language', 'fr');
            $user = Auth::guard('sanctum')->user();
            $transformedPois = $pois->map(function ($poi) use ($locale, $user) {
                $transformed = $this->transformPoi($poi, $locale, $user);
                $transformed['distance'] = round($poi->distance, 2); // Distance in km
                return $transformed;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'pois' => $transformedPois,
                    'search_params' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'radius_km' => $radius,
                        'total_found' => $pois->count()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nearby POIs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform POI for list view
     */
    private function transformPoi(Poi $poi, string $locale = 'fr', $user = null): array
    {
        $translation = $poi->translation($locale);
        
        return [
            'id' => $poi->id,
            'slug' => $poi->slug,
            'name' => $translation->name ?? '',
            'short_description' => $translation->short_description ?? '',
            'address' => $translation->address ?? '',
            'region' => $poi->region,
            'full_address' => $poi->full_address,
            'latitude' => $poi->latitude,
            'longitude' => $poi->longitude,
            'is_featured' => $poi->is_featured,
            'allow_reservations' => $poi->allow_reservations,
            'website' => $poi->website,
            'contact' => $poi->contact,
            'featured_image' => $poi->featuredImage ? [
                'id' => $poi->featuredImage->id,
                'url' => $poi->featuredImage->getImageUrl(),
                'alt' => $poi->featuredImage->translation($locale)->alt_text ?? ''
            ] : null,
            'categories' => $poi->categories->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->translation($locale)->name ?? $category->name,
                    'slug' => $category->slug,
                    'level' => $category->level,
                    'parent_id' => $category->parent_id,
                    'parent_name' => $category->parent ? ($category->parent->translation($locale)->name ?? $category->parent->name) : null
                ];
            }),
            'favorites_count' => $poi->favorites_count,
            'is_favorited' => $user ? $poi->isFavoritedBy($user->id) : false,
            'created_at' => $poi->created_at->toISOString(),
            'updated_at' => $poi->updated_at->toISOString()
        ];
    }

    /**
     * Transform POI for detailed view
     */
    private function transformPoiDetailed(Poi $poi, string $locale = 'fr', $user = null): array
    {
        $translation = $poi->translation($locale);
        
        $basic = $this->transformPoi($poi, $locale, $user);
        
        return array_merge($basic, [
            'description' => $translation->description ?? '',
            'opening_hours' => $translation->opening_hours ?? '',
            'entry_fee' => $translation->entry_fee ?? '',
            'tips' => $translation->tips ?? '',
            'media' => $poi->media->map(function ($media) use ($locale) {
                return [
                    'id' => $media->id,
                    'url' => $media->getImageUrl(),
                    'alt' => $media->translation($locale)->alt_text ?? '',
                    'order' => $media->pivot->order ?? 0
                ];
            })
        ]);
    }

    /**
     * Get available regions
     */
    private function getAvailableRegions(): array
    {
        return ['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'];
    }

    /**
     * Get available categories
     */
    private function getAvailableCategories(string $locale = 'fr'): array
    {
        return Category::with('translations')
            ->get()
            ->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->translation($locale)->name ?? $category->name,
                    'slug' => $category->slug
                ];
            })
            ->toArray();
    }
}