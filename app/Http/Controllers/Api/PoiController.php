<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Poi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * POI API Controller
 *
 * Exemple de structure API avec contacts multiples:
 * {
 *   "contacts": [
 *     {
 *       "name": "Restaurant Le Palmier",
 *       "type": "restaurant",
 *       "type_label": "Restaurant",
 *       "phone": "+253 77 XX XX XX",
 *       "email": "contact@palmier.dj",
 *       "website": "https://www.palmier.dj",
 *       "address": "Avenue Hassan Gouled, Djibouti",
 *       "description": "Spécialités locales",
 *       "is_primary": true
 *     }
 *   ],
 *   "has_contacts": true,
 *   "contacts_count": 2,
 *   "primary_contact": { ... },
 *   "tour_operators": [
 *     {
 *       "id": 5,
 *       "name": "Desert Tours Djibouti",
 *       "service_type": "full_package",
 *       "service_type_label": "Package complet",
 *       "is_primary": true,
 *       "phone": "+253 77 XX XX XX",
 *       "website": "https://deserttours.dj",
 *       "notes": "Excursions vers le Lac Assal incluses"
 *     }
 *   ],
 *   "has_tour_operators": true,
 *   "tour_operators_count": 1,
 *   "primary_tour_operator": { ... }
 * }
 *
 * Paramètres de filtrage:
 * - contact_type: restaurant, tour_operator, guide, etc.
 * - has_contacts: true/false
 * - tour_operator_id: ID du tour operator
 * - service_type: guide, transport, full_package, accommodation, activity, other
 * - has_tour_operators: true/false
 * - has_primary_operator: true/false
 */
