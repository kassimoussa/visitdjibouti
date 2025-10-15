<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Embassy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmbassyController extends Controller
{
    /**
     * Get all active embassies
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Embassy::active()->with('translations');

            // Filter by type
            $type = $request->get('type');
            if ($type === 'foreign_in_djibouti') {
                $query->foreignInDjibouti();
            } elseif ($type === 'djiboutian_abroad') {
                $query->djiboutianAbroad();
            }

            // Search by name or country
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%");
                });
            }

            // Filter by country code
            if ($request->filled('country_code')) {
                $query->where('country_code', $request->get('country_code'));
            }

            $embassies = $query->orderBy('id')->get();

            $locale = $request->header('Accept-Language', 'fr');
            $transformedEmbassies = $embassies->map(function ($embassy) use ($locale) {
                return $this->transformEmbassy($embassy, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'embassies' => $transformedEmbassies,
                    'total' => $embassies->count(),
                    'types' => [
                        'foreign_in_djibouti' => 'Ambassades étrangères à Djibouti',
                        'djiboutian_abroad' => 'Ambassades djiboutiennes à l\'étranger',
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch embassies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific embassy by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $embassy = Embassy::active()->with('translations')->find($id);

            if (! $embassy) {
                return response()->json([
                    'success' => false,
                    'message' => 'Embassy not found',
                ], 404);
            }

            $locale = $request->header('Accept-Language', 'fr');

            return response()->json([
                'success' => true,
                'data' => [
                    'embassy' => $this->transformEmbassy($embassy, $locale, true),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch embassy',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get embassies by type
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        if (! in_array($type, ['foreign_in_djibouti', 'djiboutian_abroad'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid embassy type',
            ], 400);
        }

        try {
            $query = Embassy::active()->with('translations')->where('type', $type);

            $embassies = $query->orderBy('id')->get();

            $locale = $request->header('Accept-Language', 'fr');
            $transformedEmbassies = $embassies->map(function ($embassy) use ($locale) {
                return $this->transformEmbassy($embassy, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'embassies' => $transformedEmbassies,
                    'type' => $type,
                    'type_label' => Embassy::TYPES[$type],
                    'total' => $embassies->count(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch embassies by type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get nearby embassies based on coordinates
     */
    public function getNearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:100', // radius in kilometers
        ]);

        try {
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 50); // Default 50km radius

            $query = Embassy::active()
                ->with('translations')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->selectRaw('
                               *,
                               (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                               * cos(radians(longitude) - radians(?)) 
                               + sin(radians(?)) * sin(radians(latitude)))) AS distance
                           ', [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->orderBy('distance');

            $limit = min($request->get('limit', 20), 50);
            $embassies = $query->limit($limit)->get();

            $locale = $request->header('Accept-Language', 'fr');
            $transformedEmbassies = $embassies->map(function ($embassy) use ($locale) {
                $transformed = $this->transformEmbassy($embassy, $locale);
                $transformed['distance'] = round($embassy->distance, 2); // Distance in km

                return $transformed;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'embassies' => $transformedEmbassies,
                    'search_params' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'radius_km' => $radius,
                        'total_found' => $embassies->count(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nearby embassies',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform embassy for API response
     */
    private function transformEmbassy(Embassy $embassy, string $locale = 'fr', bool $detailed = false): array
    {
        $translation = $embassy->getTranslation($locale);

        $basic = [
            'id' => $embassy->id,
            'type' => $embassy->type,
            'type_label' => $embassy->type_label,
            'country_code' => $embassy->country_code,
            'name' => $embassy->getTranslatedName($locale),
            'ambassador_name' => $embassy->getTranslatedAmbassadorName($locale),
            'phones' => $embassy->phones_array,
            'emails' => $embassy->emails_array,
            'website' => $embassy->website_url,
            'is_active' => $embassy->is_active,
            'created_at' => $embassy->created_at->toISOString(),
            'updated_at' => $embassy->updated_at->toISOString(),
        ];

        if ($detailed) {
            $basic = array_merge($basic, [
                'address' => $translation->address ?? '',
                'postal_box' => $translation->postal_box ?? '',
                'fax' => $embassy->fax,
                'ld' => $embassy->ld_array,
                'latitude' => $embassy->latitude,
                'longitude' => $embassy->longitude,
                'has_coordinates' => ! is_null($embassy->latitude) && ! is_null($embassy->longitude),
            ]);
        }

        return $basic;
    }
}
