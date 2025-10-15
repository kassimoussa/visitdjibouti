<?php

namespace App\Http\Controllers\Api\Operator;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Get operator's events with reservations data.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('operator-api')->user();

            if (! $user || ! $user->canManageEvents()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée',
                ], 403);
            }

            $query = $user->managedEvents()
                ->with(['translations', 'featuredImage', 'categories.translations']);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->where('start_date', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->where('end_date', '<=', $request->date_to);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'start_date');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = min($request->get('per_page', 15), 50);
            $events = $query->paginate($perPage);

            $locale = $request->header('Accept-Language', 'fr');
            $transformedEvents = $events->getCollection()->map(function ($event) use ($locale) {
                return $this->transformEventForOperator($event, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'events' => $transformedEvents,
                    'pagination' => [
                        'current_page' => $events->currentPage(),
                        'last_page' => $events->lastPage(),
                        'per_page' => $events->perPage(),
                        'total' => $events->total(),
                        'from' => $events->firstItem(),
                        'to' => $events->lastItem(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des événements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific event details with reservations.
     */
    public function show(Request $request, Event $event): JsonResponse
    {
        try {
            $user = Auth::guard('operator-api')->user();

            if (! $user || ! $user->canManageEvents()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée',
                ], 403);
            }

            // Verify the event belongs to this operator
            if ($event->tour_operator_id !== $user->tour_operator_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            $event->load([
                'translations',
                'featuredImage',
                'media',
                'categories.translations',
                'tourOperator.translations',
            ]);

            $locale = $request->header('Accept-Language', 'fr');

            return response()->json([
                'success' => true,
                'data' => [
                    'event' => $this->transformEventDetailedForOperator($event, $locale),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get event reservations.
     */
    public function reservations(Request $request, Event $event): JsonResponse
    {
        try {
            $user = Auth::guard('operator-api')->user();

            if (! $user || ! $user->canViewReservations()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée',
                ], 403);
            }

            // Verify the event belongs to this operator
            if ($event->tour_operator_id !== $user->tour_operator_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            $query = $event->reservations()->with(['appUser']);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('confirmation_number', 'LIKE', "%{$search}%")
                        ->orWhere('guest_name', 'LIKE', "%{$search}%")
                        ->orWhere('guest_email', 'LIKE', "%{$search}%")
                        ->orWhereHas('appUser', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                        });
                });
            }

            $perPage = min($request->get('per_page', 20), 100);
            $reservations = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $transformedReservations = $reservations->getCollection()->map(function ($reservation) {
                return $this->transformReservationForOperator($reservation);
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
                    ],
                    'statistics' => [
                        'total' => $event->reservations()->count(),
                        'confirmed' => $event->confirmedReservations()->count(),
                        'pending' => $event->pendingReservations()->count(),
                        'cancelled' => $event->reservations()->where('status', 'cancelled')->count(),
                        'revenue' => $event->confirmedReservations()->sum('payment_amount'),
                        'pending_revenue' => $event->pendingReservations()->sum('payment_amount'),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des réservations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update event status or details.
     */
    public function update(Request $request, Event $event): JsonResponse
    {
        try {
            $user = Auth::guard('operator-api')->user();

            if (! $user || ! $user->canManageEvents()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission refusée',
                ], 403);
            }

            // Verify the event belongs to this operator
            if ($event->tour_operator_id !== $user->tour_operator_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Événement non trouvé',
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'sometimes|in:draft,published,cancelled',
                'contact_email' => 'sometimes|nullable|email',
                'contact_phone' => 'sometimes|nullable|string|max:20',
                'website_url' => 'sometimes|nullable|url',
                'ticket_url' => 'sometimes|nullable|url',
                'organizer' => 'sometimes|nullable|string|max:255',
                'max_participants' => 'sometimes|nullable|integer|min:1',
                'price' => 'sometimes|nullable|numeric|min:0',
            ]);

            $event->update($validated);

            $locale = $request->header('Accept-Language', 'fr');

            return response()->json([
                'success' => true,
                'message' => 'Événement mis à jour avec succès',
                'data' => [
                    'event' => $this->transformEventForOperator($event, $locale),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'événement',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform event for operator list view.
     */
    private function transformEventForOperator(Event $event, string $locale = 'fr'): array
    {
        $translation = $event->translation($locale);

        return [
            'id' => $event->id,
            'slug' => $event->slug,
            'title' => $translation->title ?? '',
            'short_description' => $translation->short_description ?? '',
            'location' => $event->location,
            'full_location' => $event->full_location,
            'start_date' => $event->start_date->toISOString(),
            'end_date' => $event->end_date->toISOString(),
            'start_time' => $event->start_time ? $event->start_time->format('H:i') : null,
            'end_time' => $event->end_time ? $event->end_time->format('H:i') : null,
            'formatted_date_range' => $event->formatted_date_range,
            'price' => $event->price,
            'is_free' => $event->price == 0,
            'status' => $event->status,
            'max_participants' => $event->max_participants,
            'current_participants' => $event->current_participants,
            'available_spots' => $event->available_spots,
            'is_sold_out' => $event->is_sold_out,
            'is_active' => $event->is_active,
            'is_ongoing' => $event->is_ongoing,
            'has_ended' => $event->has_ended,
            'organizer' => $event->organizer,
            'contact_email' => $event->contact_email,
            'contact_phone' => $event->contact_phone,
            'website_url' => $event->website_url,
            'ticket_url' => $event->ticket_url,
            'views_count' => $event->views_count,
            'reservations_count' => $event->reservations_count,
            'confirmed_reservations_count' => $event->confirmed_reservations_count,
            'featured_image' => $event->featuredImage ? [
                'id' => $event->featuredImage->id,
                'url' => $event->featuredImage->getImageUrl(),
                'alt' => $event->featuredImage->translation($locale)->alt_text ?? '',
            ] : null,
            'categories' => $event->categories->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->translation($locale)->name ?? $category->name,
                    'slug' => $category->slug,
                ];
            }),
            'created_at' => $event->created_at->toISOString(),
            'updated_at' => $event->updated_at->toISOString(),
        ];
    }

    /**
     * Transform event for operator detailed view.
     */
    private function transformEventDetailedForOperator(Event $event, string $locale = 'fr'): array
    {
        $translation = $event->translation($locale);

        $basic = $this->transformEventForOperator($event, $locale);

        return array_merge($basic, [
            'description' => $translation->description ?? '',
            'location_details' => $translation->location_details ?? '',
            'requirements' => $translation->requirements ?? '',
            'program' => $translation->program ?? '',
            'additional_info' => $translation->additional_info ?? '',
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'media' => $event->media->map(function ($media) use ($locale) {
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
     * Transform reservation for operator.
     */
    private function transformReservationForOperator(Reservation $reservation): array
    {
        return [
            'id' => $reservation->id,
            'confirmation_number' => $reservation->confirmation_number,
            'user_name' => $reservation->user_name,
            'user_email' => $reservation->user_email,
            'user_phone' => $reservation->user_phone,
            'number_of_people' => $reservation->number_of_people,
            'status' => $reservation->status,
            'payment_status' => $reservation->payment_status,
            'payment_amount' => $reservation->payment_amount,
            'special_requirements' => $reservation->special_requirements,
            'reservation_date' => $reservation->reservation_date?->toDateString(),
            'is_guest_reservation' => $reservation->isGuestReservation(),
            'can_be_cancelled' => $reservation->canBeCancelled(),
            'created_at' => $reservation->created_at->toISOString(),
            'user' => $reservation->appUser ? [
                'id' => $reservation->appUser->id,
                'name' => $reservation->appUser->getDisplayName(),
                'email' => $reservation->appUser->email,
                'phone' => $reservation->appUser->phone,
                'is_anonymous' => $reservation->appUser->is_anonymous,
            ] : null,
        ];
    }
}
