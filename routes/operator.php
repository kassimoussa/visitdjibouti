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
        Route::post('/bulk-confirm', [\App\Http\Controllers\Operator\ReservationController::class, 'bulkConfirm'])->name('bulk-confirm');
        Route::post('/{reservation}/update-notes', [\App\Http\Controllers\Operator\ReservationController::class, 'updateNotes'])->name('update-notes');
        Route::post('/{reservation}/check-in', [\App\Http\Controllers\Operator\ReservationController::class, 'checkIn'])->name('check-in');
    });

    // Routes pour les rapports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/export', [\App\Http\Controllers\Operator\ReportController::class, 'export'])->name('export');
    });

    // Routes pour les tours
    Route::prefix('tours')->name('tours.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\TourController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Operator\TourController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Operator\TourController::class, 'store'])->name('store');
        Route::get('/{tour}', [\App\Http\Controllers\Operator\TourController::class, 'show'])->name('show');
        Route::get('/{tour}/edit', [\App\Http\Controllers\Operator\TourController::class, 'edit'])->name('edit');
        Route::put('/{tour}', [\App\Http\Controllers\Operator\TourController::class, 'update'])->name('update');
    });

    // Routes pour les médias
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\MediaController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Operator\MediaController::class, 'create'])->name('create');
        Route::get('/{id}/edit', [\App\Http\Controllers\Operator\MediaController::class, 'edit'])->name('edit');
    });

    // Routes pour les réservations de tours
    Route::prefix('tour-reservations')->name('tour-reservations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\TourReservationController::class, 'index'])->name('index');
        Route::get('/{reservation}', [\App\Http\Controllers\Operator\TourReservationController::class, 'show'])->name('show');
        Route::patch('/{reservation}/confirm', [\App\Http\Controllers\Operator\TourReservationController::class, 'confirm'])->name('confirm');
        Route::patch('/{reservation}/cancel', [\App\Http\Controllers\Operator\TourReservationController::class, 'cancel'])->name('cancel');
        Route::post('/bulk-confirm', [\App\Http\Controllers\Operator\TourReservationController::class, 'bulkConfirm'])->name('bulk-confirm');
        Route::post('/{reservation}/update-notes', [\App\Http\Controllers\Operator\TourReservationController::class, 'updateNotes'])->name('update-notes');
        Route::post('/{reservation}/check-in', [\App\Http\Controllers\Operator\TourReservationController::class, 'checkIn'])->name('check-in');
    });

    // Routes pour le profil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\Operator\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [\App\Http\Controllers\Operator\ProfileController::class, 'update'])->name('update');
        Route::put('/password', [\App\Http\Controllers\Operator\ProfileController::class, 'updatePassword'])->name('password');
    });

    // Routes pour l'entreprise
    Route::prefix('tour-operator')->name('tour-operator.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Operator\TourOperatorController::class, 'show'])->name('show');
        Route::get('/edit', [\App\Http\Controllers\Operator\TourOperatorController::class, 'edit'])->name('edit');
    });

    // Déconnexion
    Route::post('/logout', function () {
        Auth::guard('operator')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
