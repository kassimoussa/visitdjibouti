<?php

use Illuminate\Support\Facades\Route;

// Redirection de la page d'accueil vers le dashboard
Route::redirect('/', '/dashboard');

// Dashboard
Route::get('/dashboard', function () {
    return view(view: 'admin.index');
})->name('dashboard');

// Routes pour les points d'intérêt (POIs)
Route::get('/pois', function () {
    return view(view: 'admin.pois.index');
})->name('pois.index');

Route::get('/pois/create', function () {
    return view(view: 'admin.pois.create');
})->name('pois.create');

Route::get('/pois/{id}/edit', function ($id) {
    return view(view: 'admin.pois.edit', data: ['id' => $id]);
})->name('pois.edit');

Route::get('/pois/{id}', function ($id) {
    return view(view: 'admin.pois.show', data: ['id' => $id]);
})->name('pois.show');

// Routes pour les événements
Route::get('/events', function () {
    return view(view: 'admin.events.index');
})->name('events.index');

Route::get('/events/create', function () {
    return view(view: 'admin.events.create');
})->name('events.create');

Route::get('/events/{id}/edit', function ($id) {
    return view(view: 'admin.events.edit', data: ['id' => $id]);
})->name('events.edit');

// Routes pour les catégories
Route::get('/categories', function () {
    return view(view: 'admin.categories.index');
})->name('categories.index');

Route::get('/categories/create', function () {
    return view(view: 'admin.categories.create');
})->name('categories.create');

Route::get('/categories/{id}/edit', function ($id) {
    return view(view: 'admin.categories.edit', data: ['id' => $id]);
})->name('categories.edit');

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
Route::get('/media', function () {
    return view(view: 'admin.media.index');
})->name('media.index');

Route::get('/media/upload', function () {
    return view(view: 'admin.media.upload');
})->name('media.upload');

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

// Route pour le profil utilisateur
Route::get('/profile', function () {
    return view(view: 'admin.profile');
})->name('profile');