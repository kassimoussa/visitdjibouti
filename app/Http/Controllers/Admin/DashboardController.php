<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poi;
use App\Models\Event;
use App\Models\AppUser;
use App\Models\Reservation;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\Category;
use App\Models\Media;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with real statistics.
     */
    public function index(): View
    {
        // POIs Statistics
        $totalPois = Poi::count();
        $publishedPois = Poi::where('status', 'published')->count();
        $featuredPois = Poi::where('is_featured', true)->count();

        // Events Statistics
        $totalEvents = Event::count();
        $publishedEvents = Event::where('status', 'published')->count();
        $upcomingEvents = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->count();
        $endedEvents = Event::where('end_date', '<', now())->count();

        // Reservations Statistics
        $totalReservations = Reservation::count();
        $confirmedReservations = Reservation::where('status', 'confirmed')->count();
        $pendingReservations = Reservation::where('status', 'pending')->count();
        $todayReservations = Reservation::whereDate('created_at', today())->count();

        // Revenue from confirmed reservations
        $totalRevenue = Reservation::where('status', 'confirmed')
            ->sum('payment_amount');

        // Mobile App Users Statistics
        $totalAppUsers = AppUser::count();
        $activeAppUsers = AppUser::whereNotNull('email_verified_at')->count();
        $newUsersThisWeek = AppUser::where('created_at', '>=', now()->subWeek())->count();
        $newUsersToday = AppUser::whereDate('created_at', today())->count();

        // Tours Statistics
        $totalTours = Tour::count();
        $activeTours = Tour::where('status', 'active')->count();
        $featuredTours = Tour::where('is_featured', true)->count();

        // Tour Operators Statistics
        $totalTourOperators = TourOperator::count();
        $activeTourOperators = TourOperator::where('is_active', true)->count();
        $featuredTourOperators = TourOperator::where('featured', true)->count();

        // Categories Statistics
        $totalCategories = Category::count();

        // Media Statistics
        $totalMedia = Media::count();
        $mediaThisMonth = Media::where('created_at', '>=', now()->startOfMonth())->count();

        // Recent Activity - Last 10 activities
        $recentActivities = collect();

        // Recent POIs
        $recentPois = Poi::with('translations')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($poi) {
                return [
                    'type' => 'poi',
                    'icon' => 'map-marker-alt',
                    'color' => 'primary',
                    'title' => 'Nouveau POI',
                    'description' => $poi->name ?: 'Sans nom',
                    'time' => $poi->created_at,
                    'url' => route('pois.show', $poi->id),
                ];
            });

        // Recent Events
        $recentEvents = Event::with('translations')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($event) {
                return [
                    'type' => 'event',
                    'icon' => 'calendar-alt',
                    'color' => 'success',
                    'title' => 'Nouvel événement',
                    'description' => $event->title ?: 'Sans titre',
                    'time' => $event->created_at,
                    'url' => route('events.show', $event->id),
                ];
            });

        // Recent Reservations
        $recentReservations = Reservation::with(['reservable', 'appUser'])
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($reservation) {
                return [
                    'type' => 'reservation',
                    'icon' => 'ticket-alt',
                    'color' => 'warning',
                    'title' => 'Nouvelle réservation',
                    'description' => ($reservation->appUser ? $reservation->appUser->name : $reservation->guest_name) . ' - ' . $reservation->number_of_people . ' personne(s)',
                    'time' => $reservation->created_at,
                    'url' => route('reservations.index'),
                ];
            });

        // Recent Users
        $recentUsers = AppUser::latest()
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'user',
                    'icon' => 'user',
                    'color' => 'info',
                    'title' => 'Nouvel utilisateur',
                    'description' => $user->name,
                    'time' => $user->created_at,
                    'url' => route('app-users.show', $user->id),
                ];
            });

        // Merge and sort activities by time
        $recentActivities = $recentPois
            ->merge($recentEvents)
            ->merge($recentReservations)
            ->merge($recentUsers)
            ->sortByDesc('time')
            ->take(10)
            ->values();

        // Statistics for chart - Reservations by month (last 6 months)
        $reservationsByMonth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Reservation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $reservationsByMonth->push([
                'month' => $date->format('M Y'),
                'count' => $count,
            ]);
        }

        // Top POIs by reservations count
        $topPois = Poi::with('translations')
            ->where('status', 'published')
            ->withCount('reservations')
            ->orderBy('reservations_count', 'desc')
            ->limit(5)
            ->get();

        // Upcoming Events (next 5)
        $upcomingEventsList = Event::with('translations')
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        return view('admin.index', compact(
            'totalPois',
            'publishedPois',
            'featuredPois',
            'totalEvents',
            'publishedEvents',
            'upcomingEvents',
            'endedEvents',
            'totalReservations',
            'confirmedReservations',
            'pendingReservations',
            'todayReservations',
            'totalRevenue',
            'totalAppUsers',
            'activeAppUsers',
            'newUsersThisWeek',
            'newUsersToday',
            'totalTours',
            'activeTours',
            'featuredTours',
            'totalTourOperators',
            'activeTourOperators',
            'featuredTourOperators',
            'totalCategories',
            'totalMedia',
            'mediaThisMonth',
            'recentActivities',
            'reservationsByMonth',
            'topPois',
            'upcomingEventsList'
        ));
    }
}
