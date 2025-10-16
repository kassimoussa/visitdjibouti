<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\TourSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReservationController extends Controller
{
    /**
     * Display a listing of the operator's reservations.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('operator')->user();

        $query = $user->managedReservations()
            ->with(['reservable.translations', 'appUser']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            if ($request->type === 'events') {
                $query->where('reservable_type', Event::class);
            } elseif ($request->type === 'tours') {
                $query->where('reservable_type', TourSchedule::class);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('confirmation_number', 'LIKE', "%{$search}%")
                    ->orWhere('guest_name', 'LIKE', "%{$search}%")
                    ->orWhere('guest_email', 'LIKE', "%{$search}%")
                    ->orWhere('guest_phone', 'LIKE', "%{$search}%")
                    ->orWhereHas('appUser', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $reservations = $query->paginate(20)->withQueryString();

        // Statistics
        $statistics = [
            'total' => $user->managedReservations()->count(),
            'confirmed' => $user->managedReservations()->confirmed()->count(),
            'pending' => $user->managedReservations()->pending()->count(),
            'cancelled' => $user->managedReservations()->where('status', 'cancelled')->count(),
            'total_revenue' => $user->managedReservations()->confirmed()->sum('payment_amount'),
            'pending_revenue' => $user->managedReservations()->pending()->sum('payment_amount'),
        ];

        return view('operator.reservations.index', compact('reservations', 'statistics'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation): View
    {
        $user = Auth::guard('operator')->user();

        // Verify the reservation belongs to this operator
        $this->verifyReservationAccess($reservation, $user);

        $reservation->load([
            'reservable.translations',
            'appUser',
        ]);

        return view('operator.reservations.show', compact('reservation'));
    }

    /**
     * Confirm a reservation.
     */
    public function confirm(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the reservation belongs to this operator
        $this->verifyReservationAccess($reservation, $user);

        if ($reservation->status !== 'pending') {
            return redirect()
                ->route('operator.reservations.show', $reservation)
                ->with('error', 'Seules les réservations en attente peuvent être confirmées.');
        }

        $reservation->confirm();

        // Update participant count for events
        if ($reservation->reservable_type === Event::class) {
            $reservation->reservable->addParticipant();
        }

        return redirect()
            ->route('operator.reservations.show', $reservation)
            ->with('success', 'Réservation confirmée avec succès.');
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();

        // Verify the reservation belongs to this operator
        $this->verifyReservationAccess($reservation, $user);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (! $reservation->canBeCancelled()) {
            return redirect()
                ->route('operator.reservations.show', $reservation)
                ->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        $reservation->cancel($request->reason);

        // Update participant count for events
        if ($reservation->reservable_type === Event::class && $reservation->status === 'confirmed') {
            $reservation->reservable->removeParticipant();
        }

        return redirect()
            ->route('operator.reservations.show', $reservation)
            ->with('success', 'Réservation annulée avec succès.');
    }

    /**
     * Export reservations to CSV.
     */
    public function exportCsv(Request $request): Response
    {
        $user = Auth::guard('operator')->user();

        $query = $user->managedReservations()
            ->with(['reservable.translations', 'appUser']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            if ($request->type === 'events') {
                $query->where('reservable_type', Event::class);
            } elseif ($request->type === 'tours') {
                $query->where('reservable_type', TourSchedule::class);
            }
        }

        if ($request->filled('date_from')) {
            $query->where('reservation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('reservation_date', '<=', $request->date_to);
        }

        $reservations = $query->orderBy('created_at', 'desc')->get();

        $csvData = $this->generateCsvData($reservations);

        $filename = 'reservations_'.now()->format('Y-m-d_H-i-s').'.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Get reports for reservations.
     */
    public function reports(Request $request): View
    {
        $user = Auth::guard('operator')->user();

        $dateFrom = $request->get('date_from', now()->subMonths(6)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        // Reservations statistics
        $reservationsQuery = $user->managedReservations()
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $reservationStats = [
            'total_reservations' => $reservationsQuery->count(),
            'confirmed_reservations' => $reservationsQuery->confirmed()->count(),
            'pending_reservations' => $reservationsQuery->pending()->count(),
            'cancelled_reservations' => $reservationsQuery->where('status', 'cancelled')->count(),
            'total_revenue' => $reservationsQuery->confirmed()->sum('payment_amount'),
            'pending_revenue' => $reservationsQuery->pending()->sum('payment_amount'),
            'total_participants' => $reservationsQuery->confirmed()->sum('number_of_people'),
            'average_booking_value' => $reservationsQuery->confirmed()->avg('payment_amount'),
        ];

        // Revenue by type
        $eventRevenue = $user->managedReservations()
            ->where('reservable_type', Event::class)
            ->confirmed()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('payment_amount');

        $tourRevenue = $user->managedReservations()
            ->where('reservable_type', TourSchedule::class)
            ->confirmed()
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('payment_amount');

        // Monthly data for charts
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');

            $monthlyReservations = $user->managedReservations()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month);

            $monthlyData[$monthKey] = [
                'month' => $month->format('M Y'),
                'reservations' => $monthlyReservations->count(),
                'confirmed' => $monthlyReservations->confirmed()->count(),
                'revenue' => $monthlyReservations->confirmed()->sum('payment_amount'),
                'participants' => $monthlyReservations->confirmed()->sum('number_of_people'),
            ];
        }

        // Top events by reservations
        $topEvents = $user->managedEvents()
            ->withCount(['reservations as total_reservations'])
            ->with(['translations'])
            ->having('total_reservations', '>', 0)
            ->orderBy('total_reservations', 'desc')
            ->limit(10)
            ->get();

        return view('operator.reservations.reports', compact(
            'reservationStats',
            'eventRevenue',
            'tourRevenue',
            'monthlyData',
            'topEvents',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Verify that the reservation belongs to this operator.
     */
    private function verifyReservationAccess(Reservation $reservation, $user): void
    {
        $hasAccess = false;

        if ($reservation->reservable_type === Event::class) {
            $hasAccess = $reservation->reservable->tour_operator_id === $user->tour_operator_id;
        } elseif ($reservation->reservable_type === TourSchedule::class) {
            $hasAccess = $reservation->reservable->tour->tour_operator_id === $user->tour_operator_id;
        }

        if (! $hasAccess) {
            abort(403, 'Vous n\'avez pas accès à cette réservation.');
        }
    }

    /**
     * Generate CSV data for reservations.
     */
    private function generateCsvData($reservations): string
    {
        $locale = session('locale', 'fr');
        $csvData = '';

        // Headers
        $headers = [
            'Numéro de confirmation',
            'Type',
            'Événement/Tour',
            'Nom du client',
            'Email du client',
            'Téléphone du client',
            'Nombre de personnes',
            'Statut',
            'Montant',
            'Date de réservation',
            'Date de création',
            'Exigences spéciales',
        ];

        $csvData .= '"'.implode('","', $headers).'"'."\n";

        // Data
        foreach ($reservations as $reservation) {
            $reservableTitle = '';
            $reservableType = '';

            if ($reservation->reservable) {
                $translation = $reservation->reservable->translation($locale);
                $reservableTitle = $translation ? $translation->title : 'N/A';
                $reservableType = $reservation->reservable_type === Event::class ? 'Événement' : 'Tour';
            }

            $row = [
                $reservation->confirmation_number,
                $reservableType,
                $reservableTitle,
                $reservation->user_name,
                $reservation->user_email,
                $reservation->user_phone,
                $reservation->number_of_people,
                $this->getStatusLabel($reservation->status),
                $reservation->payment_amount.' DJF',
                $reservation->reservation_date?->format('d/m/Y') ?? 'N/A',
                $reservation->created_at->format('d/m/Y H:i'),
                $reservation->special_requirements ?? '',
            ];

            $csvData .= '"'.implode('","', $row).'"'."\n";
        }

        return $csvData;
    }

    /**
     * Get status label in French.
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
            default => ucfirst($status)
        };
    }

    public function bulkConfirm(Request $request): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $ids = $request->input('ids', []);
        $reservations = Reservation::whereIn('id', $ids)->get();

        foreach ($reservations as $reservation) {
            $this->verifyReservationAccess($reservation, $user);
            if ($reservation->status === 'pending') {
                $reservation->confirm();
            }
        }

        return redirect()
            ->route('operator.reservations.index')
            ->with('success', 'Réservations sélectionnées confirmées avec succès.');
    }

    public function updateNotes(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $this->verifyReservationAccess($reservation, $user);

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $reservation->update(['notes' => $request->notes]);

        return redirect()
            ->route('operator.reservations.show', $reservation)
            ->with('success', 'Notes mises à jour avec succès.');
    }

    public function checkIn(Request $request, Reservation $reservation): RedirectResponse
    {
        $user = Auth::guard('operator')->user();
        $this->verifyReservationAccess($reservation, $user);

        if ($reservation->status !== 'confirmed') {
            return redirect()
                ->route('operator.reservations.show', $reservation)
                ->with('error', 'Seuls les participants avec une réservation confirmée peuvent être enregistrés.');
        }

        $reservation->update(['status' => 'completed']);

        return redirect()
            ->route('operator.reservations.show', $reservation)
            ->with('success', 'Participant enregistré avec succès.');
    }
}
