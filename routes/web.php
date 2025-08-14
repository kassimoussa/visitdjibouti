<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ExampleController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsCategoryController;
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

// Routes de test pour Universal Media Selector (sans authentification)
Route::get('/test/universal-media-selector', [ExampleController::class, 'test'])->name('test.universal-media-selector');
Route::get('/debug/universal-media-selector', [ExampleController::class, 'debug'])->name('debug.universal-media-selector');
Route::get('/simple/universal-media-selector', [ExampleController::class, 'simple'])->name('simple.universal-media-selector');
Route::get('/minimal/universal-media-selector', [ExampleController::class, 'minimal'])->name('minimal.universal-media-selector');

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
        $categories = \App\Models\NewsCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        $news = \App\Models\News::with(['translations', 'category', 'creator', 'featuredImage'])
            ->when(request('search'), function($query, $search) {
                $query->whereHas('translations', function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->when(request('status'), function($query, $status) {
                $query->where('status', $status);
            })
            ->when(request('category'), function($query, $category) {
                $query->where('news_category_id', $category);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.news.index', compact('categories', 'news'));
    })->name('news.index');

    Route::get('/news/create', function () {
        return view(view: 'admin.news.create');
    })->name('news.create');

    Route::get('/news/{id}/edit', function ($id) {
        return view(view: 'admin.news.edit', data: ['id' => $id]);
    })->name('news.edit');
    
    Route::get('/news/{news}', function (\App\Models\News $news) {
        return view('admin.news.show', compact('news'));
    })->name('news.show');
    
    Route::post('/news/bulk-action', function () {
        // TODO: Implement bulk actions
        return redirect()->back()->with('info', 'Actions groupées à implémenter');
    })->name('news.bulk-action');
    
    Route::post('/news/{news}/duplicate', function (\App\Models\News $news) {
        // TODO: Implement news duplication
        return redirect()->back()->with('info', 'Duplication à implémenter');
    })->name('news.duplicate');
    
    Route::delete('/news/{news}', function (\App\Models\News $news) {
        // TODO: Implement news deletion
        return redirect()->back()->with('info', 'Suppression à implémenter');
    })->name('news.destroy');

    // Routes pour les catégories d'actualités
    Route::get('/news-categories', [NewsCategoryController::class, 'index'])->name('news-categories.index');
    Route::get('/news-categories/create', [NewsCategoryController::class, 'create'])->name('news-categories.create');
    Route::get('/news-categories/{id}/edit', [NewsCategoryController::class, 'edit'])->name('news-categories.edit');
    
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

    // Route pour les exemples Universal Media Selector
    Route::get('/examples/universal-media-selector', [ExampleController::class, 'index'])->name('examples.universal-media-selector');

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
});

Route::get('/test-livewire', function() {
    return response()->json([
        'storage_path' => storage_path('app/livewire-tmp'),
        'storage_exists' => is_dir(storage_path('app/livewire-tmp')),
        'permissions' => substr(sprintf('%o', fileperms(storage_path('app'))), -4),
    ]);
});