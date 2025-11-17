<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Event;
use App\Models\Poi;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Get all content (POIs, Events, Tours, Activities)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllContent(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', 'fr');
            $perPage = $request->get('per_page', 15);
            $featured = $request->get('featured');
            $region = $request->get('region');
            $search = $request->get('search');

            // Get POIs
            $poisQuery = Poi::published()
                ->with(['featuredImage', 'categories.translations', 'translations', 'tourOperators.translations']);

            if ($featured !== null) {
                $poisQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($region) {
                $poisQuery->where('region', $region);
            }

            if ($search) {
                $poisQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Get Events
            $eventsQuery = Event::published()
                ->with(['featuredImage', 'categories.translations', 'translations', 'tourOperator.translations']);

            if ($featured !== null) {
                $eventsQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($region) {
                $eventsQuery->where('region', $region);
            }

            if ($search) {
                $eventsQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Get Tours
            $toursQuery = Tour::where('status', 'approved')
                ->with(['tourOperator.translations', 'translations', 'featuredImage']);

            if ($featured !== null) {
                $toursQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($search) {
                $toursQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Get Activities
            $activitiesQuery = Activity::where('status', 'active')
                ->with(['tourOperator.translations', 'translations', 'featuredImage']);

            if ($featured !== null) {
                $activitiesQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($region) {
                $activitiesQuery->where('region', $region);
            }

            if ($search) {
                $activitiesQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Get all data
            $pois = $poisQuery->get();
            $events = $eventsQuery->get();
            $tours = $toursQuery->get();
            $activities = $activitiesQuery->get();

            // Transform data
            $poisData = $pois->map(function ($poi) use ($locale) {
                return $this->transformPoi($poi, $locale);
            });

            $eventsData = $events->map(function ($event) use ($locale) {
                return $this->transformEvent($event, $locale);
            });

            $toursData = $tours->map(function ($tour) use ($locale) {
                return $this->transformTour($tour, $locale);
            });

            $activitiesData = $activities->map(function ($activity) use ($locale) {
                return $this->transformActivity($activity, $locale);
            });

            // Combine all content
            $allContent = collect([
                ...$poisData,
                ...$eventsData,
                ...$toursData,
                ...$activitiesData,
            ]);

            // Sort by featured and created date
            $allContent = $allContent->sortByDesc(function ($item) {
                return [$item['is_featured'] ?? false, $item['created_at'] ?? null];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $allContent,
                    'total' => $allContent->count(),
                    'counts' => [
                        'pois' => $pois->count(),
                        'events' => $events->count(),
                        'tours' => $tours->count(),
                        'activities' => $activities->count(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all geolocated content (POIs and Events with coordinates)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGeolocatedContent(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', 'fr');
            $featured = $request->get('featured');
            $region = $request->get('region');
            $search = $request->get('search');

            // Nearby search parameters
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 50); // km

            // Get POIs with coordinates
            $poisQuery = Poi::published()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['featuredImage', 'categories.translations', 'translations', 'tourOperators.translations']);

            if ($featured !== null) {
                $poisQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($region) {
                $poisQuery->where('region', $region);
            }

            if ($search) {
                $poisQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Get Events with coordinates
            $eventsQuery = Event::published()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['featuredImage', 'categories.translations', 'translations', 'tourOperator.translations']);

            if ($featured !== null) {
                $eventsQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($region) {
                $eventsQuery->where('region', $region);
            }

            if ($search) {
                $eventsQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Get Activities with coordinates
            $activitiesQuery = Activity::where('status', 'active')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['tourOperator.translations', 'translations', 'featuredImage']);

            if ($featured !== null) {
                $activitiesQuery->where('is_featured', filter_var($featured, FILTER_VALIDATE_BOOLEAN));
            }

            if ($region) {
                $activitiesQuery->where('region', $region);
            }

            if ($search) {
                $activitiesQuery->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Apply nearby filter if coordinates provided
            if ($latitude && $longitude) {
                $poisQuery = $this->addNearbyFilter($poisQuery, $latitude, $longitude, $radius);
                $eventsQuery = $this->addNearbyFilter($eventsQuery, $latitude, $longitude, $radius);
                $activitiesQuery = $this->addNearbyFilter($activitiesQuery, $latitude, $longitude, $radius);
            }

            // Get all data
            $pois = $poisQuery->get();
            $events = $eventsQuery->get();
            $activities = $activitiesQuery->get();

            // Transform data with distance calculation
            $poisData = $pois->map(function ($poi) use ($locale, $latitude, $longitude) {
                $data = $this->transformPoi($poi, $locale);
                if ($latitude && $longitude) {
                    $data['distance_km'] = $this->calculateDistance(
                        $latitude,
                        $longitude,
                        $poi->latitude,
                        $poi->longitude
                    );
                }
                return $data;
            });

            $eventsData = $events->map(function ($event) use ($locale, $latitude, $longitude) {
                $data = $this->transformEvent($event, $locale);
                if ($latitude && $longitude) {
                    $data['distance_km'] = $this->calculateDistance(
                        $latitude,
                        $longitude,
                        $event->latitude,
                        $event->longitude
                    );
                }
                return $data;
            });

            $activitiesData = $activities->map(function ($activity) use ($locale, $latitude, $longitude) {
                $data = $this->transformActivity($activity, $locale);
                if ($latitude && $longitude) {
                    $data['distance_km'] = $this->calculateDistance(
                        $latitude,
                        $longitude,
                        $activity->latitude,
                        $activity->longitude
                    );
                }
                return $data;
            });

            // Combine all geolocated content
            $allContent = collect([
                ...$poisData,
                ...$eventsData,
                ...$activitiesData,
            ]);

            // Sort by distance if nearby search, otherwise by featured
            if ($latitude && $longitude) {
                $allContent = $allContent->sortBy('distance_km')->values();
            } else {
                $allContent = $allContent->sortByDesc(function ($item) {
                    return [$item['is_featured'] ?? false, $item['created_at'] ?? null];
                })->values();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'content' => $allContent,
                    'total' => $allContent->count(),
                    'counts' => [
                        'pois' => $pois->count(),
                        'events' => $events->count(),
                        'activities' => $activities->count(),
                    ],
                    'search_center' => $latitude && $longitude ? [
                        'latitude' => (float) $latitude,
                        'longitude' => (float) $longitude,
                        'radius_km' => (float) $radius,
                    ] : null,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch geolocated content',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform POI for API response
     */
    private function transformPoi($poi, $locale)
    {
        $translation = $poi->translation($locale);

        return [
            'type' => 'poi',
            'id' => $poi->id,
            'slug' => $poi->slug,
            'name' => $translation->name ?? 'N/A',
            'description' => $translation->description ?? null,
            'short_description' => $translation->short_description ?? null,
            'latitude' => $poi->latitude,
            'longitude' => $poi->longitude,
            'region' => $poi->region,
            'is_featured' => $poi->is_featured,
            'featured_image' => $poi->featuredImage ? [
                'id' => $poi->featuredImage->id,
                'url' => $poi->featuredImage->url,
                'thumbnail_url' => $poi->featuredImage->thumbnail_url,
            ] : null,
            'categories' => $poi->categories->map(function ($category) use ($locale) {
                $catTranslation = $category->translation($locale);
                return [
                    'id' => $category->id,
                    'name' => $catTranslation->name ?? 'N/A',
                    'slug' => $category->slug,
                ];
            }),
            'created_at' => $poi->created_at?->toISOString(),
            'updated_at' => $poi->updated_at?->toISOString(),
        ];
    }

    /**
     * Transform Event for API response
     */
    private function transformEvent($event, $locale)
    {
        $translation = $event->translation($locale);

        return [
            'type' => 'event',
            'id' => $event->id,
            'slug' => $event->slug,
            'name' => $translation->name ?? 'N/A',
            'description' => $translation->description ?? null,
            'short_description' => $translation->short_description ?? null,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'location' => $event->location,
            'region' => $event->region,
            'start_date' => $event->start_date?->toDateString(),
            'end_date' => $event->end_date?->toDateString(),
            'start_time' => $event->start_time,
            'end_time' => $event->end_time,
            'is_featured' => $event->is_featured,
            'featured_image' => $event->featuredImage ? [
                'id' => $event->featuredImage->id,
                'url' => $event->featuredImage->url,
                'thumbnail_url' => $event->featuredImage->thumbnail_url,
            ] : null,
            'categories' => $event->categories->map(function ($category) use ($locale) {
                $catTranslation = $category->translation($locale);
                return [
                    'id' => $category->id,
                    'name' => $catTranslation->name ?? 'N/A',
                    'slug' => $category->slug,
                ];
            }),
            'created_at' => $event->created_at?->toISOString(),
            'updated_at' => $event->updated_at?->toISOString(),
        ];
    }

    /**
     * Transform Tour for API response
     */
    private function transformTour($tour, $locale)
    {
        $translation = $tour->translation($locale);

        return [
            'type' => 'tour',
            'id' => $tour->id,
            'slug' => $tour->slug,
            'name' => $translation->name ?? 'N/A',
            'description' => $translation->description ?? null,
            'short_description' => $translation->short_description ?? null,
            'meeting_point_latitude' => $tour->meeting_point_latitude,
            'meeting_point_longitude' => $tour->meeting_point_longitude,
            'meeting_point_address' => $tour->meeting_point_address,
            'price' => $tour->price,
            'currency' => $tour->currency ?? 'DJF',
            'duration_hours' => $tour->duration_hours,
            'start_date' => $tour->start_date?->toDateString(),
            'end_date' => $tour->end_date?->toDateString(),
            'is_featured' => $tour->is_featured,
            'featured_image' => $tour->featuredImage ? [
                'id' => $tour->featuredImage->id,
                'url' => $tour->featuredImage->url,
                'thumbnail_url' => $tour->featuredImage->thumbnail_url,
            ] : null,
            'tour_operator' => $tour->tourOperator ? [
                'id' => $tour->tourOperator->id,
                'name' => $tour->tourOperator->getTranslatedName($locale),
            ] : null,
            'created_at' => $tour->created_at?->toISOString(),
            'updated_at' => $tour->updated_at?->toISOString(),
        ];
    }

    /**
     * Transform Activity for API response
     */
    private function transformActivity($activity, $locale)
    {
        $translation = $activity->translation($locale);

        return [
            'type' => 'activity',
            'id' => $activity->id,
            'slug' => $activity->slug,
            'name' => $translation->name ?? 'N/A',
            'description' => $translation->description ?? null,
            'short_description' => $translation->short_description ?? null,
            'latitude' => $activity->latitude,
            'longitude' => $activity->longitude,
            'location_address' => $activity->location_address,
            'region' => $activity->region,
            'price' => $activity->price,
            'currency' => $activity->currency ?? 'DJF',
            'duration_hours' => $activity->duration_hours,
            'duration_minutes' => $activity->duration_minutes,
            'difficulty_level' => $activity->difficulty_level,
            'is_featured' => $activity->is_featured,
            'featured_image' => $activity->featuredImage ? [
                'id' => $activity->featuredImage->id,
                'url' => $activity->featuredImage->url,
                'thumbnail_url' => $activity->featuredImage->thumbnail_url,
            ] : null,
            'tour_operator' => $activity->tourOperator ? [
                'id' => $activity->tourOperator->id,
                'name' => $activity->tourOperator->getTranslatedName($locale),
            ] : null,
            'created_at' => $activity->created_at?->toISOString(),
            'updated_at' => $activity->updated_at?->toISOString(),
        ];
    }

    /**
     * Add nearby filter using Haversine formula
     */
    private function addNearbyFilter($query, $latitude, $longitude, $radius)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";

        return $query->selectRaw("*, {$haversine} AS distance", [$latitude, $longitude, $latitude])
            ->whereRaw("{$haversine} < ?", [$latitude, $longitude, $latitude, $radius])
            ->orderBy('distance');
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }
}
