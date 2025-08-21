<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Poi;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    /**
     * Create a new reservation for a POI or Event
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            // Dynamic validation rules based on authentication status
            $rules = [
                'reservable_type' => ['required', 'string', Rule::in(['poi', 'event'])],
                'reservable_id' => 'required|integer',
                'reservation_date' => 'required|date|after_or_equal:today',
                'reservation_time' => 'nullable|date_format:H:i',
                'number_of_people' => 'required|integer|min:1|max:20',
                'special_requirements' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:500'
            ];

            // If user is not authenticated, require guest information
            if (!$user) {
                $rules['guest_name'] = 'required|string|max:255';
                $rules['guest_email'] = 'required|email|max:255';
                $rules['guest_phone'] = 'nullable|string|max:20';
            } else {
                $rules['guest_name'] = 'nullable|string|max:255';
                $rules['guest_email'] = 'nullable|email|max:255';
                $rules['guest_phone'] = 'nullable|string|max:20';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Determine the reservable model class
            $reservableClass = $data['reservable_type'] === 'poi' ? Poi::class : Event::class;
            
            // Find the reservable entity
            $reservable = $reservableClass::find($data['reservable_id']);
            
            if (!$reservable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            // Check if entity allows reservations
            if (!$reservable->isAvailableForReservation()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservations are not available for this resource'
                ], 400);
            }

            // For events, check availability
            if ($reservable instanceof Event) {
                $remainingSpots = $reservable->getRemainingSpots();
                if ($remainingSpots !== null && $remainingSpots < $data['number_of_people']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Only {$remainingSpots} spots remaining for this event"
                    ], 400);
                }
            }

            // Prepare reservation data
            $reservationData = [
                'reservable_id' => $reservable->id,
                'reservable_type' => get_class($reservable),
                'reservation_date' => $data['reservation_date'],
                'reservation_time' => $data['reservation_time'] ?? null,
                'number_of_people' => $data['number_of_people'],
                'special_requirements' => $data['special_requirements'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => 'pending'
            ];

            // Set user info
            if ($user) {
                $reservationData['app_user_id'] = $user->id;
            } else {
                $reservationData['guest_name'] = $data['guest_name'];
                $reservationData['guest_email'] = $data['guest_email'];
                $reservationData['guest_phone'] = $data['guest_phone'] ?? null;
            }

            // Create reservation
            $reservation = Reservation::create($reservationData);

            // Update participant count for events
            if ($reservable instanceof Event) {
                $reservable->increment('current_participants', $data['number_of_people']);
            }

            // Transform and return the reservation
            $locale = $request->header('Accept-Language', 'fr');
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'data' => [
                    'reservation' => $this->transformReservation($reservation, $locale)
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reservations
     */
    public function getUserReservations(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('sanctum')->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            $query = Reservation::forUser($user->id)
                                ->with(['reservable']);

            // Filter by type if specified
            if ($request->filled('type')) {
                $type = $request->get('type');
                if ($type === 'poi') {
                    $query->forPois();
                } elseif ($type === 'event') {
                    $query->forEvents();
                }
            }

            // Filter by status if specified
            if ($request->filled('status')) {
                $query->byStatus($request->get('status'));
            }

            // Filter upcoming or all
            if ($request->get('upcoming', false)) {
                $query->upcoming();
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 50);
            $reservations = $query->orderBy('reservation_date', 'desc')->paginate($perPage);

            // Transform data
            $locale = $request->header('Accept-Language', 'fr');
            $transformedReservations = $reservations->getCollection()->map(function ($reservation) use ($locale) {
                return $this->transformReservation($reservation, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'reservations' => $transformedReservations,
                    'pagination' => [
                        'current_page' => $reservations->currentPage(),
                        'last_page' => $reservations->lastPage(),
                        'per_page' => $reservations->perPage(),
                        'total' => $reservations->total(),
                        'from' => $reservations->firstItem(),
                        'to' => $reservations->lastItem(),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reservations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific reservation details
     */
    public function show(Request $request, string $confirmationNumber): JsonResponse
    {
        try {
            $reservation = Reservation::with(['reservable', 'appUser'])
                                    ->where('confirmation_number', $confirmationNumber)
                                    ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // Check if user has access to this reservation
            $user = Auth::guard('sanctum')->user();
            if ($user && $reservation->app_user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $locale = $request->header('Accept-Language', 'fr');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'reservation' => $this->transformReservationDetailed($reservation, $locale)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Request $request, string $confirmationNumber): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reservation = Reservation::with(['reservable'])
                                    ->where('confirmation_number', $confirmationNumber)
                                    ->first();

            if (!$reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservation not found'
                ], 404);
            }

            // Check if user has access to this reservation
            $user = Auth::guard('sanctum')->user();
            if ($user && $reservation->app_user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            if (!$reservation->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This reservation cannot be cancelled'
                ], 400);
            }

            // Cancel the reservation
            $reason = $request->get('reason');
            $reservation->cancel($reason);

            // Update participant count for events
            if ($reservation->reservable instanceof Event) {
                $reservation->reservable->decrement('current_participants', $reservation->number_of_people);
            }

            $locale = $request->header('Accept-Language', 'fr');
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully',
                'data' => [
                    'reservation' => $this->transformReservation($reservation, $locale)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform reservation for API response
     */
    private function transformReservation(Reservation $reservation, string $locale = 'fr'): array
    {
        $reservable = $reservation->reservable;
        $reservableName = '';
        $reservableType = '';

        if ($reservable instanceof Poi) {
            $translation = $reservable->translation($locale);
            $reservableName = $translation->name ?? '';
            $reservableType = 'poi';
        } elseif ($reservable instanceof Event) {
            $translation = $reservable->translation($locale);
            $reservableName = $translation->title ?? '';
            $reservableType = 'event';
        }

        return [
            'id' => $reservation->id,
            'confirmation_number' => $reservation->confirmation_number,
            'reservable_type' => $reservableType,
            'reservable_id' => $reservation->reservable_id,
            'reservable_name' => $reservableName,
            'reservation_date' => $reservation->reservation_date->toDateString(),
            'reservation_time' => $reservation->reservation_time?->format('H:i'),
            'number_of_people' => $reservation->number_of_people,
            'status' => $reservation->status,
            'user_name' => $reservation->user_name,
            'user_email' => $reservation->user_email,
            'user_phone' => $reservation->user_phone,
            'special_requirements' => $reservation->special_requirements,
            'payment_status' => $reservation->payment_status,
            'payment_amount' => $reservation->payment_amount,
            'can_be_cancelled' => $reservation->canBeCancelled(),
            'is_active' => $reservation->isActive(),
            'created_at' => $reservation->created_at->toISOString(),
            'updated_at' => $reservation->updated_at->toISOString()
        ];
    }

    /**
     * Transform reservation with detailed information
     */
    private function transformReservationDetailed(Reservation $reservation, string $locale = 'fr'): array
    {
        $basic = $this->transformReservation($reservation, $locale);
        
        return array_merge($basic, [
            'notes' => $reservation->notes,
            'contact_info' => $reservation->contact_info,
            'cancellation_reason' => $reservation->cancellation_reason,
            'cancelled_at' => $reservation->cancelled_at?->toISOString(),
            'confirmation_sent_at' => $reservation->confirmation_sent_at?->toISOString(),
            'reminder_sent_at' => $reservation->reminder_sent_at?->toISOString(),
            'reservable_details' => $this->getReservableDetails($reservation->reservable, $locale)
        ]);
    }

    /**
     * Get reservable entity details
     */
    private function getReservableDetails($reservable, string $locale = 'fr'): ?array
    {
        if ($reservable instanceof Poi) {
            $translation = $reservable->translation($locale);
            return [
                'type' => 'poi',
                'slug' => $reservable->slug,
                'name' => $translation->name ?? '',
                'address' => $translation->address ?? '',
                'region' => $reservable->region,
                'contact' => $reservable->contact,
                'website' => $reservable->website,
                'featured_image' => $reservable->featuredImage ? [
                    'url' => $reservable->featuredImage->getImageUrl(),
                    'alt' => $reservable->featuredImage->translation($locale)->alt_text ?? ''
                ] : null
            ];
        } elseif ($reservable instanceof Event) {
            $translation = $reservable->translation($locale);
            return [
                'type' => 'event',
                'slug' => $reservable->slug,
                'title' => $translation->title ?? '',
                'start_date' => $reservable->start_date->toDateString(),
                'end_date' => $reservable->end_date->toDateString(),
                'start_time' => $reservable->start_time?->format('H:i'),
                'end_time' => $reservable->end_time?->format('H:i'),
                'location' => $reservable->location,
                'price' => $reservable->price,
                'max_participants' => $reservable->max_participants,
                'remaining_spots' => $reservable->getRemainingSpots(),
                'featured_image' => $reservable->featuredImage ? [
                    'url' => $reservable->featuredImage->getImageUrl(),
                    'alt' => $reservable->featuredImage->translation($locale)->alt_text ?? ''
                ] : null
            ];
        }

        return null;
    }
}