class PoiController extends Controller
{
    /**
     * Get all POIs with pagination, filtering, and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Poi::published()
                ->with(['featuredImage', 'categories.translations', 'translations', 'tourOperators.translations']);

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

            // Filter by contact type
            if ($request->filled('contact_type')) {
                $contactType = $request->get('contact_type');
                $query->whereRaw('JSON_SEARCH(contacts, "one", ?) IS NOT NULL', [$contactType]);
            }

            // Filter POIs that have contacts
            if ($request->filled('has_contacts') && $request->boolean('has_contacts')) {
                $query->whereNotNull('contacts')
                    ->where('contacts', '!=', '[]')
                    ->where('contacts', '!=', 'null');
            }

            // Filter by tour operator ID
            if ($request->filled('tour_operator_id')) {
                $tourOperatorId = $request->get('tour_operator_id');
                $query->whereHas('tourOperators', function ($q) use ($tourOperatorId) {
                    $q->where('tour_operators.id', $tourOperatorId);
                });
            }

            // Filter by tour operator service type
            if ($request->filled('service_type')) {
                $serviceType = $request->get('service_type');
                $query->whereHas('tourOperators', function ($q) use ($serviceType) {
                    $q->where('poi_tour_operator.service_type', $serviceType);
                });
            }

            // Filter POIs that have tour operators
            if ($request->filled('has_tour_operators') && $request->boolean('has_tour_operators')) {
                $query->whereHas('tourOperators');
            }

            // Filter POIs with primary tour operators
            if ($request->filled('has_primary_operator') && $request->boolean('has_primary_operator')) {
                $query->whereHas('tourOperators', function ($q) {
                    $q->where('poi_tour_operator.is_primary', true);
                });
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'name') {
                // Sort by translated name using subquery to avoid ID conflicts
                $locale = $request->header('Accept-Language', 'fr');

                // Use raw SQL with subquery to properly handle the sort
                $query->addSelect([
                    'translation_name' => function ($subQuery) use ($locale) {
                        $subQuery->select('name')
                            ->from('poi_translations')
                            ->whereRaw('poi_translations.poi_id = pois.id')
                            ->where('locale', $locale)
                            ->limit(1);
                    },
                ])->orderBy('translation_name', $sortOrder);
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
                        'categories' => $this->getAvailableCategories($request->header('Accept-Language', 'fr')),
                        'contact_types' => $this->getAvailableContactTypes(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POIs',
                'error' => $e->getMessage(),
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
                    'translations',
                    'tourOperators.translations',
                ]);

            // Try to find by ID first, then by slug
            $poi = is_numeric($identifier)
                ? $query->find($identifier)
                : $query->where('slug', $identifier)->first();

            if (! $poi) {
                return response()->json([
                    'success' => false,
                    'message' => 'POI not found',
                ], 404);
            }

            $locale = $request->header('Accept-Language', 'fr');
            $user = Auth::guard('sanctum')->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'poi' => $this->transformPoiDetailed($poi, $locale, $user),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POI',
                'error' => $e->getMessage(),
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

            if (! $category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found',
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
                        'description' => $category->translation($locale)->description ?? '',
                    ],
                    'pois' => $transformedPois,
                    'pagination' => [
                        'current_page' => $pois->currentPage(),
                        'last_page' => $pois->lastPage(),
                        'per_page' => $pois->perPage(),
                        'total' => $pois->total(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch POIs by category',
                'error' => $e->getMessage(),
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
            'radius' => 'nullable|numeric|min:1|max:100', // radius in kilometers
        ]);

        try {
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 10); // Default 10km radius

            $query = Poi::published()
                ->with(['featuredImage', 'categories.translations', 'translations'])
                ->selectRaw('
                             *,
                             (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                             * cos(radians(longitude) - radians(?)) 
                             + sin(radians(?)) * sin(radians(latitude)))) AS distance
                         ', [$latitude, $longitude, $latitude])
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
                        'total_found' => $pois->count(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nearby POIs',
                'error' => $e->getMessage(),
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
            'contacts' => $this->transformContacts($poi->contacts ?? []),
            'has_contacts' => ! empty($poi->contacts),
            'contacts_count' => count($poi->contacts ?? []),
            'primary_contact' => $this->getPrimaryContact($poi->contacts ?? []),
            'featured_image' => $poi->featuredImage ? [
                'id' => $poi->featuredImage->id,
                'url' => $poi->featuredImage->getImageUrl(),
                'alt' => $poi->featuredImage->translation($locale)->alt_text ?? '',
            ] : null,
            'categories' => $poi->categories->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->translation($locale)->name ?? $category->name,
                    'slug' => $category->slug,
                    'level' => $category->level,
                    'parent_id' => $category->parent_id,
                    'parent_name' => $category->parent ? ($category->parent->translation($locale)->name ?? $category->parent->name) : null,
                ];
            }),
            'tour_operators' => $this->transformTourOperators($poi->tourOperators, $locale),
            'has_tour_operators' => $poi->tourOperators->isNotEmpty(),
            'tour_operators_count' => $poi->tourOperators->count(),
            'primary_tour_operator' => $this->getPrimaryTourOperator($poi->tourOperators, $locale),
            'favorites_count' => $poi->favorites_count,
            'is_favorited' => $user ? $poi->isFavoritedBy($user->id) : false,
            'created_at' => $poi->created_at->toISOString(),
            'updated_at' => $poi->updated_at->toISOString(),
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
                    'order' => $media->pivot->order ?? 0,
                ];
            }),
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
     * Get available contact types
     */
    private function getAvailableContactTypes(): array
    {
        return [
            ['key' => 'general', 'label' => 'Contact général'],
            ['key' => 'restaurant', 'label' => 'Restaurant'],
            ['key' => 'tour_operator', 'label' => 'Opérateur de tourisme'],
            ['key' => 'guide', 'label' => 'Guide local'],
            ['key' => 'accommodation', 'label' => 'Hébergement'],
            ['key' => 'park_office', 'label' => 'Bureau du parc'],
            ['key' => 'emergency', 'label' => 'Urgence'],
            ['key' => 'transport', 'label' => 'Transport'],
            ['key' => 'shop', 'label' => 'Boutique/Commerce'],
            ['key' => 'other', 'label' => 'Autre'],
        ];
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
                    'slug' => $category->slug,
                ];
            })
            ->toArray();
    }

    /**
     * Transform contacts array for API response
     */
    private function transformContacts(array $contacts): array
    {
        if (empty($contacts)) {
            return [];
        }

        return collect($contacts)->map(function ($contact) {
            return [
                'name' => $contact['name'] ?? '',
                'type' => $contact['type'] ?? 'general',
                'type_label' => $this->getContactTypeLabel($contact['type'] ?? 'general'),
                'phone' => $contact['phone'] ?? null,
                'email' => $contact['email'] ?? null,
                'website' => $contact['website'] ?? null,
                'address' => $contact['address'] ?? null,
                'description' => $contact['description'] ?? null,
                'is_primary' => $contact['is_primary'] ?? false,
            ];
        })->toArray();
    }

    /**
     * Get contact type label
     */
    private function getContactTypeLabel(string $type): string
    {
        $labels = [
            'general' => 'Contact général',
            'restaurant' => 'Restaurant',
            'tour_operator' => 'Opérateur de tourisme',
            'guide' => 'Guide local',
            'accommodation' => 'Hébergement',
            'park_office' => 'Bureau du parc',
            'emergency' => 'Urgence',
            'transport' => 'Transport',
            'shop' => 'Boutique/Commerce',
            'other' => 'Autre',
        ];

        return $labels[$type] ?? 'Type inconnu';
    }

    /**
     * Get primary contact from contacts array
     */
    private function getPrimaryContact(array $contacts): ?array
    {
        if (empty($contacts)) {
            return null;
        }

        // Try to find primary contact
        $primaryContact = collect($contacts)->firstWhere('is_primary', true);

        // If no primary found, use first contact
        $contact = $primaryContact ?: $contacts[0] ?? null;

        if (! $contact) {
            return null;
        }

        // Transform the contact for API response
        return [
            'name' => $contact['name'] ?? '',
            'type' => $contact['type'] ?? 'general',
            'type_label' => $this->getContactTypeLabel($contact['type'] ?? 'general'),
            'phone' => $contact['phone'] ?? null,
            'email' => $contact['email'] ?? null,
            'website' => $contact['website'] ?? null,
            'address' => $contact['address'] ?? null,
            'description' => $contact['description'] ?? null,
            'is_primary' => $contact['is_primary'] ?? false,
        ];
    }

    /**
     * Transform tour operators collection for API response
     */
    private function transformTourOperators($tourOperators, string $locale = 'fr'): array
    {
        if ($tourOperators->isEmpty()) {
            return [];
        }

        return $tourOperators->map(function ($tourOperator) use ($locale) {
            return [
                'id' => $tourOperator->id,
                'name' => $tourOperator->getTranslatedName($locale),
                'description' => $tourOperator->getTranslatedDescription($locale),
                'slug' => $tourOperator->slug,
                'service_type' => $tourOperator->pivot->service_type ?? 'guide',
                'service_type_label' => $this->getServiceTypeLabel($tourOperator->pivot->service_type ?? 'guide', $locale),
                'is_primary' => (bool) ($tourOperator->pivot->is_primary ?? false),
                'notes' => $tourOperator->pivot->notes ?? null,
                'phone' => $tourOperator->first_phone,
                'email' => $tourOperator->first_email,
                'website' => $tourOperator->website,
                'website_url' => $tourOperator->website_url,
                'address' => $tourOperator->address,
                'latitude' => $tourOperator->latitude,
                'longitude' => $tourOperator->longitude,
                'is_active' => $tourOperator->is_active,
                'featured' => $tourOperator->featured,
                'logo' => $tourOperator->logo ? [
                    'id' => $tourOperator->logo->id,
                    'url' => $tourOperator->logo->getImageUrl(),
                    'alt' => $tourOperator->logo->translation($locale)->alt_text ?? '',
                ] : null,
                'created_at' => $tourOperator->created_at->toISOString(),
                'updated_at' => $tourOperator->updated_at->toISOString(),
            ];
        })->toArray();
    }

    /**
     * Get primary tour operator for this POI
     */
    private function getPrimaryTourOperator($tourOperators, string $locale = 'fr'): ?array
    {
        if ($tourOperators->isEmpty()) {
            return null;
        }

        // Try to find primary tour operator
        $primaryOperator = $tourOperators->firstWhere('pivot.is_primary', true);

        // If no primary found, use first tour operator
        $operator = $primaryOperator ?: $tourOperators->first();

        if (! $operator) {
            return null;
        }

        // Transform the tour operator for API response
        return [
            'id' => $operator->id,
            'name' => $operator->getTranslatedName($locale),
            'description' => $operator->getTranslatedDescription($locale),
            'slug' => $operator->slug,
            'service_type' => $operator->pivot->service_type ?? 'guide',
            'service_type_label' => $this->getServiceTypeLabel($operator->pivot->service_type ?? 'guide', $locale),
            'is_primary' => true,
            'notes' => $operator->pivot->notes ?? null,
            'phone' => $operator->first_phone,
            'email' => $operator->first_email,
            'website' => $operator->website,
            'website_url' => $operator->website_url,
            'address' => $operator->address,
            'latitude' => $operator->latitude,
            'longitude' => $operator->longitude,
            'logo' => $operator->logo ? [
                'id' => $operator->logo->id,
                'url' => $operator->logo->getImageUrl(),
                'alt' => $operator->logo->translation($locale)->alt_text ?? '',
            ] : null,
        ];
    }

    /**
     * Get service type label in specified language
     */
    private function getServiceTypeLabel(string $type, string $locale = 'fr'): string
    {
        $types = [
            'fr' => [
                'guide' => 'Guide touristique',
                'transport' => 'Transport',
                'full_package' => 'Package complet',
                'accommodation' => 'Hébergement',
                'activity' => 'Activité spécialisée',
                'other' => 'Autre service',
            ],
            'en' => [
                'guide' => 'Tour Guide',
                'transport' => 'Transportation',
                'full_package' => 'Complete Package',
                'accommodation' => 'Accommodation',
                'activity' => 'Specialized Activity',
                'other' => 'Other Service',
            ],
            'ar' => [
                'guide' => 'مرشد سياحي',
                'transport' => 'نقل',
                'full_package' => 'حزمة كاملة',
                'accommodation' => 'إقامة',
                'activity' => 'نشاط متخصص',
                'other' => 'خدمة أخرى',
            ],
        ];

        $localeTypes = $types[$locale] ?? $types['fr'];

        return $localeTypes[$type] ?? ucfirst($type);
    }
}
