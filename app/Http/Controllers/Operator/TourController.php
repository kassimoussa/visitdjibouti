<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ]);

        // Get upcoming schedules
        $upcomingSchedules = collect(); // Empty collection

        // Get reservation statistics
        $reservationStats = [
            'total' => $tour->reservations()->count(),
            'confirmed' => $tour->confirmedReservations()->count(),
            'pending' => $tour->reservations()->where('status', 'pending')->count(),
        ];

        return view('operator.tours.show', compact('tour', 'upcomingSchedules', 'reservationStats', 'user'));
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
            'status' => 'required|in:active,inactive,draft',
            'type' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ]);

        $tour = $user->tourOperator->tours()->create([
            'slug' => Str::slug($validated['translations']['fr']['title'] ?? 'tour-' . uniqid()),
            'status' => $validated['status'],
            'type' => $validated['type'],
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
            ->with('success', 'Tour créé avec succès.');
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

        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
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

        // Update main tour data
        $tour->update([
            'status' => $validated['status'],
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
            'active_tours' => $toursQuery->where('status', 'active')->count(),
            'inactive_tours' => $toursQuery->where('status', 'inactive')->count(),
            'tours_with_schedules' => $toursQuery->whereHas('schedules')->count(),
        ];

        // Schedules statistics
        $schedulesQuery = TourSchedule::whereHas('tour', function ($q) use ($user) {
            $q->where('tour_operator_id', $user->tour_operator_id);
        })->whereBetween('start_date', [$dateFrom, $dateTo]);

        $scheduleStats = [
            'total_schedules' => $schedulesQuery->count(),
            'available_schedules' => $schedulesQuery->where('status', 'available')->count(),
            'completed_schedules' => $schedulesQuery->where('status', 'completed')->count(),
            'cancelled_schedules' => $schedulesQuery->where('status', 'cancelled')->count(),
        ];

        // Reservations statistics
        $reservationsQuery = \App\Models\Reservation::where('reservable_type', TourSchedule::class)
            ->whereHas('reservable.tour', function ($q) use ($user) {
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
            'scheduleStats',
            'reservationStats',
            'popularTours',
            'dateFrom',
            'dateTo'
        ));
    }
}
