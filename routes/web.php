<?php

use App\Http\Controllers\Admin\AppUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\IconGalleryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PoiController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes du panneau d'administration
|--------------------------------------------------------------------------
*/

// Routes accessibles sans authentification
Route::middleware('guest:admin')->group(function () {
    // Login - page d'accueil par défaut
    Route::get('/', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('/auth', [AuthController::class, 'login'])
        ->name('auth.login');

    // Mot de passe oublié
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    // Réinitialisation de mot de passe
    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

// Routes protégées par authentification
Route::middleware('auth.admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');

    // Route pour la galerie d'icônes
    Route::get('/icons/gallery', [IconGalleryController::class, 'index'])->name('icons.gallery');

    // Routes pour les points d'intérêt
    Route::get('/pois', [PoiController::class, 'index'])->name('pois.index');
    Route::get('/pois/create', [PoiController::class, 'create'])->name('pois.create');
    Route::get('/pois/{id}/edit', [PoiController::class, 'edit'])->name('pois.edit');
    Route::get('/pois/{poi}', [PoiController::class, 'show'])->name('pois.show');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    // Routes pour les tours
    Route::get('/tours', [App\Http\Controllers\Admin\TourController::class, 'index'])->name('tours.index');
    Route::get('/tours/create', [App\Http\Controllers\Admin\TourController::class, 'create'])->name('tours.create');
    Route::get('/tours/approvals', function () {
        return view('admin.tours.approvals');
    })->name('tours.approvals');
    Route::get('/tours/{id}/edit', [App\Http\Controllers\Admin\TourController::class, 'edit'])->name('tours.edit');
    Route::get('/tours/{tour}', [App\Http\Controllers\Admin\TourController::class, 'show'])->name('tours.show');

    // Routes pour les réservations (POI + Events)
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/dashboard', [ReservationController::class, 'dashboard'])->name('reservations.dashboard');
    Route::get('/reservations/export', [ReservationController::class, 'export'])->name('reservations.export');
    Route::get('/reservations/stats', [ReservationController::class, 'stats'])->name('reservations.stats');

    // Routes pour les utilisateurs mobiles (AppUsers)
    Route::get('/app-users', [AppUserController::class, 'index'])->name('app-users.index');
    Route::get('/app-users/dashboard', [AppUserController::class, 'dashboard'])->name('app-users.dashboard');
    Route::get('/app-users/{id}', [AppUserController::class, 'show'])->name('app-users.show');
    Route::get('/app-users/{id}/edit', [AppUserController::class, 'edit'])->name('app-users.edit');
    Route::put('/app-users/{id}', [AppUserController::class, 'update'])->name('app-users.update');
    Route::delete('/app-users/{id}', [AppUserController::class, 'destroy'])->name('app-users.destroy');
    Route::post('/app-users/{id}/restore', [AppUserController::class, 'restore'])->name('app-users.restore');
    Route::delete('/app-users/{id}/force', [AppUserController::class, 'forceDelete'])->name('app-users.force-delete');
    Route::patch('/app-users/{id}/toggle-status', [AppUserController::class, 'toggleStatus'])->name('app-users.toggle-status');
    Route::post('/app-users/{id}/reset-password', [AppUserController::class, 'sendPasswordReset'])->name('app-users.reset-password');
    Route::get('/app-users-stats', [AppUserController::class, 'stats'])->name('app-users.stats');
    Route::get('/app-users-export', [AppUserController::class, 'export'])->name('app-users.export');
    Route::post('/app-users/bulk-action', [AppUserController::class, 'bulkAction'])->name('app-users.bulk-action');

    // Catégories avec interface moderne par défaut
    Route::get('/categories', function () {
        return view('admin.categories.modern');
    })->name('categories.index');

    // Ancienne interface (backup)
    Route::get('/categories-classic', [CategoryController::class, 'index'])->name('categories.classic');

    // Liens externes
    Route::get('/external-links', function () {
        return view('admin.external-links.index');
    })->name('external-links.index');

    // Routes pour les médias
    // Routes pour la gestion des médias
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::get('/media/create', [MediaController::class, 'create'])->name('media.create');
    Route::get('/media/{id}/edit', [MediaController::class, 'edit'])->name('media.edit');
    Route::get('/media/simple-upload', [MediaController::class, 'simpleupload'])->name('media.simple-upload');

    // Routes pour les avis et commentaires
    Route::get('/reviews', function () {
        return view(view: 'admin.reviews.index');
    })->name('reviews.index');

    Route::get('/reviews/{id}', function ($id) {
        return view(view: 'admin.reviews.show', data: ['id' => $id]);
    })->name('reviews.show');

    // Routes pour les utilisateurs
    Route::get('/users', function () {
        return view(view: 'admin.users.index');
    })->name('users.index');

    Route::get('/users/create', function () {
        return view(view: 'admin.users.create');
    })->name('users.create');

    Route::get('/users/{id}/edit', function ($id) {
        return view(view: 'admin.users.edit', data: ['id' => $id]);
    })->name('users.edit');

    // Routes pour les statistiques
    Route::get('/stats', function () {
        return view(view: 'admin.stats.index');
    })->name('stats.index');

    // Routes pour les paramètres
    Route::get('/settings', function () {
        return view('admin.settings.index');
    })->name('settings.index');

    // Routes pour les opérateurs de tour avec structure Resource-like
    Route::prefix('tour-operators')->name('tour-operators.')->group(function () {
        Route::get('/', function () {
            return view('admin.tour-operators.index');
        })->name('index');

        Route::get('/create', function () {
            return view('admin.tour-operators.create');
        })->name('create');

        Route::get('/{id}/edit', function ($id) {
            return view('admin.tour-operators.edit', ['tourOperatorId' => $id]);
        })->where('id', '[0-9]+')->name('edit');

        Route::get('/{id}', function ($id) {
            $tourOperator = \App\Models\TourOperator::findOrFail($id);

            return view('admin.tour-operators.show', compact('tourOperator'));
        })->where('id', '[0-9]+')->name('show');
    });

    // Routes pour les activités
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ActivityController::class, 'index'])->name('index');
        Route::get('/{activity}', [\App\Http\Controllers\Admin\ActivityController::class, 'show'])->name('show');
        Route::post('/{activity}/toggle-status', [\App\Http\Controllers\Admin\ActivityController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{activity}/toggle-featured', [\App\Http\Controllers\Admin\ActivityController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{activity}', [\App\Http\Controllers\Admin\ActivityController::class, 'destroy'])->name('destroy');
    });

    // Routes pour les inscriptions aux activités
    Route::prefix('activity-registrations')->name('activity-registrations.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ActivityController::class, 'registrations'])->name('index');
        Route::get('/{registration}', [\App\Http\Controllers\Admin\ActivityController::class, 'showRegistration'])->name('show');
    });

    // Routes pour la modération des avis et commentaires
    Route::prefix('moderation')->name('moderation.')->group(function () {
        // Avis (Reviews)
        Route::get('/reviews', [\App\Http\Controllers\Admin\ModerationController::class, 'reviews'])->name('reviews');
        Route::post('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ModerationController::class, 'approveReview'])->name('reviews.approve');
        Route::post('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ModerationController::class, 'rejectReview'])->name('reviews.reject');
        Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ModerationController::class, 'deleteReview'])->name('reviews.delete');

        // Commentaires (Comments)
        Route::get('/comments', [\App\Http\Controllers\Admin\ModerationController::class, 'comments'])->name('comments');
        Route::post('/comments/{comment}/approve', [\App\Http\Controllers\Admin\ModerationController::class, 'approveComment'])->name('comments.approve');
        Route::post('/comments/{comment}/reject', [\App\Http\Controllers\Admin\ModerationController::class, 'rejectComment'])->name('comments.reject');
        Route::delete('/comments/{comment}', [\App\Http\Controllers\Admin\ModerationController::class, 'deleteComment'])->name('comments.delete');
    });

});
