<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Poi;
use App\Models\Review;
use App\Models\ReviewHelpfulVote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get reviews for a specific POI
     */
    public function index(Request $request, Poi $poi): JsonResponse
    {
        $query = $poi->reviews()
            ->with(['appUser'])
            ->where('is_approved', true);

        // Filtres
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('verified_only')) {
            $query->where('is_verified', true);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'recent');
        switch ($sortBy) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $perPage = min($request->get('per_page', 15), 50);
        $reviews = $query->paginate($perPage);

        $user = Auth::guard('sanctum')->user();
        $guestIdentifier = $request->header('X-Guest-ID');

        return response()->json([
            'success' => true,
            'data' => $reviews->map(function ($review) use ($user, $guestIdentifier) {
                return $this->formatReview($review, $user?->id, $guestIdentifier);
            }),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
            'statistics' => [
                'average_rating' => $poi->average_rating,
                'total_reviews' => $poi->reviews_count,
                'rating_distribution' => $poi->rating_distribution,
            ],
        ]);
    }

    /**
     * Create a new review for a POI
     */
    public function store(Request $request, Poi $poi): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Validation
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
        ];

        // Si l'utilisateur n'est pas connecté, exiger nom et email
        if (! $user) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Vérifier si l'utilisateur a déjà laissé un avis
        if ($user) {
            $existingReview = $poi->reviews()->where('app_user_id', $user->id)->first();
            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous avez déjà laissé un avis pour ce lieu',
                ], 400);
            }
        }

        // Créer l'avis
        $review = $poi->reviews()->create([
            'app_user_id' => $user?->id,
            'guest_name' => $user ? null : $request->guest_name,
            'guest_email' => $user ? null : $request->guest_email,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approuvé par défaut, peut être modéré
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Votre avis a été ajouté avec succès',
            'data' => $this->formatReview($review, $user?->id),
        ], 201);
    }

    /**
     * Update a review
     */
    public function update(Request $request, Review $review): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Vérifier que l'utilisateur est propriétaire de l'avis
        if (! $user || $review->app_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Votre avis a été mis à jour',
            'data' => $this->formatReview($review, $user->id),
        ]);
    }

    /**
     * Delete a review
     */
    public function destroy(Review $review): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Vérifier que l'utilisateur est propriétaire de l'avis
        if (! $user || $review->app_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Votre avis a été supprimé',
        ]);
    }

    /**
     * Mark a review as helpful
     */
    public function markHelpful(Request $request, Review $review): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $guestIdentifier = $request->header('X-Guest-ID');

        if (! $user && ! $guestIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Identification requise',
            ], 400);
        }

        // Vérifier si déjà voté
        if ($review->isHelpfulBy($user?->id, $guestIdentifier)) {
            // Retirer le vote
            $query = ReviewHelpfulVote::where('review_id', $review->id);
            if ($user) {
                $query->where('app_user_id', $user->id);
            } else {
                $query->where('guest_identifier', $guestIdentifier);
            }
            $query->delete();
            $review->decrementHelpfulCount();

            return response()->json([
                'success' => true,
                'message' => 'Vote retiré',
                'helpful_count' => $review->fresh()->helpful_count,
                'is_helpful' => false,
            ]);
        }

        // Ajouter le vote
        ReviewHelpfulVote::create([
            'review_id' => $review->id,
            'app_user_id' => $user?->id,
            'guest_identifier' => $user ? null : $guestIdentifier,
        ]);
        $review->incrementHelpfulCount();

        return response()->json([
            'success' => true,
            'message' => 'Merci pour votre vote',
            'helpful_count' => $review->fresh()->helpful_count,
            'is_helpful' => true,
        ]);
    }

    /**
     * Get user's reviews
     */
    public function myReviews(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $reviews = $user->reviews()
            ->with(['poi.translations', 'poi.featuredImage'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews->map(function ($review) use ($user) {
                $formatted = $this->formatReview($review, $user->id);
                $formatted['poi'] = [
                    'id' => $review->poi->id,
                    'name' => $review->poi->name,
                    'slug' => $review->poi->slug,
                    'featured_image' => $review->poi->featuredImage ? asset($review->poi->featuredImage->path) : null,
                ];

                return $formatted;
            }),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    /**
     * Format review for API response
     */
    private function formatReview(Review $review, $userId = null, $guestIdentifier = null): array
    {
        return [
            'id' => $review->id,
            'author' => [
                'name' => $review->author_name,
                'is_verified' => $review->is_verified,
                'is_me' => $userId && $review->app_user_id === $userId,
            ],
            'rating' => $review->rating,
            'title' => $review->title,
            'comment' => $review->comment,
            'helpful_count' => $review->helpful_count,
            'is_helpful' => $review->isHelpfulBy($userId, $guestIdentifier),
            'created_at' => $review->created_at->toIso8601String(),
            'updated_at' => $review->updated_at->toIso8601String(),
            'operator_response' => $review->operator_response ? [
                'text' => $review->operator_response,
                'date' => $review->operator_response_at?->toIso8601String(),
            ] : null,
        ];
    }
}
