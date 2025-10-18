<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourReservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TourReservationController extends Controller
{
    /**
     * Display a listing of the operator's tour reservations.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('operator')->user();

        // Get all tours for this operator
        $tourIds = Tour::where('tour_operator_id', $user->tour_operator_id)->pluck('id');

        $query = TourReservation::whereIn('tour_id', $tourIds)
            ->with(['tour.translations', 'appUser']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'LIKE', "%{$search}%")
                    ->orWhere('guest_email', 'LIKE', "%{$search}%")
                    ->orWhere('guest_phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('appUser', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to.' 23:59:59');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $reservations = $query->paginate(20)->withQueryString();

        // Get tours for filter
        $tours = Tour::where('tour_operator_id', $user->tour_operator_id)
            ->with('translations')
            ->get();

        // Statistics
        $statistics = [
            'total' => TourReservation::whereIn('tour_id', $tourIds)->count(),
            'confirmed' => TourReservation::whereIn('tour_id', $tourIds)->where('status', 'confirmed')->count(),
            'pending' => TourReservation::whereIn('tour_id', $tourIds)->where('status', 'pending')->count(),
            'cancelled' => TourReservation::whereIn('tour_id', $tourIds)->whereIn('status', ['cancelled_by_user', 'cancelled_by_operator'])->count(),
            'total_participants' => TourReservation::whereIn('tour_id', $tourIds)->where('status', 'confirmed')->sum('number_of_people'),
        ];

        return view('operator.tour-reservations.index', compact('reservations', 'statistics', 'tours'));
    }

    /**
     * Display the specified tour reservation.
     */
    public function show(TourReservation $reservation): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the reservation belongs to this operator
        $this->verifyReservationAccess($reservation, $user);

        $reservation->load([
            'tour.translations',
            'tour.tourOperator.translations',
            'appUser',
        ]);

        return view('operator.tour-reservations.show', compact('reservation'));
    }

    /**
     * Confirm a tour reservation.
     */
    public function confirm(Request $request, TourReservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the reservation belongs to this operator
        $this->verifyReservationAccess($reservation, $user);

        if ($reservation->status !== 'pending') {
            return redirect()
                ->route('operator.tour-reservations.show', $reservation)
                ->with('error', 'Seules les réservations en attente peuvent être confirmées.');
        }

        $reservation->update(['status' => 'confirmed']);

        return redirect()
            ->route('operator.tour-reservations.show', $reservation)
            ->with('success', 'Réservation confirmée avec succès.');
    }

    /**
     * Cancel a tour reservation.
     */
    public function cancel(Request $request, TourReservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the reservation belongs to this operator
        $this->verifyReservationAccess($reservation, $user);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return redirect()
                ->route('operator.tour-reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        // Update current_participants count
        DB::transaction(function () use ($reservation, $request) {
            $reservation->update([
                'status' => 'cancelled_by_operator',
                'notes' => ($reservation->notes ? $reservation->notes . "\n\n" : '') .
                          'Annulé par l\'opérateur: ' . ($request->reason ?? 'Aucune raison fournie')
            ]);

            // Decrement participants if was confirmed
            if ($reservation->status === 'confirmed') {
                $reservation->tour->decrement('current_participants', $reservation->number_of_people);
            }
        });

        return redirect()
            ->route('operator.tour-reservations.show', $reservation)
            ->with('success', 'Réservation annulée avec succès.');
    }

    /**
     * Update notes for a reservation.
     */
    public function updateNotes(Request $request, TourReservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $this->verifyReservationAccess($reservation, $user);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $reservation->update(['notes' => $request->notes]);

        return redirect()
            ->route('operator.tour-reservations.show', $reservation)
            ->with('success', 'Notes mises à jour avec succès.');
    }

    /**
     * Mark reservation as completed (check-in).
     */
    public function checkIn(Request $request, TourReservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $this->verifyReservationAccess($reservation, $user);

        if ($reservation->status !== 'confirmed') {
            return redirect()
                ->route('operator.tour-reservations.show', $reservation)
                ->with('error', 'Seules les réservations confirmées peuvent être marquées comme terminées.');
        }

        $reservation->update(['status' => 'completed']);

        return redirect()
            ->route('operator.tour-reservations.show', $reservation)
            ->with('success', 'Réservation marquée comme terminée avec succès.');
    }

    /**
     * Bulk confirm reservations.
     */
    public function bulkConfirm(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $ids = $request->input('ids', []);

        $reservations = TourReservation::whereIn('id', $ids)->get();

        foreach ($reservations as $reservation) {
            $this->verifyReservationAccess($reservation, $user);
            if ($reservation->status === 'pending') {
                $reservation->update(['status' => 'confirmed']);
            }
        }

        return redirect()
            ->route('operator.tour-reservations.index')
            ->with('success', count($ids) . ' réservation(s) confirmée(s) avec succès.');
    }

    /**
     * Verify that the reservation belongs to this operator.
     */
    private function verifyReservationAccess(TourReservation $reservation, $user): void
    {
        if ($reservation->tour->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette réservation.');
        }
    }
}
