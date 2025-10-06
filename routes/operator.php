<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Tour Operator Routes
|--------------------------------------------------------------------------
|
| Interface dédiée aux tour operators pour gérer leurs événements,
| réservations et tours via une interface web sécurisée.
|
*/

// Routes d'authentification pour tour operators (ouvertes)
// Redirection vers la page de connexion admin unifiée
Route::prefix('operator')->name('operator.')->group(function () {
    Route::get('/login', function() {
        return redirect()->route('login');
    })->name('login');
});

// Dashboard operator - accessible après connexion
Route::middleware('operator.auth')->prefix('operator')->name('operator.')->group(function () {

    // Dashboard principal
    Route::get('/dashboard', function() {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator;

        // Statistiques pour le dashboard
        $statistics = [
            'total_events' => $user->managedEvents()->count(),
            'active_events' => $user->managedEvents()->where('status', 'published')->count(),
            'total_reservations' => $user->managedReservations()->count(),
            'pending_reservations' => $user->managedReservations()->where('status', 'pending')->count(),
            'revenue_this_month' => $user->managedReservations()
                ->where('status', 'confirmed')
                ->whereMonth('created_at', now()->month)
                ->sum('payment_amount'),
            'total_tours' => $user->managedTours()->count(),
            'active_tours' => $user->managedTours()->where('status', 'published')->count(),
        ];

        // Événements récents
        $recentEvents = $user->managedEvents()
            ->with(['featuredImage', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Réservations récentes
        $recentReservations = $user->managedReservations()
            ->with(['reservable'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Événements à venir cette semaine
        $upcomingEvents = $user->managedEvents()
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addWeek())
            ->where('status', 'published')
            ->get();

        $pendingReservationsCount = $statistics['pending_reservations'];

        return view('operator.dashboard.index', compact(
            'user', 'tourOperator', 'statistics', 'recentEvents',
            'recentReservations', 'upcomingEvents', 'pendingReservationsCount'
        ));
    })->name('dashboard');

    // Routes pour les événements
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\EventController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Operator\EventController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Operator\EventController::class, 'store'])->name('store');
        Route::get('/{event}', [\App\Http\Controllers\Operator\EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [\App\Http\Controllers\Operator\EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [\App\Http\Controllers\Operator\EventController::class, 'update'])->name('update');
    });

    // Routes pour les réservations
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\ReservationController::class, 'index'])->name('index');
        Route::get('/{reservation}', [\App\Http\Controllers\Operator\ReservationController::class, 'show'])->name('show');
        Route::patch('/{reservation}/confirm', [\App\Http\Controllers\Operator\ReservationController::class, 'confirm'])->name('confirm');
        Route::patch('/{reservation}/cancel', [\App\Http\Controllers\Operator\ReservationController::class, 'cancel'])->name('cancel');
    });

    // Routes pour les tours
    Route::prefix('tours')->name('tours.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\TourController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Operator\TourController::class, 'create'])->name('create');
        Route::get('/{tour}', [\App\Http\Controllers\Operator\TourController::class, 'show'])->name('show');
    });

    // Routes pour le profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\Operator\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Operator\ProfileController::class, 'update'])->name('update');
    });

    // Route pour l'entreprise
    Route::get('/tour-operator', function() {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator;

        $statistics = [
            'total_events' => $user->managedEvents()->count(),
            'total_reservations' => $user->managedReservations()->count(),
            'total_tours' => $user->managedTours()->count(),
            'total_revenue' => $user->managedReservations()
                ->where('status', 'confirmed')
                ->sum('payment_amount'),
        ];

        return view('operator.tour-operator.show', compact('user', 'tourOperator', 'statistics'));
    })->name('tour-operator.show');

    // Route pour les rapports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Operator\ReportController::class, 'dashboard'])->name('dashboard');
    });

    // Déconnexion
    Route::post('/logout', function() {
        Auth::guard('operator')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});