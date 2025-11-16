<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the operator dashboard.
     */
    public function index(): View
    {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator;

        // Get statistics
        $statistics = $user->getStatistics();
        $operatorStatistics = $tourOperator->getStatistics();

        // Get recent tours
        $recentTours = $tourOperator->tours()
            ->with(['translations', 'featuredImage'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming tours
        $upcomingTours = $tourOperator->tours()
            ->with(['translations', 'featuredImage'])
            ->where('status', 'approved')
            ->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->limit(5)
            ->get();

        // Get recent activities
        $recentActivities = $tourOperator->activities()
            ->with(['translations', 'featuredImage'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get active activities
        $activeActivities = $tourOperator->activities()
            ->with(['translations', 'featuredImage'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent reservations
        $recentReservations = $user->managedReservations()
            ->with(['reservable.translations'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get pending reservations count
        $pendingReservationsCount = $user->managedReservations()
            ->pending()
            ->count();

        return view('operator.dashboard.index', compact(
            'user',
            'tourOperator',
            'statistics',
            'operatorStatistics',
            'recentTours',
            'upcomingTours',
            'recentActivities',
            'activeActivities',
            'recentReservations',
            'pendingReservationsCount'
        ));
    }
}
