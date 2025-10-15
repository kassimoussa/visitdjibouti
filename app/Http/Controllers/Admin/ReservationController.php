<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Poi;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Display the main reservations management page
     */
    public function index()
    {
        return view('admin.reservations.index');
    }

    /**
     * Display the reservations dashboard
     */
    public function dashboard()
    {
        // Statistiques globales
        $globalStats = [
            'total_reservations' => Reservation::count(),
            'total_poi_reservations' => Reservation::forPois()->count(),
            'total_event_reservations' => Reservation::forEvents()->count(),
            'pending_reservations' => Reservation::where('status', 'pending')->count(),
            'confirmed_reservations' => Reservation::where('status', 'confirmed')->count(),
            'today_reservations' => Reservation::whereDate('reservation_date', today())->count(),
            'week_reservations' => Reservation::whereBetween('reservation_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_reservations' => Reservation::whereYear('reservation_date', now()->year)
                ->whereMonth('reservation_date', now()->month)
                ->count(),
            'total_people' => Reservation::where('status', '!=', 'cancelled')->sum('number_of_people'),
        ];

        // Top POIs les plus réservés
        $topPois = DB::table('reservations')
            ->select('reservable_id', DB::raw('COUNT(*) as reservations_count'))
            ->join('pois', 'reservations.reservable_id', '=', 'pois.id')
            ->join('poi_translations', function ($join) {
                $join->on('pois.id', '=', 'poi_translations.poi_id')
                    ->where('poi_translations.locale', '=', 'fr');
            })
            ->where('reservations.reservable_type', Poi::class)
            ->where('reservations.status', '!=', 'cancelled')
            ->groupBy('reservable_id')
            ->orderByDesc('reservations_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $poi = Poi::with('translations')->find($item->reservable_id);

                return [
                    'id' => $poi->id,
                    'name' => $poi->translation('fr')->name ?? 'Sans nom',
                    'slug' => $poi->slug,
                    'reservations_count' => $item->reservations_count,
                ];
            });

        // Top Events les plus réservés
        $topEvents = DB::table('reservations')
            ->select('reservable_id', DB::raw('COUNT(*) as reservations_count'))
            ->join('events', 'reservations.reservable_id', '=', 'events.id')
            ->join('event_translations', function ($join) {
                $join->on('events.id', '=', 'event_translations.event_id')
                    ->where('event_translations.locale', '=', 'fr');
            })
            ->where('reservations.reservable_type', Event::class)
            ->where('reservations.status', '!=', 'cancelled')
            ->groupBy('reservable_id')
            ->orderByDesc('reservations_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $event = Event::with('translations')->find($item->reservable_id);

                return [
                    'id' => $event->id,
                    'name' => $event->translation('fr')->title ?? 'Sans nom',
                    'slug' => $event->slug,
                    'reservations_count' => $item->reservations_count,
                ];
            });

        // Réservations récentes
        $recentReservations = Reservation::with(['reservable.translations', 'appUser'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.reservations.dashboard', compact(
            'globalStats',
            'topPois',
            'topEvents',
            'recentReservations'
        ));
    }

    /**
     * Export reservations to CSV
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Get reservation statistics for AJAX requests
     */
    public function stats(Request $request)
    {
        $period = $request->get('period', 'week'); // day, week, month, year

        $query = Reservation::query();

        switch ($period) {
            case 'day':
                $query->whereDate('reservation_date', today());
                break;
            case 'week':
                $query->whereBetween('reservation_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereYear('reservation_date', now()->year)
                    ->whereMonth('reservation_date', now()->month);
                break;
            case 'year':
                $query->whereYear('reservation_date', now()->year);
                break;
        }

        $stats = [
            'total' => $query->count(),
            'poi_reservations' => $query->clone()->forPois()->count(),
            'event_reservations' => $query->clone()->forEvents()->count(),
            'by_status' => $query->clone()
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];

        return response()->json($stats);
    }
}
