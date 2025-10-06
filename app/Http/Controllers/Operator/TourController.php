<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
            'with_schedules' => $user->managedTours()
                ->whereHas('activeSchedules')
                ->count(),
        ];

        return view('operator.tours.index', compact('tours', 'statistics'));
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
            'target.translations'
        ]);

        return view('operator.tours.show', compact('tour'));
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
     * Display tour schedules.
     */
    public function schedules(Request $request, Tour $tour): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        $query = $tour->schedules()->with(['tour.translations']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $schedules = $query->paginate(20)->withQueryString();

        return view('operator.tours.schedules', compact('tour', 'schedules'));
    }

    /**
     * Create a new tour schedule.
     */
    public function createSchedule(Request $request, Tour $tour): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à ce tour.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'available_spots' => 'required|integer|min:1',
            'guide_name' => 'nullable|string|max:255',
            'guide_contact' => 'nullable|string|max:255',
            'guide_languages' => 'nullable|array',
            'guide_languages.*' => 'string|in:fr,en,ar,es,it,de',
            'special_notes' => 'nullable|string',
            'price_override' => 'nullable|numeric|min:0',
            'meeting_point_override' => 'nullable|string|max:500',
            'cancellation_deadline' => 'nullable|date|before:start_date',
        ]);

        $validated['tour_id'] = $tour->id;
        $validated['booked_spots'] = 0;
        $validated['status'] = 'available';
        $validated['created_by_admin_id'] = null; // Created by operator

        TourSchedule::create($validated);

        return redirect()
            ->route('operator.tours.schedules.index', $tour)
            ->with('success', 'Calendrier créé avec succès.');
    }

    /**
     * Update a tour schedule.
     */
    public function updateSchedule(Request $request, Tour $tour, TourSchedule $schedule): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id || $schedule->tour_id !== $tour->id) {
            abort(403, 'Vous n\'avez pas accès à ce calendrier.');
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'available_spots' => 'required|integer|min:' . $schedule->booked_spots,
            'status' => 'required|in:available,full,cancelled,completed',
            'guide_name' => 'nullable|string|max:255',
            'guide_contact' => 'nullable|string|max:255',
            'guide_languages' => 'nullable|array',
            'guide_languages.*' => 'string|in:fr,en,ar,es,it,de',
            'special_notes' => 'nullable|string',
            'price_override' => 'nullable|numeric|min:0',
            'meeting_point_override' => 'nullable|string|max:500',
            'cancellation_deadline' => 'nullable|date|before:start_date',
        ]);

        $schedule->update($validated);

        return redirect()
            ->route('operator.tours.schedules.index', $tour)
            ->with('success', 'Calendrier mis à jour avec succès.');
    }

    /**
     * Delete a tour schedule.
     */
    public function deleteSchedule(Tour $tour, TourSchedule $schedule): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the tour belongs to this operator
        if ($tour->tour_operator_id !== $user->tour_operator_id || $schedule->tour_id !== $tour->id) {
            abort(403, 'Vous n\'avez pas accès à ce calendrier.');
        }

        // Check if there are confirmed reservations
        if ($schedule->confirmedReservations()->exists()) {
            return redirect()
                ->route('operator.tours.schedules.index', $tour)
                ->with('error', 'Impossible de supprimer un calendrier avec des réservations confirmées.');
        }

        // Cancel pending reservations
        $schedule->pendingReservations()->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Calendrier supprimé par l\'opérateur',
            'cancelled_at' => now()
        ]);

        $schedule->delete();

        return redirect()
            ->route('operator.tours.schedules.index', $tour)
            ->with('success', 'Calendrier supprimé avec succès.');
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