<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Event;
use App\Models\Poi;
use App\Models\Tour;
use App\Models\TourOperator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Get comments for a resource
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'commentable_type' => 'required|string|in:poi,event,tour,tour_operator,activity',
            'commentable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Paramètres invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Obtenir le type de model
        $modelClass = $this->getModelClass($request->commentable_type);
        if (! $modelClass) {
            return response()->json([
                'success' => false,
                'message' => 'Type de ressource invalide',
            ], 400);
        }

        $commentable = $modelClass::find($request->commentable_id);
        if (! $commentable) {
            return response()->json([
                'success' => false,
                'message' => 'Ressource non trouvée',
            ], 404);
        }

        // Récupérer les commentaires approuvés avec leurs réponses
        $comments = $commentable->comments()
            ->with(['appUser', 'replies' => function ($query) {
                $query->where('is_approved', true)
                    ->with('appUser')
                    ->orderBy('created_at', 'asc');
            }])
            ->where('is_approved', true)
            ->whereNull('parent_id') // Seulement les commentaires racines
            ->latest()
            ->paginate(20);

        $user = Auth::guard('sanctum')->user();
        $guestIdentifier = $request->header('X-Guest-ID');

        return response()->json([
            'success' => true,
            'data' => $comments->map(function ($comment) use ($user, $guestIdentifier) {
                return $this->formatComment($comment, $user?->id, $guestIdentifier);
            }),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Validation
        $rules = [
            'commentable_type' => 'required|string|in:poi,event,tour,tour_operator,activity',
            'commentable_id' => 'required|integer',
            'comment' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
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

        // Obtenir le type de model
        $modelClass = $this->getModelClass($request->commentable_type);
        if (! $modelClass) {
            return response()->json([
                'success' => false,
                'message' => 'Type de ressource invalide',
            ], 400);
        }

        $commentable = $modelClass::find($request->commentable_id);
        if (! $commentable) {
            return response()->json([
                'success' => false,
                'message' => 'Ressource non trouvée',
            ], 404);
        }

        // Si c'est une réponse, vérifier que le parent existe
        if ($request->parent_id) {
            $parent = Comment::find($request->parent_id);
            if (! $parent || $parent->commentable_id != $request->commentable_id || $parent->commentable_type != $modelClass) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commentaire parent invalide',
                ], 400);
            }
        }

        // Créer le commentaire
        $comment = Comment::create([
            'commentable_type' => $modelClass,
            'commentable_id' => $request->commentable_id,
            'app_user_id' => $user?->id,
            'guest_name' => $user ? null : $request->guest_name,
            'guest_email' => $user ? null : $request->guest_email,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approuvé par défaut
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté avec succès',
            'data' => $this->formatComment($comment, $user?->id),
        ], 201);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Vérifier que l'utilisateur est propriétaire du commentaire
        if (! $user || $comment->app_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|min:3|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire mis à jour',
            'data' => $this->formatComment($comment, $user->id),
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        // Vérifier que l'utilisateur est propriétaire du commentaire
        if (! $user || $comment->app_user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire supprimé',
        ]);
    }

    /**
     * Like/Unlike a comment
     */
    public function toggleLike(Request $request, Comment $comment): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        $guestIdentifier = $request->header('X-Guest-ID');

        if (! $user && ! $guestIdentifier) {
            return response()->json([
                'success' => false,
                'message' => 'Identification requise',
            ], 400);
        }

        // Vérifier si déjà liké
        if ($comment->isLikedBy($user?->id, $guestIdentifier)) {
            // Retirer le like
            $query = CommentLike::where('comment_id', $comment->id);
            if ($user) {
                $query->where('app_user_id', $user->id);
            } else {
                $query->where('guest_identifier', $guestIdentifier);
            }
            $query->delete();
            $comment->decrementLikesCount();

            return response()->json([
                'success' => true,
                'message' => 'Like retiré',
                'likes_count' => $comment->fresh()->likes_count,
                'is_liked' => false,
            ]);
        }

        // Ajouter le like
        CommentLike::create([
            'comment_id' => $comment->id,
            'app_user_id' => $user?->id,
            'guest_identifier' => $user ? null : $guestIdentifier,
        ]);
        $comment->incrementLikesCount();

        return response()->json([
            'success' => true,
            'message' => 'Commentaire liké',
            'likes_count' => $comment->fresh()->likes_count,
            'is_liked' => true,
        ]);
    }

    /**
     * Get user's comments
     */
    public function myComments(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Non authentifié',
            ], 401);
        }

        $comments = $user->comments()
            ->with('commentable')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $comments->map(function ($comment) use ($user) {
                $formatted = $this->formatComment($comment, $user->id);
                $formatted['resource'] = [
                    'type' => $this->getTypeString($comment->commentable_type),
                    'id' => $comment->commentable->id,
                    'name' => $this->getResourceName($comment->commentable),
                ];

                return $formatted;
            }),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Format comment for API response
     */
    private function formatComment(Comment $comment, $userId = null, $guestIdentifier = null): array
    {
        $formatted = [
            'id' => $comment->id,
            'author' => [
                'name' => $comment->author_name,
                'is_me' => $userId && $comment->app_user_id === $userId,
            ],
            'comment' => $comment->comment,
            'likes_count' => $comment->likes_count,
            'is_liked' => $comment->isLikedBy($userId, $guestIdentifier),
            'created_at' => $comment->created_at->toIso8601String(),
            'updated_at' => $comment->updated_at->toIso8601String(),
        ];

        // Ajouter les réponses si elles existent
        if ($comment->relationLoaded('replies') && $comment->replies->count() > 0) {
            $formatted['replies'] = $comment->replies->map(function ($reply) use ($userId, $guestIdentifier) {
                return $this->formatComment($reply, $userId, $guestIdentifier);
            });
        }

        return $formatted;
    }

    /**
     * Get model class from type string
     */
    private function getModelClass(string $type): ?string
    {
        return match ($type) {
            'poi' => Poi::class,
            'event' => Event::class,
            'tour' => Tour::class,
            'tour_operator' => TourOperator::class,
            'activity' => Activity::class,
            default => null,
        };
    }

    /**
     * Get type string from model class
     */
    private function getTypeString(string $modelClass): string
    {
        return match ($modelClass) {
            Poi::class => 'poi',
            Event::class => 'event',
            Tour::class => 'tour',
            TourOperator::class => 'tour_operator',
            Activity::class => 'activity',
            default => 'unknown',
        };
    }

    /**
     * Get resource name from commentable
     */
    private function getResourceName($commentable): string
    {
        if ($commentable instanceof Poi || $commentable instanceof Event || $commentable instanceof Activity) {
            return $commentable->name ?? $commentable->title ?? 'Sans nom';
        }

        if ($commentable instanceof TourOperator) {
            return $commentable->name ?? 'Sans nom';
        }

        if ($commentable instanceof Tour) {
            return $commentable->title ?? 'Sans nom';
        }

        return 'Sans nom';
    }
}
