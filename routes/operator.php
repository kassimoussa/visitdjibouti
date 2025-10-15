<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    Route::get('/login', function () {
        return redirect()->route('login');
    })->name('login');
});

// Dashboard operator - accessible après connexion
Route::middleware('operator.auth')->prefix('operator')->name('operator.')->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [App\Http\Controllers\Operator\DashboardController::class, 'index'])->name('dashboard');

    // Routes pour les événements
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\EventController::class, 'index'])->name('index');
        Route::get('/reports', [\App\Http\Controllers\Operator\EventController::class, 'reports'])->name('reports');
        Route::get('/create', [\App\Http\Controllers\Operator\EventController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Operator\EventController::class, 'store'])->name('store');
        Route::get('/{event}', [\App\Http\Controllers\Operator\EventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [\App\Http\Controllers\Operator\EventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [\App\Http\Controllers\Operator\EventController::class, 'update'])->name('update');

        // Event reservation management
        Route::get('/{event}/reservations', [\App\Http\Controllers\Operator\EventController::class, 'reservations'])->name('reservations');
        Route::get('/{event}/reservations/export', [\App\Http\Controllers\Operator\EventController::class, 'exportReservations'])->name('export-reservations');

        // Event status management
        Route::patch('/{event}/publish', [\App\Http\Controllers\Operator\EventController::class, 'publish'])->name('publish');
        Route::patch('/{event}/unpublish', [\App\Http\Controllers\Operator\EventController::class, 'unpublish'])->name('unpublish');
        Route::patch('/{event}/cancel', [\App\Http\Controllers\Operator\EventController::class, 'cancel'])->name('cancel');
        Route::get('/{event}/duplicate', [\App\Http\Controllers\Operator\EventController::class, 'duplicate'])->name('duplicate');
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
        Route::put('/password', [\App\Http\Controllers\Operator\ProfileController::class, 'updatePassword'])->name('password');
    });

    // Route pour l'entreprise
    Route::get('/tour-operator', function () {
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

    // Déconnexion
    Route::post('/logout', function () {
        Auth::guard('operator')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
