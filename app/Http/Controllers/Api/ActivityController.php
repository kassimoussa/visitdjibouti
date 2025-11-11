<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ActivityRegistrationCancelled;
use App\Mail\ActivityRegistrationReceived;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    /**
     * Get list of activities with filters
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->header('Accept-Language', 'fr');

        $query = Activity::with(['tourOperator.logo', 'tourOperator.translations', 'featuredImage', 'media', 'translations'])
            ->where('status', 'active');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('has_spots')) {
            $query->where(function ($q) {
                $q->whereNull('max_participants')
                    ->orWhereRaw('current_participants < max_participants');
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'popularity') {
            $query->orderBy('registrations_count', 'desc');
        } else {
            $query->latest();
        }

        $perPage = min($request->get('per_page', 15), 50);
        $activities = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $activities->map(function ($activity) use ($locale) {
                return $this->formatActivity($activity, $locale);
            }),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    /**
     * Get activity details by ID or slug
     */
    public function show(Request $request, $identifier): JsonResponse
    {
        $locale = $request->header('Accept-Language', 'fr');

        $activity = Activity::with(['tourOperator.logo', 'tourOperator.translations', 'featuredImage', 'media', 'translations'])
            ->where('status', 'active')
            ->where(function ($query) use ($identifier) {
                $query->where('id', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->first();

        if (! $activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activité non trouvée',
            ], 404);
        }

        // Incrémenter les vues
        $activity->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $this->formatActivity($activity, $locale, true),
        ]);
    }

    /**
     * Get nearby activities based on GPS coordinates
     */
    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $locale = $request->header('Accept-Language', 'fr');
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->get('radius', 50); // km

        $activities = Activity::with(['tourOperator.logo', 'tourOperator.translations', 'featuredImage', 'media', 'translations'])
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                *,
                (6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance
            ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activities->map(function ($activity) use ($locale) {
                $formatted = $this->formatActivity($activity, $locale);
                $formatted['distance_km'] = round($activity->distance, 2);

                return $formatted;
            }),
        ]);
    }

    /**
     * Register for an activity
     */
    public function register(Request $request, Activity $activity): JsonResponse
    {
        if ($activity->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Cette activité n\'est pas disponible pour l\'inscription',
            ], 400);
        }

        // Check if user is authenticated
        $user = Auth::guard('sanctum')->user();

        // Build validation rules dynamically
        $rules = [
            'number_of_people' => 'required|integer|min:1',
            'preferred_date' => 'nullable|date|after:today',
            'special_requirements' => 'nullable|string|max:500',
            'medical_conditions' => 'nullable|string|max:500',
        ];

        // Add guest fields validation only if user is not authenticated
        if (!$user) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
            $rules['guest_phone'] = 'nullable|string|max:50';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Vérifier si l'utilisateur a déjà une inscription active
        if ($user && ! $activity->canUserRegister($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez déjà une inscription active pour cette activité',
            ], 400);
        }

        // Vérifier la disponibilité
        if (! $activity->hasAvailableSpots($request->number_of_people)) {
            return response()->json([
                'success' => false,
                'message' => 'Pas assez de places disponibles',
            ], 400);
        }

        // Créer l'inscription
        $registration = ActivityRegistration::create([
            'activity_id' => $activity->id,
            'app_user_id' => $user?->id,
            'guest_name' => $user ? null : $request->guest_name,
            'guest_email' => $user ? null : $request->guest_email,
            'guest_phone' => $user ? null : $request->guest_phone,
            'number_of_people' => $request->number_of_people,
            'preferred_date' => $request->preferred_date,
            'special_requirements' => $request->special_requirements,
            'medical_conditions' => $request->medical_conditions,
            'status' => 'pending',
            'total_price' => $activity->price * $request->number_of_people,
        ]);

        // Envoyer email de notification à l'opérateur
        try {
            $operatorEmail = $activity->tourOperator->email;
            if ($operatorEmail) {
                Mail::to($operatorEmail)->send(new ActivityRegistrationReceived($registration));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email à l\'opérateur: '.$e->getMessage());
        }

        $locale = $request->header('Accept-Language', 'fr');

        return response()->json([
            'success' => true,
            'message' => 'Inscription enregistrée avec succès. En attente de confirmation.',
            'data' => $this->formatRegistration($registration, $locale),
        ], 201);
    }

    /**
     * Get user's activity registrations
     */
    public function myRegistrations(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $locale = $request->header('Accept-Language', 'fr');

        $query = ActivityRegistration::with(['activity.translations', 'activity.featuredImage', 'activity.tourOperator'])
            ->where('app_user_id', $user->id);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $registrations->map(function ($registration) use ($locale) {
                return $this->formatRegistration($registration, $locale);
            }),
            'meta' => [
                'current_page' => $registrations->currentPage(),
                'last_page' => $registrations->lastPage(),
                'per_page' => $registrations->perPage(),
                'total' => $registrations->total(),
            ],
        ]);
    }

    /**
     * Cancel a registration
     */
    public function cancelRegistration(Request $request, ActivityRegistration $registration): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user || $registration->app_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        if (! $registration->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Cette inscription ne peut pas être annulée',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $registration->cancel($request->get('reason'), 'user');

        // Envoyer email à l'opérateur
        try {
            $operatorEmail = $registration->activity->tourOperator->email;
            if ($operatorEmail) {
                Mail::to($operatorEmail)->send(new ActivityRegistrationCancelled($registration, 'user'));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email d\'annulation à l\'opérateur: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Inscription annulée avec succès',
        ]);
    }

    /**
     * Permanently delete a cancelled activity registration
     */
    public function deleteRegistration(Request $request, ActivityRegistration $registration): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user || $registration->app_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        // Only allow deletion of cancelled registrations
        if ($registration->status !== 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les inscriptions annulées peuvent être supprimées. Veuillez d\'abord annuler l\'inscription.',
                'current_status' => $registration->status,
            ], 400);
        }

        try {
            $registration->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inscription supprimée définitivement',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'inscription: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la suppression de l\'inscription',
            ], 500);
        }
    }

    /**
     * Format activity for API response
     */
    private function formatActivity(Activity $activity, string $locale, bool $detailed = false): array
    {
        $translation = $activity->translation($locale);

        $data = [
            'id' => $activity->id,
            'slug' => $activity->slug,
            'title' => $translation->title ?? $activity->title,
            'short_description' => $translation->short_description ?? '',
            'price' => (float) $activity->price,
            'currency' => $activity->currency,
            'difficulty_level' => $activity->difficulty_level,
            'difficulty_label' => $activity->difficulty_label,
            'duration' => [
                'hours' => $activity->duration_hours,
                'minutes' => $activity->duration_minutes,
                'formatted' => $activity->formatted_duration,
            ],
            'region' => $activity->region,
            'location' => [
                'address' => $activity->location_address,
                'latitude' => $activity->latitude ? (float) $activity->latitude : null,
                'longitude' => $activity->longitude ? (float) $activity->longitude : null,
            ],
            'participants' => [
                'min' => $activity->min_participants,
                'max' => $activity->max_participants,
                'current' => $activity->current_participants,
                'available_spots' => $activity->max_participants ? ($activity->max_participants - $activity->current_participants) : null,
            ],
            'age_restrictions' => [
                'has_restrictions' => $activity->has_age_restrictions,
                'min_age' => $activity->min_age,
                'max_age' => $activity->max_age,
                'text' => $activity->age_restrictions_text,
            ],
            'featured_image' => $activity->featuredImage ? [
                'id' => $activity->featuredImage->id,
                'url' => asset($activity->featuredImage->path),
                'thumbnail_url' => asset($activity->featuredImage->thumbnail_path ?? $activity->featuredImage->path),
            ] : null,
            'gallery' => $activity->media->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => asset($media->path),
                    'thumbnail_url' => asset($media->thumbnail_path ?? $media->path),
                ];
            }),
            'tour_operator' => [
                'id' => $activity->tourOperator->id,
                'name' => $activity->tourOperator->getTranslatedName($locale),
                'slug' => $activity->tourOperator->slug,
                'email' => $activity->tourOperator->email,
                'phone' => $activity->tourOperator->phone,
                'logo' => $activity->tourOperator->logo ? asset($activity->tourOperator->logo->path) : null,
            ],
            'is_featured' => $activity->is_featured,
            'weather_dependent' => $activity->weather_dependent,
            'views_count' => $activity->views_count,
            'registrations_count' => $activity->registrations_count,
        ];

        if ($detailed) {
            $data['description'] = $translation->description ?? '';
            $data['what_to_bring'] = $translation->what_to_bring ?? '';
            $data['meeting_point_description'] = $translation->meeting_point_description ?? '';
            $data['additional_info'] = $translation->additional_info ?? '';
            $data['physical_requirements'] = $activity->physical_requirements ?? [];
            $data['certifications_required'] = $activity->certifications_required ?? [];
            $data['equipment_provided'] = $activity->equipment_provided ?? [];
            $data['equipment_required'] = $activity->equipment_required ?? [];
            $data['includes'] = $activity->includes ?? [];
            $data['cancellation_policy'] = $activity->cancellation_policy;
        }

        return $data;
    }

    /**
     * Format registration for API response
     */
    private function formatRegistration(ActivityRegistration $registration, string $locale): array
    {
        $activity = $registration->activity;
        $translation = $activity->translation($locale);

        return [
            'id' => $registration->id,
            'activity' => [
                'id' => $activity->id,
                'slug' => $activity->slug,
                'title' => $translation->title ?? $activity->title,
                'price' => (float) $activity->price,
                'currency' => $activity->currency,
                'featured_image' => $activity->featuredImage ? [
                    'url' => asset($activity->featuredImage->path),
                    'thumbnail_url' => asset($activity->featuredImage->thumbnail_path ?? $activity->featuredImage->path),
                ] : null,
                'tour_operator' => [
                    'name' => $activity->tourOperator->name,
                    'phone' => $activity->tourOperator->phone,
                ],
            ],
            'number_of_people' => $registration->number_of_people,
            'preferred_date' => $registration->preferred_date?->format('Y-m-d'),
            'special_requirements' => $registration->special_requirements,
            'medical_conditions' => $registration->medical_conditions,
            'status' => $registration->status,
            'status_label' => $registration->status_label,
            'payment_status' => $registration->payment_status,
            'payment_status_label' => $registration->payment_status_label,
            'total_price' => (float) $registration->total_price,
            'created_at' => $registration->created_at->toIso8601String(),
            'confirmed_at' => $registration->confirmed_at?->toIso8601String(),
            'completed_at' => $registration->completed_at?->toIso8601String(),
            'cancelled_at' => $registration->cancelled_at?->toIso8601String(),
            'cancellation_reason' => $registration->cancellation_reason,
        ];
    }
}
