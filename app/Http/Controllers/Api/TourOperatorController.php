<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourOperator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TourOperatorController extends Controller
{
    /**
     * Liste des opérateurs de tour avec filtres
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', config('app.fallback_locale', 'fr'));
            
            // Validation des paramètres
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:255',
                'featured' => 'nullable|boolean',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:1|max:200',
                'per_page' => 'nullable|integer|min:1|max:50',
                'page' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paramètres de requête invalides',
                    'errors' => $validator->errors()
                ], 400);
            }

            $query = TourOperator::with([
                'translations',
                'logo',
                'media' => function ($query) {
                    $query->wherePivot('media_type', 'gallery')->orderByPivot('order');
                }
            ])->active();

            // Filtre par recherche
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address_translated', 'like', "%{$search}%");
                })->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('phones', 'like', "%{$search}%")
                  ->orWhere('emails', 'like', "%{$search}%")
                  ->orWhere('website', 'like', "%{$search}%");
            }

            if ($request->filled('featured')) {
                $query->featured();
            }

            // Filtre géographique (proximité)
            if ($request->filled(['latitude', 'longitude'])) {
                $latitude = $request->input('latitude');
                $longitude = $request->input('longitude');
                $radius = $request->input('radius', 50);
                
                $query->nearby($latitude, $longitude, $radius);
            } else {
                // Tri par défaut si pas de tri géographique
                $query->orderByDesc('featured')
                      ->orderByDesc('created_at');
            }

            $perPage = $request->input('per_page', 15);
            $tourOperators = $query->paginate($perPage);

            // Formater les données pour l'API
            $formattedData = $tourOperators->through(function ($tourOperator) use ($locale) {
                return $this->formatTourOperatorForApi($tourOperator, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => $formattedData->items(),
                'pagination' => [
                    'current_page' => $formattedData->currentPage(),
                    'per_page' => $formattedData->perPage(),
                    'total' => $formattedData->total(),
                    'last_page' => $formattedData->lastPage(),
                    'has_more_pages' => $formattedData->hasMorePages(),
                ],
                'filters_applied' => array_filter([
                    'search' => $request->input('search'),
                    'featured' => $request->input('featured'),
                    'nearby' => $request->filled(['latitude', 'longitude']),
                ]),
                'locale' => $locale,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des opérateurs de tour',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Détails d'un opérateur de tour
     */
    public function show(Request $request, $identifier): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', config('app.fallback_locale', 'fr'));

            // Recherche par ID ou slug
            $query = TourOperator::with([
                'translations',
                'logo',
                'media' => function ($query) {
                    $query->orderByPivot('media_type')->orderByPivot('order');
                }
            ]);

            $tourOperator = is_numeric($identifier) 
                ? $query->findOrFail($identifier)
                : $query->where('slug', $identifier)->firstOrFail();

            if (!$tourOperator->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Opérateur de tour non disponible'
                ], 404);
            }

            $data = $this->formatTourOperatorForApi($tourOperator, $locale, true);

            return response()->json([
                'success' => true,
                'data' => $data,
                'locale' => $locale,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Opérateur de tour non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'opérateur de tour',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Opérateurs de tour à proximité
     */
    public function getNearby(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:1|max:200',
                'limit' => 'nullable|integer|min:1|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paramètres requis manquants',
                    'errors' => $validator->errors()
                ], 400);
            }

            $locale = $request->header('Accept-Language', config('app.fallback_locale', 'fr'));
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 25);
            $limit = $request->input('limit', 10);

            $tourOperators = TourOperator::with(['translations', 'logo'])
                ->active()
                ->nearby($latitude, $longitude, $radius)
                ->limit($limit)
                ->get();

            $data = $tourOperators->map(function ($tourOperator) use ($locale) {
                $formatted = $this->formatTourOperatorForApi($tourOperator, $locale);
                $formatted['distance'] = round($tourOperator->distance, 2);
                $formatted['distance_unit'] = 'km';
                return $formatted;
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'search_params' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'radius' => $radius,
                ],
                'locale' => $locale,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche des opérateurs à proximité',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }


    /**
     * Formater un opérateur de tour pour l'API
     */
    private function formatTourOperatorForApi(TourOperator $tourOperator, string $locale, bool $detailed = false): array
    {
        $translation = $tourOperator->getTranslation($locale);
        
        $data = [
            'id' => $tourOperator->id,
            'slug' => $tourOperator->slug,
            'name' => $translation->name ?: $tourOperator->getTranslatedName('fr'),
            'description' => $translation->description ?: $tourOperator->getTranslatedDescription('fr'),
            'phones' => $tourOperator->phones_array,
            'first_phone' => $tourOperator->first_phone,
            'emails' => $tourOperator->emails_array,
            'first_email' => $tourOperator->first_email,
            'website' => $tourOperator->website_url,
            'address' => $translation->address_translated ?: $tourOperator->address,
            'latitude' => $tourOperator->latitude,
            'longitude' => $tourOperator->longitude,
            'featured' => $tourOperator->featured,
            'logo' => $tourOperator->logo ? [
                'url' => $tourOperator->logo->url,
                'thumbnail_url' => $tourOperator->logo->thumbnail_url,
                'alt_text' => $tourOperator->logo->alt_text,
            ] : null,
        ];

        if ($detailed) {
            $data = array_merge($data, [
                'gallery' => $tourOperator->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->url,
                        'thumbnail_url' => $media->thumbnail_url,
                        'title' => $media->title,
                        'alt_text' => $media->alt_text,
                        'order' => $media->pivot->order,
                    ];
                }),
            ]);
        } else {
            // Version simplifiée pour les listes
            $data['gallery_preview'] = $tourOperator->media->take(3)->map(function ($media) {
                return [
                    'url' => $media->thumbnail_url ?: $media->url,
                    'alt_text' => $media->alt_text,
                ];
            });
        }

        return $data;
    }
}