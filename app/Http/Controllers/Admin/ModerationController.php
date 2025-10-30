<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    /**
     * Display reviews for moderation
     */
    public function reviews(Request $request): View
    {
        $query = Review::with(['poi.translations', 'appUser'])
            ->latest();

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('comment', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%");
            });
        }

        $reviews = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
            'average_rating' => round(Review::where('is_approved', true)->avg('rating'), 1),
        ];

        return view('admin.moderation.reviews', compact('reviews', 'stats'));
    }

    /**
     * Approve a review
     */
    public function approveReview(Review $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Avis approuvé avec succès');
    }

    /**
     * Reject/Disapprove a review
     */
    public function rejectReview(Review $review)
    {
        $review->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Avis rejeté');
    }

    /**
     * Delete a review
     */
    public function deleteReview(Review $review)
    {
        $review->delete();

        return redirect()->back()->with('success', 'Avis supprimé');
    }

    /**
     * Display comments for moderation
     */
    public function comments(Request $request): View
    {
        $query = Comment::with(['commentable', 'appUser'])
            ->whereNull('parent_id') // Seulement les commentaires racines
            ->latest();

        // Filtres
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('type')) {
            $modelClass = $this->getModelClass($request->type);
            if ($modelClass) {
                $query->where('commentable_type', $modelClass);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(20);

        // Statistiques
        $stats = [
            'total' => Comment::whereNull('parent_id')->count(),
            'approved' => Comment::whereNull('parent_id')->where('is_approved', true)->count(),
            'pending' => Comment::whereNull('parent_id')->where('is_approved', false)->count(),
        ];

        return view('admin.moderation.comments', compact('comments', 'stats'));
    }

    /**
     * Approve a comment
     */
    public function approveComment(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Commentaire approuvé avec succès');
    }

    /**
     * Reject/Disapprove a comment
     */
    public function rejectComment(Comment $comment)
    {
        $comment->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Commentaire rejeté');
    }

    /**
     * Delete a comment
     */
    public function deleteComment(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Commentaire supprimé');
    }

    /**
     * Get model class from type string
     */
    private function getModelClass(string $type): ?string
    {
        return match ($type) {
            'poi' => \App\Models\Poi::class,
            'event' => \App\Models\Event::class,
            'tour' => \App\Models\Tour::class,
            'tour_operator' => \App\Models\TourOperator::class,
            'activity' => \App\Models\Activity::class,
            default => null,
        };
    }
}
