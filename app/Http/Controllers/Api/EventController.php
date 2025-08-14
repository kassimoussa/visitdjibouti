<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Get all events with pagination, filtering, and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Event::published()
                          ->with(['featuredImage', 'categories.translations', 'translations']);

            // Search by title
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('category_id')) {
                $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('categories.id', $request->get('category_id'));
                });
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->where('start_date', '>=', $request->get('date_from'));
            }
            
            if ($request->filled('date_to')) {
                $query->where('end_date', '<=', $request->get('date_to'));
            }

            // Filter by event status
            $status = $request->get('status');
            if ($status === 'upcoming') {
                $query->upcoming();
            } elseif ($status === 'ongoing') {
                $query->ongoing();
            }

            // Filter by featured
            if ($request->filled('featured')) {
                $query->featured();
            }

            // Filter by location
            if ($request->filled('location')) {
                $query->where('location', 'LIKE', '%' . $request->get('location') . '%');
            }

            // Sort options
            $sortBy = $request->get('sort_by', 'start_date');
            $sortOrder = $request->get('sort_order', 'asc');

            if ($sortBy === 'title') {
                // Sort by translated title
                $locale = $request->header('Accept-Language', 'fr');
                $query->leftJoin('event_translations', function ($join) use ($locale) {
                    $join->on('events.id', '=', 'event_translations.event_id')
                         ->where('event_translations.locale', '=', $locale);
                })->orderBy('event_translations.title', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 50);
            $events = $query->paginate($perPage);

            // Transform data
            $locale = $request->header('Accept-Language', 'fr');
            $transformedEvents = $events->getCollection()->map(function ($event) use ($locale) {
                return $this->transformEvent($event, $locale);
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
                    'filters' => [
                        'categories' => $this->getAvailableCategories($locale)
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific event by ID or slug
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        try {
            $query = Event::published()
                          ->with([
                              'featuredImage', 
                              'media', 
                              'categories.translations', 
                              'translations'
                          ]);

            // Try to find by ID first, then by slug
            $event = is_numeric($identifier) 
                ? $query->find($identifier)
                : $query->where('slug', $identifier)->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found'
                ], 404);
            }

            // Increment view count
            $event->incrementViews();

            $locale = $request->header('Accept-Language', 'fr');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'event' => $this->transformEventDetailed($event, $locale)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register for an event
     */
    public function register(Request $request, Event $event): JsonResponse
    {
        try {
            // Check if event exists and is published
            if (!$event || $event->status !== 'published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found or not available for registration'
                ], 404);
            }

            // Check if event has ended
            if ($event->has_ended) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration closed - event has ended'
                ], 422);
            }

            // Get user from auth or request data
            $user = $request->user();
            $participants_count = $request->get('participants_count', 1);

            // Check if event is sold out
            if ($event->max_participants && 
                ($event->current_participants + $participants_count) > $event->max_participants) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event is sold out or not enough spots available'
                ], 422);
            }

            // Validation rules
            $rules = [
                'participants_count' => 'integer|min:1|max:10',
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

            // Check if authenticated user already registered
            if ($user) {
                $existingRegistration = EventRegistration::where('event_id', $event->id)
                                                        ->where('user_id', $user->id)
                                                        ->whereIn('status', ['confirmed', 'pending'])
                                                        ->first();
                
                if ($existingRegistration) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are already registered for this event'
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Create registration
            $registrationData = [
                'event_id' => $event->id,
                'participants_count' => $participants_count,
                'status' => 'pending',
                'payment_status' => $event->price > 0 ? 'pending' : 'paid',
                'payment_amount' => $event->price * $participants_count,
                'special_requirements' => $validated['special_requirements'] ?? null,
            ];

            if ($user) {
                // Authenticated user
                $registrationData = array_merge($registrationData, [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_phone' => $user->phone ?? null
                ]);
            } else {
                // Guest registration
                $registrationData = array_merge($registrationData, [
                    'user_name' => $validated['user_name'],
                    'user_email' => $validated['user_email'],
                    'user_phone' => $validated['user_phone'] ?? null
                ]);
            }

            $registration = EventRegistration::create($registrationData);

            // If free event, auto-confirm registration
            if ($event->price == 0) {
                $registration->confirm();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'registration' => $this->transformRegistration($registration),
                    'payment_required' => $event->price > 0,
                    'total_amount' => $registration->total_amount
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel event registration
     */
    public function cancelRegistration(Request $request, Event $event): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required to cancel registration'
                ], 401);
            }

            $registration = EventRegistration::where('event_id', $event->id)
                                           ->where('user_id', $user->id)
                                           ->whereIn('status', ['confirmed', 'pending'])
                                           ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not found'
                ], 404);
            }

            $request->validate([
                'reason' => 'nullable|string|max:255'
            ]);

            $registration->cancel($request->get('reason'));

            return response()->json([
                'success' => true,
                'message' => 'Registration cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's event registrations
     */
    public function myRegistrations(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            $query = EventRegistration::where('user_id', $user->id)
                                     ->with(['event.featuredImage', 'event.translations']);

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            $registrations = $query->orderBy('created_at', 'desc')->paginate(20);

            $locale = $request->header('Accept-Language', 'fr');
            $transformedRegistrations = $registrations->getCollection()->map(function ($registration) use ($locale) {
                return $this->transformRegistrationWithEvent($registration, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'registrations' => $transformedRegistrations,
                    'pagination' => [
                        'current_page' => $registrations->currentPage(),
                        'last_page' => $registrations->lastPage(),
                        'per_page' => $registrations->perPage(),
                        'total' => $registrations->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch registrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform event for list view
     */
    private function transformEvent(Event $event, string $locale = 'fr'): array
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
            'is_featured' => $event->is_featured,
            'max_participants' => $event->max_participants,
            'current_participants' => $event->current_participants,
            'available_spots' => $event->available_spots,
            'is_sold_out' => $event->is_sold_out,
            'is_active' => $event->is_active,
            'is_ongoing' => $event->is_ongoing,
            'has_ended' => $event->has_ended,
            'organizer' => $event->organizer,
            'featured_image' => $event->featuredImage ? [
                'id' => $event->featuredImage->id,
                'url' => $event->featuredImage->getImageUrl(),
                'alt' => $event->featuredImage->translation($locale)->alt_text ?? ''
            ] : null,
            'categories' => $event->categories->map(function ($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->translation($locale)->name ?? $category->name,
                    'slug' => $category->slug
                ];
            }),
            'created_at' => $event->created_at->toISOString(),
            'updated_at' => $event->updated_at->toISOString()
        ];
    }

    /**
     * Transform event for detailed view
     */
    private function transformEventDetailed(Event $event, string $locale = 'fr'): array
    {
        $translation = $event->translation($locale);
        
        $basic = $this->transformEvent($event, $locale);
        
        return array_merge($basic, [
            'description' => $translation->description ?? '',
            'location_details' => $translation->location_details ?? '',
            'requirements' => $translation->requirements ?? '',
            'program' => $translation->program ?? '',
            'additional_info' => $translation->additional_info ?? '',
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'contact_email' => $event->contact_email,
            'contact_phone' => $event->contact_phone,
            'website_url' => $event->website_url,
            'ticket_url' => $event->ticket_url,
            'views_count' => $event->views_count,
            'media' => $event->media->map(function ($media) use ($locale) {
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
     * Transform registration
     */
    private function transformRegistration(EventRegistration $registration): array
    {
        return [
            'id' => $registration->id,
            'registration_number' => $registration->registration_number,
            'participants_count' => $registration->participants_count,
            'status' => $registration->status,
            'payment_status' => $registration->payment_status,
            'payment_amount' => $registration->payment_amount,
            'total_amount' => $registration->total_amount,
            'special_requirements' => $registration->special_requirements,
            'created_at' => $registration->created_at->toISOString()
        ];
    }

    /**
     * Transform registration with event details
     */
    private function transformRegistrationWithEvent(EventRegistration $registration, string $locale = 'fr'): array
    {
        $basic = $this->transformRegistration($registration);
        
        return array_merge($basic, [
            'event' => $this->transformEvent($registration->event, $locale)
        ]);
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