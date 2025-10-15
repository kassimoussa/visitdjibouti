<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of the operator's events.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('operator')->user();
        $categories = \App\Models\Category::with('translations')->get();

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
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'title') {
            $locale = session('locale', 'fr');
            $query->leftJoin('event_translations', function ($join) use ($locale) {
                $join->on('events.id', '=', 'event_translations.event_id')
                    ->where('event_translations.locale', '=', $locale);
            })->orderBy('event_translations.title', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $events = $query->paginate(15)->withQueryString();

        // Statistics
        $statistics = [
            'total' => $user->managedEvents()->count(),
            'published' => $user->managedEvents()->where('status', 'published')->count(),
            'draft' => $user->managedEvents()->where('status', 'draft')->count(),
            'upcoming' => $user->managedEvents()
                ->where('status', 'published')
                ->where('start_date', '>=', now()->toDateString())
                ->count(),
            'past' => $user->managedEvents()
                ->where('status', 'published')
                ->where('end_date', '<', now()->toDateString())
                ->count(),
        ];

        return view('operator.events.index', compact('events', 'statistics', 'user', 'categories'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $event->load([
            'translations',
            'featuredImage',
            'media',
            'categories.translations',
            'tourOperator.translations',
            'creator',
        ]);

        // Get reservations for this event
        $reservations = $event->reservations()
            ->with(['appUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get recent reservations for sidebar
        $recentReservations = $event->reservations()
            ->with(['appUser'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get reservation statistics
        $reservationStats = [
            'total' => $event->reservations()->count(),
            'confirmed' => $event->confirmedReservations()->count(),
            'pending' => $event->pendingReservations()->count(),
            'cancelled' => $event->reservations()->where('status', 'cancelled')->count(),
            'revenue' => $event->confirmedReservations()->sum('payment_amount'),
        ];

        return view('operator.events.show', compact('event', 'reservations', 'recentReservations', 'reservationStats', 'user'));
    }

    /**
     * Show reservations for a specific event.
     */
    public function reservations(Event $event): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $reservations = $event->reservations()
            ->with(['appUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('operator.events.reservations', compact('event', 'reservations', 'user'));
    }

    /**
     * Export reservations for a specific event.
     */
    public function exportReservations(Event $event)
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $reservations = $event->reservations()
            ->with(['appUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'reservations-'.$event->slug.'-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($reservations) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, ['ID', 'Nom', 'Email', 'Téléphone', 'Nombre de personnes', 'Statut', 'Montant', 'Date de réservation']);

            // Data
            foreach ($reservations as $reservation) {
                fputcsv($file, [
                    $reservation->id,
                    $reservation->user_name ?? ($reservation->appUser ? $reservation->appUser->name : 'N/A'),
                    $reservation->user_email ?? ($reservation->appUser ? $reservation->appUser->email : 'N/A'),
                    $reservation->user_phone ?? 'N/A',
                    $reservation->number_of_people,
                    $reservation->status,
                    $reservation->payment_amount ?? 0,
                    $reservation->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Publish an event.
     */
    public function publish(Event $event): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $event->update(['status' => 'published']);

        return redirect()
            ->route('operator.events.show', $event)
            ->with('success', 'Événement publié avec succès.');
    }

    /**
     * Unpublish an event.
     */
    public function unpublish(Event $event): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $event->update(['status' => 'draft']);

        return redirect()
            ->route('operator.events.show', $event)
            ->with('success', 'Événement dépublié avec succès.');
    }

    /**
     * Cancel an event.
     */
    public function cancel(Event $event): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $event->update(['status' => 'cancelled']);

        // Optionally: notify users with reservations
        // TODO: Send cancellation emails to users with confirmed reservations

        return redirect()
            ->route('operator.events.show', $event)
            ->with('success', 'Événement annulé avec succès.');
    }

    /**
     * Duplicate an event.
     */
    public function duplicate(Event $event): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        // Create duplicate
        $newEvent = $event->replicate();
        $newEvent->slug = $event->slug.'-copy-'.time();
        $newEvent->status = 'draft';
        $newEvent->current_participants = 0;
        $newEvent->views_count = 0;
        $newEvent->save();

        // Duplicate translations
        foreach ($event->translations as $translation) {
            $newTranslation = $translation->replicate();
            $newTranslation->event_id = $newEvent->id;
            $newTranslation->save();
        }

        // Duplicate category relationships
        $newEvent->categories()->sync($event->categories->pluck('id'));

        // Duplicate media relationships
        $newEvent->media()->sync($event->media->pluck('id'));

        return redirect()
            ->route('operator.events.edit', $newEvent)
            ->with('success', 'Événement dupliqué avec succès. Vous pouvez maintenant le modifier.');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $event->load(['translations', 'featuredImage', 'categories']);

        return view('operator.events.edit', compact('event', 'user'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the event belongs to this operator
        if ($event->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cet événement.');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,published,cancelled',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'website_url' => 'nullable|url',
            'ticket_url' => 'nullable|url',
            'organizer' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'translations' => 'required|array',
            'translations.*' => 'required|array',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
            'translations.*.short_description' => 'nullable|string|max:500',
            'translations.*.location_details' => 'nullable|string',
            'translations.*.requirements' => 'nullable|string',
            'translations.*.program' => 'nullable|string',
            'translations.*.additional_info' => 'nullable|string',
        ]);

        // Update main event data
        $event->update([
            'status' => $validated['status'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'website_url' => $validated['website_url'],
            'ticket_url' => $validated['ticket_url'],
            'organizer' => $validated['organizer'],
            'max_participants' => $validated['max_participants'],
            'price' => $validated['price'],
        ]);

        // Update translations
        foreach ($validated['translations'] as $locale => $translationData) {
            $event->translations()->updateOrCreate(
                ['locale' => $locale],
                $translationData
            );
        }

        return redirect()
            ->route('operator.events.show', $event)
            ->with('success', 'Événement mis à jour avec succès.');
    }

    /**
     * Get reports for events.
     */
    public function reports(Request $request): View
    {
        $user = Auth::guard('operator')->user();

        $dateFrom = $request->get('date_from', now()->subMonths(6)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        // Events statistics
        $eventsQuery = $user->managedEvents()
            ->whereBetween('start_date', [$dateFrom, $dateTo]);

        $eventStats = [
            'total_events' => $eventsQuery->count(),
            'published_events' => $eventsQuery->where('status', 'published')->count(),
            'draft_events' => $eventsQuery->where('status', 'draft')->count(),
            'cancelled_events' => $eventsQuery->where('status', 'cancelled')->count(),
            'upcoming_events' => $eventsQuery->where('start_date', '>=', now()->toDateString())->count(),
            'past_events' => $eventsQuery->where('end_date', '<', now()->toDateString())->count(),
        ];

        // Reservations statistics
        $reservationsQuery = \App\Models\Reservation::where('reservable_type', Event::class)
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

        // Popular events
        $popularEvents = $user->managedEvents()
            ->withCount(['reservations as total_reservations'])
            ->having('total_reservations', '>', 0)
            ->orderBy('total_reservations', 'desc')
            ->limit(10)
            ->get();

        // Monthly data for charts
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');

            $monthlyData[$monthKey] = [
                'month' => $month->format('M Y'),
                'events' => $user->managedEvents()
                    ->whereYear('start_date', $month->year)
                    ->whereMonth('start_date', $month->month)
                    ->count(),
                'reservations' => \App\Models\Reservation::where('reservable_type', Event::class)
                    ->whereHas('reservable', function ($q) use ($user) {
                        $q->where('tour_operator_id', $user->tour_operator_id);
                    })
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
                'revenue' => \App\Models\Reservation::where('reservable_type', Event::class)
                    ->whereHas('reservable', function ($q) use ($user) {
                        $q->where('tour_operator_id', $user->tour_operator_id);
                    })
                    ->confirmed()
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('payment_amount'),
            ];
        }

        return view('operator.events.reports', compact(
            'eventStats',
            'reservationStats',
            'popularEvents',
            'monthlyData',
            'dateFrom',
            'dateTo'
        ));
    }
}
