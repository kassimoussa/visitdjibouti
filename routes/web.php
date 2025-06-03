<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PoiController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\DashboardController;
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
    Route::get('/dashboard', function () {
        return view(view: 'admin.index');
    })->name('dashboard');


    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/info', [ProfileController::class, 'updateInfo'])->name('profile.update.info');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update.avatar');

    // Routes pour les points d'intérêt
    Route::get('/pois', [PoiController::class, 'index'])->name('pois.index');
    Route::get('/pois/create', [PoiController::class, 'create'])->name('pois.create'); 
    Route::get('/pois/{id}/edit', [PoiController::class, 'edit'])->name('pois.edit');
    Route::get('/pois/{poi}', [PoiController::class, 'show'])->name('pois.show');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    // Catégories (une seule route suffisante avec Livewire)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Routes pour les actualités
    Route::get('/news', function () {
        return view(view: 'admin.news.index');
    })->name('news.index');

    Route::get('/news/create', function () {
        return view(view: 'admin.news.create');
    })->name('news.create');

    Route::get('/news/{id}/edit', function ($id) {
        return view(view: 'admin.news.edit', data: ['id' => $id]);
    })->name('news.edit');

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
        return view(view: 'admin.settings.index');
    })->name('settings.index');
});
