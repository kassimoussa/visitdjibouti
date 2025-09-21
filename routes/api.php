<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\AnonymousAuthController;
use App\Http\Controllers\Api\PoiController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\ExternalLinkController;
use App\Http\Controllers\Api\EmbassyController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\TourOperatorController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\DeviceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques d'authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Routes OAuth
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])
         ->where('provider', 'google|facebook');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
         ->where('provider', 'google|facebook');
    Route::post('/{provider}/token', [SocialAuthController::class, 'authenticateWithToken'])
         ->where('provider', 'google|facebook');
    
    // Routes pour utilisateurs anonymes (publiques)
    Route::post('/anonymous', [AnonymousAuthController::class, 'createAnonymous']);
    Route::post('/anonymous/retrieve', [AnonymousAuthController::class, 'getAnonymous']);
});

// Routes publiques pour les catégories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // Hiérarchique
    Route::get('/flat', [CategoryController::class, 'flat']); // Liste plate
    Route::get('/{id}', [CategoryController::class, 'show']);
});

// Routes publiques pour les POIs (Points d'Intérêt)
Route::prefix('pois')->group(function () {
    Route::get('/', [PoiController::class, 'index']);
    Route::get('/nearby', [PoiController::class, 'getNearby']);
    Route::get('/category/{category}', [PoiController::class, 'getByCategory']);
    Route::get('/{identifier}', [PoiController::class, 'show']);
});

// Routes publiques pour les Events
Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::get('/{identifier}', [EventController::class, 'show']);
    Route::post('/{event}/register', [EventController::class, 'register']);
});

// Routes publiques pour les informations de l'organisme
Route::get('/organization', [OrganizationController::class, 'getInfo']);

// Routes publiques pour les liens externes
Route::prefix('external-links')->group(function () {
    Route::get('/', [ExternalLinkController::class, 'index']);
    Route::get('/{id}', [ExternalLinkController::class, 'show']);
});

// Routes publiques pour les ambassades
Route::prefix('embassies')->group(function () {
    Route::get('/', [EmbassyController::class, 'index']);
    Route::get('/nearby', [EmbassyController::class, 'getNearby']);
    Route::get('/type/{type}', [EmbassyController::class, 'getByType'])
         ->where('type', 'foreign_in_djibouti|djiboutian_abroad');
    Route::get('/{id}', [EmbassyController::class, 'show']);
});

// Routes publiques pour les paramètres d'application mobile
Route::prefix('app-settings')->group(function () {
    Route::get('/', [AppSettingController::class, 'index']); // Tous les settings groupés par type
    Route::get('/flat', [AppSettingController::class, 'flat']); // Tous les settings en liste plate
    Route::get('/type/{type}', [AppSettingController::class, 'getByType']); // Settings par type
    Route::get('/splash-screens', [AppSettingController::class, 'splashScreens']); // Splash screens uniquement
    Route::get('/{key}', [AppSettingController::class, 'show']); // Setting spécifique par clé
});

// Routes publiques pour les opérateurs de tour
Route::prefix('tour-operators')->group(function () {
    Route::get('/', [TourOperatorController::class, 'index']); // Liste avec filtres avancés
    Route::get('/nearby', [TourOperatorController::class, 'getNearby']); // Proximité géographique
    Route::get('/{identifier}', [TourOperatorController::class, 'show']); // Détails (ID ou slug)
});

// Routes publiques pour les tours
Route::prefix('tours')->group(function () {
    Route::get('/', [TourController::class, 'index']); // Liste des tours avec filtres
    Route::get('/{identifier}', [TourController::class, 'show']); // Détails d'un tour (ID ou slug)
    Route::get('/{tour}/schedules', [TourController::class, 'schedules']); // Créneaux disponibles
    Route::post('/{schedule}/book', [TourController::class, 'book']); // Réserver un créneau
});

// Routes publiques pour les réservations
Route::prefix('reservations')->group(function () {
    Route::post('/', [ReservationController::class, 'store']); // Créer une réservation
    Route::get('/{confirmation_number}', [ReservationController::class, 'show']); // Détails par numéro de confirmation
});

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Routes d'authentification protégées
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::delete('/account', [AuthController::class, 'deleteAccount']);
        
        // Routes OAuth protégées
        Route::delete('/{provider}/unlink', [SocialAuthController::class, 'unlinkSocialAccount'])
             ->where('provider', 'google|facebook');
        Route::get('/linked-accounts', [SocialAuthController::class, 'getLinkedAccounts']);
        
        // Routes pour utilisateurs anonymes authentifiés
        Route::post('/convert-anonymous', [AnonymousAuthController::class, 'convertToComplete']);
        Route::put('/anonymous/preferences', [AnonymousAuthController::class, 'updatePreferences']);
        Route::delete('/anonymous', [AnonymousAuthController::class, 'deleteAnonymous']);
    });
    
    // Test route pour vérifier l'authentification
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Routes protégées pour les Events
    Route::prefix('events')->group(function () {
        Route::delete('/{event}/registration', [EventController::class, 'cancelRegistration']);
    });
    
    // Routes protégées pour les réservations utilisateur
    Route::get('/my-registrations', [EventController::class, 'myRegistrations']); // Legacy events
    
    // Routes protégées pour les réservations (nouveau système unifié)
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'getUserReservations']); // Toutes les réservations de l'utilisateur
        Route::patch('/{confirmation_number}/cancel', [ReservationController::class, 'cancel']); // Annuler une réservation
        Route::delete('/{confirmation_number}', [ReservationController::class, 'delete']); // Supprimer une réservation annulée
    });
    
    // Routes protégées pour les favoris
    Route::prefix('favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']); // Tous les favoris
        Route::get('/pois', [FavoriteController::class, 'pois']); // POIs favoris uniquement
        Route::get('/stats', [FavoriteController::class, 'stats']); // Statistiques favoris
        
        // Gestion des favoris POIs
        Route::post('/pois/{poi}', [FavoriteController::class, 'addPoi']);
        Route::delete('/pois/{poi}', [FavoriteController::class, 'removePoi']);
        
        // Gestion des favoris Events
        Route::post('/events/{event}', [FavoriteController::class, 'addEvent']);
        Route::delete('/events/{event}', [FavoriteController::class, 'removeEvent']);
    });

    // Routes protégées pour les tours
    Route::prefix('tours')->group(function () {
        Route::delete('/bookings/{reservationId}', [TourController::class, 'cancelBooking']); // Annuler une réservation
    });

    // Routes protégées pour les réservations de tours
    Route::get('/my-tour-bookings', [TourController::class, 'myBookings']); // Mes réservations de tours

    // Routes protégées pour la gestion des appareils et géolocalisation
    Route::prefix('device')->group(function () {
        Route::post('/update', [DeviceController::class, 'updateDeviceInfo']); // Mettre à jour les infos de l'appareil
        Route::get('/info', [DeviceController::class, 'getDeviceInfo']); // Récupérer les infos de l'appareil
        Route::post('/location', [DeviceController::class, 'updateLocation']); // Mettre à jour la localisation actuelle
        Route::post('/location/history', [DeviceController::class, 'recordLocationHistory']); // Enregistrer un historique de localisation
        Route::get('/location/history', [DeviceController::class, 'getLocationHistory']); // Récupérer l'historique de localisation
        Route::get('/nearby-users', [DeviceController::class, 'getNearbyUsers']); // Utilisateurs à proximité (respect vie privée)
    });
});