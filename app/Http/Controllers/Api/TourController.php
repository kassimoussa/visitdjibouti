<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\TourSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    /**
     * Get all tours with pagination, filtering, and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Tour::active()
                ->with([
                    'tourOperator.translations',
                    'translations' => function ($q) {
                        $q->orderBy('start_date')->limit(3);
                    },
                ]);

            // Search by title or description
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Filter by tour operator
            if ($request->filled('operator_id')) {
                $query->where('tour_operator_id', $request->get('operator_id'));
            }

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }

            // Filter by difficulty level
            if ($request->filled('difficulty')) {
                $query->where('difficulty_level', $request->get('difficulty'));
            }

            // Filter by price range
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->get('min_price'));
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->get('max_price'));
            }

            // Filter by duration
            if ($request->filled('max_duration_hours')) {
                $query->where('duration_hours', '<=', $request->get('max_duration_hours'));
            }
            if ($request->filled('max_duration_days')) {
                $query->whereRaw('DATEDIFF(COALESCE(end_date, start_date), start_date) + 1 <= ?', [$request->get('max_duration_days')]);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('start_date', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('start_date', '<=', $request->get('date_to'));
            }

            // Filter by featured
            if ($request->filled('featured')) {
                $query->featured();
            }

            // Nearby tours (geolocation)
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $radius = $request->get('radius', 50);
                $query->nearby(
                    $request->get('latitude'),
                    $request->get('longitude'),
                    $radius
                );
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'title') {
                $locale = $request->header('Accept-Language', 'fr');
                $query->leftJoin('tour_translations', function ($join) use ($locale) {
                    $join->on('tours.id', '=', 'tour_translations.tour_id')
                        ->where('tour_translations.locale', '=', $locale);
                })->orderBy('tour_translations.title', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 50);
            $tours = $query->paginate($perPage);

            // Transform data
            $locale = $request->header('Accept-Language', 'fr');
            $transformedTours = $tours->getCollection()->map(function ($tour) use ($locale) {
                return $this->transformTour($tour, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'tours' => $transformedTours,
                    'pagination' => [
                        'current_page' => $tours->currentPage(),
                        'last_page' => $tours->lastPage(),
                        'per_page' => $tours->perPage(),
                        'total' => $tours->total(),
                        'from' => $tours->firstItem(),
                        'to' => $tours->lastItem(),
                    ],
                    'filters' => [
                        'operators' => $this->getAvailableOperators($locale),
                        'types' => $this->getAvailableTypes(),
                        'difficulties' => $this->getAvailableDifficulties(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tours',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific tour by ID or slug
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        try {
            $query = Tour::active()
                ->with([
                    'tourOperator.translations',
                    'translations',
                    'featuredImage',
                    'media',
                ]);

            // Try to find by ID first, then by slug
            $tour = is_numeric($identifier)
                ? $query->find($identifier)
                : $query->where('slug', $identifier)->first();

            if (! $tour) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tour not found',
                ], 404);
            }

            // Increment view count
            $tour->incrementViews();

            $locale = $request->header('Accept-Language', 'fr');

            return response()->json([
                'success' => true,
                'data' => [
                    'tour' => $this->transformTourDetailed($tour, $locale),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tour',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get schedules for a specific tour
     */
    /* public function schedules(Request $request, Tour $tour): JsonResponse
    {
        try {
            if ($tour->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tour not available'
                ], 404);
            }

            $query = $tour->upcomingSchedules()->with(['tour.translations']);

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->where('start_date', '>=', $request->get('from_date'));
            }
            if ($request->filled('to_date')) {
                $query->where('start_date', '<=', $request->get('to_date'));
            }

            // Filter by available spots
            if ($request->filled('min_spots')) {
                $query->whereRaw('available_spots - booked_spots >= ?', [$request->get('min_spots')]);
            }

            // Filter by guide language
            if ($request->filled('guide_language')) {
                $query->whereJsonContains('guide_languages', $request->get('guide_language'));
            }

            $schedules = $query->orderBy('start_date')->get();

            $locale = $request->header('Accept-Language', 'fr');
            $transformedSchedules = $schedules->map(function ($schedule) use ($locale) {
                return $this->transformSchedule($schedule, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'schedules' => $transformedSchedules,
                    'tour' => $this->transformTour($tour, $locale)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    } */

    /**
     * Book a tour schedule
     */
    /* public function book(Request $request, TourSchedule $schedule): JsonResponse
    {
        try {
            // Check if schedule exists and is available
            if (!$schedule || $schedule->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found or not available for booking'
                ], 404);
            }

            // Check if tour is active
            if ($schedule->tour->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tour is not active'
                ], 422);
            }

            // Get user from auth or request data
            $user = $request->user();
            $participants_count = $request->get('participants_count', 1);

            // Check available spots
            if (!$schedule->canAcceptReservation($participants_count)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough spots available or booking deadline passed'
                ], 422);
            }

            // Validation rules
            $rules = [
                'participants_count' => 'integer|min:1|max:20',
                'special_requirements' => 'nullable|string|max:500'
            ];

            // If user is not authenticated, require contact info
            if (!$user) {
                $rules = array_merge($rules, [
                    'user_name' => 'required|string|max:255',
                    'user_email' => 'required|email|max:255',
                    'user_phone' => 'nullable|string|max:20'
                ]);
            }

            $validated = $request->validate($rules);

            // Check if authenticated user already has a booking for this schedule
            if ($user) {
                $existingReservation = Reservation::where('reservable_id', $schedule->id)
                                                  ->where('reservable_type', TourSchedule::class)
                                                  ->where('app_user_id', $user->id)
                                                  ->whereIn('status', ['confirmed', 'pending'])
                                                  ->first();

                if ($existingReservation) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You already have a booking for this tour schedule'
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Create reservation
            $reservationData = [
                'reservable_id' => $schedule->id,
                'reservable_type' => TourSchedule::class,
                'reservation_date' => $schedule->start_date,
                'reservation_time' => $schedule->start_time,
                'number_of_people' => $participants_count,
                'status' => 'pending',
                'payment_status' => $schedule->effective_price > 0 ? 'pending' : 'not_required',
                'payment_amount' => $schedule->effective_price * $participants_count,
                'special_requirements' => $validated['special_requirements'] ?? null,
            ];

            if ($user) {
                $reservationData['app_user_id'] = $user->id;
            } else {
                $reservationData = array_merge($reservationData, [
                    'guest_name' => $validated['user_name'],
                    'guest_email' => $validated['user_email'],
                    'guest_phone' => $validated['user_phone'] ?? null
                ]);
            }

            $reservation = Reservation::create($reservationData);

            // If free tour, auto-confirm reservation
            if ($schedule->effective_price == 0) {
                $reservation->confirm();
                $schedule->addBooking($participants_count);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tour booking successful',
                'data' => [
                    'reservation' => $this->transformReservation($reservation),
                    'payment_required' => $schedule->effective_price > 0,
                    'total_amount' => $reservation->payment_amount
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Tour booking failed',
                'error' => $e->getMessage()
            ], 500);
        }
    } */

    /**
     * Cancel a tour booking
     */
    /* public function cancelBooking(Request $request, int $reservationId): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to cancel booking'
                ], 401);
            }

            $reservation = Reservation::where('id', $reservationId)
                                     ->where('reservable_type', TourSchedule::class)
                                     ->where('app_user_id', $user->id)
                                     ->whereIn('status', ['confirmed', 'pending'])
                                     ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tour booking not found'
                ], 404);
            }

            $request->validate([
                'reason' => 'nullable|string|max:255'
            ]);

            $schedule = $reservation->reservable;
            if (!$schedule || !$schedule->canBeCancelled) {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled'
                ], 422);
            }

            $reservation->cancel($request->get('reason'));
            $schedule->removeBooking($reservation->number_of_people);

            return response()->json([
                'success' => true,
                'message' => 'Tour booking cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking',
                'error' => $e->getMessage()
            ], 500);
        }
    } */

    /**
     * Get user's tour bookings
     */
    /*  public function myBookings(Request $request): JsonResponse
     {
         try {
             $user = $request->user();

             if (!$user) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Authentication required'
                 ], 401);
             }

             $query = Reservation::where('app_user_id', $user->id)
                                 ->where('reservable_type', TourSchedule::class)
                                 ->with(['reservable.tour.translations', 'reservable.tour.tourOperator.translations']);

             // Filter by status
             if ($request->filled('status')) {
                 $query->where('status', $request->get('status'));
             }

             $reservations = $query->orderBy('created_at', 'desc')->paginate(20);

             $locale = $request->header('Accept-Language', 'fr');
             $transformedReservations = $reservations->getCollection()->map(function ($reservation) use ($locale) {
                 return $this->transformReservationWithTour($reservation, $locale);
             });

             return response()->json([
                 'success' => true,
                 'data' => [
                     'bookings' => $transformedReservations,
                     'pagination' => [
                         'current_page' => $reservations->currentPage(),
                         'last_page' => $reservations->lastPage(),
                         'per_page' => $reservations->perPage(),
                         'total' => $reservations->total()
                     ]
                 ]
             ]);

         } catch (\Exception $e) {
             return response()->json([
                 'success' => false,
                 'message' => 'Failed to fetch bookings',
                 'error' => $e->getMessage()
             ], 500);
         }
     } */

    /**
     * Transform tour for list view
     */
    private function transformTour(Tour $tour, string $locale = 'fr'): array
    {
        $translation = $tour->translation($locale);

        return [
            'id' => $tour->id,
            'slug' => $tour->slug,
            'title' => $translation->title ?? '',
            'type' => $tour->type,
            'type_label' => $tour->type_label,
            'difficulty_level' => $tour->difficulty_level,
            'difficulty_label' => $tour->difficulty_label,
            'start_date' => $tour->start_date?->toDateString(),
            'end_date' => $tour->end_date?->toDateString(),
            'formatted_date_range' => $tour->formatted_date_range,
            'duration' => [
                'hours' => $tour->duration_hours,
                'days' => $tour->duration_in_days,
                'formatted' => $tour->formatted_duration,
            ],
            'price' => $tour->price,
            'formatted_price' => $tour->formatted_price,
            'is_free' => $tour->is_free,
            'is_featured' => $tour->is_featured,
            'max_participants' => $tour->max_participants,
            'min_participants' => $tour->min_participants,
            'available_spots' => $tour->available_spots,
            'tour_operator' => [
                'id' => $tour->tourOperator->id,
                'name' => $tour->tourOperator->getTranslatedName($locale),
                'slug' => $tour->tourOperator->slug,
            ],
            'featured_image' => $tour->featuredImage ? [
                'id' => $tour->featuredImage->id,
                'url' => $tour->featuredImage->getImageUrl(),
                'alt' => $tour->featuredImage->translation($locale)->alt_text ?? '',
            ] : null,
            'created_at' => $tour->created_at->toISOString(),
            'updated_at' => $tour->updated_at->toISOString(),
        ];
    }

    /**
     * Transform tour for detailed view
     */
    private function transformTourDetailed(Tour $tour, string $locale = 'fr'): array
    {
        $translation = $tour->translation($locale);
        $basic = $this->transformTour($tour, $locale);

        return array_merge($basic, [
            'description' => $translation->description ?? '',
            'itinerary' => $translation->itinerary ?? '',
            'meeting_point' => [
                'latitude' => $tour->meeting_point_latitude,
                'longitude' => $tour->meeting_point_longitude,
                'address' => $tour->meeting_point_address,
                'description' => $translation->meeting_point_description ?? '',
            ],
            'highlights' => $translation->highlights ?? [],
            'what_to_bring' => $translation->what_to_bring ?? [],
            'age_restrictions' => [
                'min' => $tour->age_restriction_min,
                'max' => $tour->age_restriction_max,
                'text' => $tour->age_restrictions_text,
            ],
            'weather_dependent' => $tour->weather_dependent,
            'views_count' => $tour->views_count,
            'media' => $tour->media->map(function ($media) use ($locale) {
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
     * Transform reservation
     */
    private function transformReservation(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'confirmation_number' => $reservation->confirmation_number,
            'number_of_people' => $reservation->number_of_people,
            'status' => $reservation->status,
            'payment_status' => $reservation->payment_status,
            'payment_amount' => $reservation->payment_amount,
            'special_requirements' => $reservation->special_requirements,
            'reservation_date' => $reservation->reservation_date?->toDateString(),
            'user_name' => $reservation->user_name,
            'user_email' => $reservation->user_email,
            'created_at' => $reservation->created_at->toISOString(),
        ];
    }

    /**
     * Get available operators
     */
    private function getAvailableOperators(string $locale = 'fr'): array
    {
        return TourOperator::active()
            ->whereHas('tours', function ($q) {
                $q->active();
            })
            ->with('translations')
            ->get()
            ->map(function ($operator) use ($locale) {
                return [
                    'id' => $operator->id,
                    'name' => $operator->getTranslatedName($locale),
                    'slug' => $operator->slug,
                ];
            })
            ->toArray();
    }

    /**
     * Get available types
     */
    private function getAvailableTypes(): array
    {
        return [
            ['value' => 'poi', 'label' => 'Visite de site'],
            ['value' => 'event', 'label' => 'Accompagnement événement'],
            ['value' => 'mixed', 'label' => 'Circuit mixte'],
            ['value' => 'cultural', 'label' => 'Culturel'],
            ['value' => 'adventure', 'label' => 'Aventure'],
            ['value' => 'nature', 'label' => 'Nature'],
            ['value' => 'gastronomic', 'label' => 'Gastronomique'],
        ];
    }

    /**
     * Get available difficulties
     */
    private function getAvailableDifficulties(): array
    {
        return [
            ['value' => 'easy', 'label' => 'Facile'],
            ['value' => 'moderate', 'label' => 'Modéré'],
            ['value' => 'difficult', 'label' => 'Difficile'],
            ['value' => 'expert', 'label' => 'Expert'],
        ];
    }
}
