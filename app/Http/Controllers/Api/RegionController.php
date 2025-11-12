<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Event;
use App\Models\Poi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    /**
     * Get all available regions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $regions = [
            'Djibouti',
            'Ali Sabieh',
            'Dikhil',
            'Tadjourah',
            'Obock',
            'Arta',
        ];

        // Get content count per region
        $regionsWithCounts = collect($regions)->map(function ($region) {
            return [
                'name' => $region,
                'pois_count' => Poi::where('region', $region)->where('status', 'published')->count(),
                'events_count' => Event::where('region', $region)->where('status', 'published')->count(),
                'activities_count' => Activity::where('region', $region)->where('status', 'active')->count(),
                'total_count' => Poi::where('region', $region)->where('status', 'published')->count()
                    + Event::where('region', $region)->where('status', 'published')->count()
                    + Activity::where('region', $region)->where('status', 'active')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $regionsWithCounts,
        ]);
    }

    /**
     * Get all content (POIs, Events, Activities) for a specific region.
     *
     * @param  string  $region
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $region)
    {
        // Validate region
        $validRegions = ['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'];
        if (! in_array($region, $validRegions)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid region. Valid regions are: '.implode(', ', $validRegions),
            ], 400);
        }

        $locale = $request->header('Accept-Language', 'fr');

        // Get POIs
        $pois = Poi::with(['translations', 'categories.translations', 'featuredImage'])
            ->where('region', $region)
            ->where('status', 'published')
            ->get()
            ->map(function ($poi) use ($locale) {
                $translation = $poi->translations->firstWhere('locale', $locale)
                    ?? $poi->translations->firstWhere('locale', 'fr');

                return [
                    'id' => $poi->id,
                    'slug' => $poi->slug,
                    'name' => $translation?->name ?? '',
                    'short_description' => $translation?->short_description ?? '',
                    'description' => $translation?->description ?? '',
                    'address' => $translation?->address ?? '',
                    'region' => $poi->region,
                    'latitude' => $poi->latitude,
                    'longitude' => $poi->longitude,
                    'is_featured' => $poi->is_featured,
                    'featured_image' => $poi->featuredImage ? [
                        'id' => $poi->featuredImage->id,
                        'url' => $poi->featuredImage->getImageUrl(),
                    ] : null,
                    'categories' => $poi->categories->map(function ($category) use ($locale) {
                        $catTranslation = $category->translations->firstWhere('locale', $locale)
                            ?? $category->translations->firstWhere('locale', 'fr');

                        return [
                            'id' => $category->id,
                            'name' => $catTranslation?->name ?? '',
                        ];
                    }),
                ];
            });
 
        // Get Events
        $events = Event::with(['translations', 'categories.translations', 'featuredImage'])
            ->where('region', $region)
            ->where('status', 'published')
            ->get()
            ->map(function ($event) use ($locale) {
                $translation = $event->translations->firstWhere('locale', $locale)
                    ?? $event->translations->firstWhere('locale', 'fr');

                return [
                    'id' => $event->id,
                    'slug' => $event->slug,
                    'title' => $translation?->title ?? '',
                    'short_description' => $translation?->short_description ?? '',
                    'description' => $translation?->description ?? '',
                    'location' => $event->location,
                    'region' => $event->region,
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                    'start_date' => $event->start_date->format('Y-m-d'),
                    'end_date' => $event->end_date->format('Y-m-d'),
                    'start_time' => $event->start_time ? $event->start_time->format('H:i') : null,
                    'end_time' => $event->end_time ? $event->end_time->format('H:i') : null,
                    'price' => $event->price,
                    'is_featured' => $event->is_featured,
                    'featured_image' => $event->featuredImage ? [
                        'id' => $event->featuredImage->id,
                        'url' => $event->featuredImage->getImageUrl(),
                    ] : null,
                    'categories' => $event->categories->map(function ($category) use ($locale) {
                        $catTranslation = $category->translations->firstWhere('locale', $locale)
                            ?? $category->translations->firstWhere('locale', 'fr');

                        return [
                            'id' => $category->id,
                            'name' => $catTranslation?->name ?? '',
                        ];
                    }),
                ];
            });

        // Get Activities
        $activities = Activity::with(['translations', 'tourOperator', 'featuredImage'])
            ->where('region', $region)
            ->where('status', 'active')
            ->get()
            ->map(function ($activity) use ($locale) {
                $translation = $activity->translations->firstWhere('locale', $locale)
                    ?? $activity->translations->firstWhere('locale', 'fr');

                return [
                    'id' => $activity->id,
                    'slug' => $activity->slug,
                    'title' => $translation?->title ?? '',
                    'short_description' => $translation?->short_description ?? '',
                    'description' => $translation?->description ?? '',
                    'location_address' => $activity->location_address,
                    'region' => $activity->region,
                    'latitude' => $activity->latitude,
                    'longitude' => $activity->longitude,
                    'price' => $activity->price,
                    'currency' => $activity->currency,
                    'difficulty_level' => $activity->difficulty_level,
                    'duration_hours' => $activity->duration_hours,
                    'duration_minutes' => $activity->duration_minutes,
                    'is_featured' => $activity->is_featured,
                    'featured_image' => $activity->featuredImage ? [
                        'id' => $activity->featuredImage->id,
                        'url' => asset($activity->featuredImage->path),
                    ] : null,
                    'tour_operator' => $activity->tourOperator ? [
                        'id' => $activity->tourOperator->id,
                        'name' => $activity->tourOperator->name,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'region' => $region,
                'summary' => [
                    'pois_count' => $pois->count(),
                    'events_count' => $events->count(),
                    'activities_count' => $activities->count(),
                    'total_count' => $pois->count() + $events->count() + $activities->count(),
                ],
                'pois' => $pois,
                'events' => $events,
                'activities' => $activities,
            ],
        ]);
    }

    /**
     * Get content statistics grouped by region.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $regions = ['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'];

        $statistics = collect($regions)->map(function ($region) {
            $poisCount = Poi::where('region', $region)->where('status', 'published')->count();
            $eventsCount = Event::where('region', $region)->where('status', 'published')->count();
            $activitiesCount = Activity::where('region', $region)->where('status', 'active')->count();

            return [
                'region' => $region,
                'pois_count' => $poisCount,
                'events_count' => $eventsCount,
                'activities_count' => $activitiesCount,
                'total_count' => $poisCount + $eventsCount + $activitiesCount,
            ];
        })->sortByDesc('total_count')->values();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }
}
