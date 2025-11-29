<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Mail\TourSubmittedForApproval;
use App\Models\AdminUser;
use App\Models\Tour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourController extends Controller
{
    /**
     * Display a listing of the operator's tours.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('operator')->user();

        $query = $user->managedTours()
            ->with(['translations', 'featuredImage', 'tourOperator.translations']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'title') {
            $locale = session('locale', 'fr');
            $query->leftJoin('tour_translations', function ($join) use ($locale) {
                $join->on('tours.id', '=', 'tour_translations.tour_id')
                    ->where('tour_translations.locale', '=', $locale);
            })->orderBy('tour_translations.title', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $tours = $query->paginate(15)->withQueryString();

        // Statistics
        $statistics = [
            'total' => $user->managedTours()->count(),
            'draft' => $user->managedTours()->where('status', 'draft')->count(),
            'pending_approval' => $user->managedTours()->where('status', 'pending_approval')->count(),
            'approved' => $user->managedTours()->where('status', 'approved')->count(),
            'rejected' => $user->managedTours()->where('status', 'rejected')->count(),
            'active' => $user->managedTours()->where('status', 'active')->count(),
            'inactive' => $user->managedTours()->where('status', 'inactive')->count(),
        ];

        return view('operator.tours.index', compact('tours', 'statistics', 'user'));
    }

    /**
     * Display the specified tour.
     */
    public function show(Tour $tour): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        $tour->load([
            'translations',
            'featuredImage',
            'media',
            'tourOperator.translations',
            'target.translations',
            'reservations',
        ]);

        // Get reservation statistics
        $reservationStats = [
            'total' => $tour->reservations()->count(),
            'confirmed' => $tour->confirmedReservations()->count(),
            'pending' => $tour->reservations()->where('status', 'pending')->count(),
        ];

        return view('operator.tours.show', compact('tour', 'reservationStats', 'user'));
    }

    /**
     * Show the form for creating a new tour.
     */
    public function create(): View
    {
        $user = Auth::guard('operator')->user();
        return view('operator.tours.create', compact('user'));
    }


    /**
     * Store a newly created tour in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        $validated = $request->validate([
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ]);

        // Tours created by operators always start as draft
        $tour = $user->tourOperator->tours()->create([
            'slug' => Str::slug($validated['translations']['fr']['title'] ?? 'tour-' . uniqid()),
            'status' => 'draft',
            'created_by_operator_user_id' => $user->id,
            'price' => $validated['price'],
            'max_participants' => $validated['max_participants'],
            'current_participants' => 0,
        ]);

        foreach ($validated['translations'] as $locale => $translationData) {
            $tour->translations()->create(
                array_merge($translationData, ['locale' => $locale])
            );
        }

        return redirect()
            ->route('operator.tours.show', $tour)
            ->with('success', 'Tour créé avec succès. Soumettez-le pour approbation quand il sera prêt.');
    }


    /**
     * Update the specified tour.
     */
    public function update(Request $request, Tour $tour): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        // Operators can only edit draft or rejected tours
        if (!in_array($tour->status, ['draft', 'rejected'])) {
            return redirect()
                ->back()
                ->with('error', 'Vous ne pouvez modifier que les tours en brouillon ou rejetés.');
        }

        $validated = $request->validate([
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'min_participants' => 'nullable|integer|min:1',
            'duration_hours' => 'nullable|integer|min:1',
            'difficulty_level' => 'nullable|in:easy,moderate,difficult,expert',
            'includes' => 'nullable|array',
            'requirements' => 'nullable|array',
            'meeting_point_address' => 'nullable|string|max:500',
            'meeting_point_latitude' => 'nullable|numeric|between:-90,90',
            'meeting_point_longitude' => 'nullable|numeric|between:-180,180',
            'weather_dependent' => 'boolean',
            'age_restriction_min' => 'nullable|integer|min:0',
            'age_restriction_max' => 'nullable|integer|min:0',
            'cancellation_policy' => 'nullable|string',
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.short_description' => 'nullable|string|max:500',
            'translations.*.itinerary' => 'nullable|string',
            'translations.*.meeting_point_description' => 'nullable|string',
        ]);

        // Update main tour data (status is NOT updated here, use submitForApproval instead)
        $tour->update([
            'price' => $validated['price'],
            'max_participants' => $validated['max_participants'],
            'min_participants' => $validated['min_participants'],
            'duration_hours' => $validated['duration_hours'],
            'difficulty_level' => $validated['difficulty_level'],
            'includes' => $validated['includes'],
            'requirements' => $validated['requirements'],
            'meeting_point_address' => $validated['meeting_point_address'],
            'meeting_point_latitude' => $validated['meeting_point_latitude'],
            'meeting_point_longitude' => $validated['meeting_point_longitude'],
            'weather_dependent' => $validated['weather_dependent'],
            'age_restriction_min' => $validated['age_restriction_min'],
            'age_restriction_max' => $validated['age_restriction_max'],
            'cancellation_policy' => $validated['cancellation_policy'],
        ]);

        // Update translations
        foreach ($validated['translations'] as $locale => $translationData) {
            $tour->translations()->updateOrCreate(
                ['locale' => $locale],
                $translationData
            );
        }

        return redirect()
            ->route('operator.tours.show', $tour)
            ->with('success', 'Tour mis à jour avec succès.');
    }

    /**
     * Show the form for editing the specified tour.
     */
    public function edit(Tour $tour): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        $tour->load(['translations', 'featuredImage']);

        return view('operator.tours.edit', compact('tour', 'user'));
    }




    /**
     * Get reports for tours.
     */
    public function reports(Request $request): View
    {
        $user = Auth::guard('operator')->user();

        $dateFrom = $request->get('date_from', now()->subMonths(6)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        // Tours statistics
        $toursQuery = $user->managedTours();

        $tourStats = [
            'total_tours' => $toursQuery->count(),
            'draft_tours' => $toursQuery->where('status', 'draft')->count(),
            'pending_approval_tours' => $toursQuery->where('status', 'pending_approval')->count(),
            'approved_tours' => $toursQuery->where('status', 'approved')->count(),
            'rejected_tours' => $toursQuery->where('status', 'rejected')->count(),
            'active_tours' => $toursQuery->where('status', 'active')->count(),
            'inactive_tours' => $toursQuery->where('status', 'inactive')->count(),
        ];

        // Reservations statistics
        $reservationsQuery = \App\Models\Reservation::where('reservable_type', Tour::class)
            ->whereHas('reservable', function ($q) use ($user) {
                $q->where('tour_operator_id', $user->tour_operator_id);
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $reservationStats = [
            'total_reservations' => $reservationsQuery->count(),
            'confirmed_reservations' => $reservationsQuery->confirmed()->count(),
            'pending_reservations' => $reservationsQuery->pending()->count(),
            'cancelled_reservations' => $reservationsQuery->where('status', 'cancelled')->count(),
            'total_revenue' => $reservationsQuery->confirmed()->sum('payment_amount'),
            'total_participants' => $reservationsQuery->confirmed()->sum('number_of_people'),
        ];

        // Popular tours
        $popularTours = $user->managedTours()
            ->withCount(['reservations as total_reservations'])
            ->with(['translations'])
            ->having('total_reservations', '>', 0)
            ->orderBy('total_reservations', 'desc')
            ->limit(10)
            ->get();

        return view('operator.tours.reports', compact(
            'tourStats',
            'reservationStats',
            'popularTours',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Submit tour for admin approval.
     */
    public function submitForApproval(Tour $tour): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        // Only draft or rejected tours can be submitted
        if (!in_array($tour->status, ['draft', 'rejected'])) {
            return redirect()
                ->back()
                ->with('error', 'Seuls les tours en brouillon ou rejetés peuvent être soumis pour approbation.');
        }

        // Submit the tour
        if ($tour->submitForApproval()) {
            // Send notification email to all admins
            $admins = AdminUser::where('is_active', true)->get();

            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->send(new TourSubmittedForApproval($tour));
                } catch (\Exception $e) {
                    \Log::error('Failed to send tour submission email to admin: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('operator.tours.show', $tour)
                ->with('success', 'Tour soumis pour approbation avec succès. Vous recevrez un email une fois qu\'il sera examiné.');
        }

        return redirect()
            ->back()
            ->with('error', 'Erreur lors de la soumission du tour.');
    }

    /**
     * Display all comments for a tour.
     */
    public function comments(Tour $tour): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        // Load tour with translations
        $tour->load(['translations']);

        // Get paginated comments
        $comments = $tour->approvedRootComments()
            ->with([
                'appUser',
                'replies' => function($query) {
                    $query->where('is_approved', true)->with('appUser')->orderBy('created_at', 'asc');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('operator.tours.comments', compact('tour', 'comments', 'user'));
    }
}
