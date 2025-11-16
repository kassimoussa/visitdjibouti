<?php

use App\Http\Controllers\Api\AnonymousAuthController;
use App\Http\Controllers\Api\AppSettingController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\EmbassyController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ExternalLinkController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\PoiController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TourOperatorController;
use App\Http\Controllers\Api\TourReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|*/

// Routes publiques d'authentification
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Routes de récupération de mot de passe
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

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

// Routes publiques pour le contenu global
Route::prefix('content')->group(function () {
    Route::get('/all', [ContentController::class, 'getAllContent']); // Tout le contenu (POIs, Events, Tours, Activities)
    Route::get('/geolocated', [ContentController::class, 'getGeolocatedContent']); // Contenu avec coordonnées GPS
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
});

// Routes publiques pour les activités
Route::prefix('activities')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ActivityController::class, 'index']); // Liste des activités avec filtres
    Route::get('/nearby', [\App\Http\Controllers\Api\ActivityController::class, 'nearby']); // Activités à proximité
    Route::get('/{identifier}', [\App\Http\Controllers\Api\ActivityController::class, 'show']); // Détails d'une activité (ID ou slug)
    Route::post('/{activity}/register', [\App\Http\Controllers\Api\ActivityController::class, 'register']); // S'inscrire à une activité
});

// Routes publiques pour les régions
Route::prefix('regions')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\RegionController::class, 'index']); // Liste des régions avec compteurs
    Route::get('/statistics', [\App\Http\Controllers\Api\RegionController::class, 'statistics']); // Statistiques par région
    Route::get('/{region}', [\App\Http\Controllers\Api\RegionController::class, 'show']); // Contenu d'une région spécifique
});

// Routes publiques pour les avis (reviews) sur les POIs
Route::prefix('pois/{poi}/reviews')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ReviewController::class, 'index']); // Liste des avis d'un POI
    Route::post('/', [\App\Http\Controllers\Api\ReviewController::class, 'store']); // Créer un avis (authentifié ou invité)
});

// Routes publiques pour les commentaires polymorphiques
Route::prefix('comments')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\CommentController::class, 'index']); // Liste des commentaires (avec commentable_type et commentable_id)
    Route::post('/', [\App\Http\Controllers\Api\CommentController::class, 'store']); // Créer un commentaire (authentifié ou invité)
});

// Routes publiques pour les réservations de tours
Route::prefix('tour-reservations')->group(function () {
    Route::get('/{reservation}', [TourReservationController::class, 'show']);
    Route::post('/{tour}/register', [TourReservationController::class, 'store']);  // Réserver un tour
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

    // Routes protégées pour les réservations de tours (nouveau système)
    Route::prefix('tour-reservations')->group(function () {
        Route::get('/', [TourReservationController::class, 'index']); // User's tour reservations
        Route::patch('/{reservation}/cancel', [TourReservationController::class, 'cancel']);
        Route::patch('/{reservation}', [TourReservationController::class, 'update']);
        Route::delete('/{reservation}', [TourReservationController::class, 'destroy']); // Supprimer une réservation annulée
    });

    // Routes protégées pour les inscriptions aux activités
    Route::prefix('activity-registrations')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ActivityController::class, 'myRegistrations']); // Mes inscriptions
        Route::patch('/{registration}/cancel', [\App\Http\Controllers\Api\ActivityController::class, 'cancelRegistration']); // Annuler une inscription
        Route::delete('/{registration}', [\App\Http\Controllers\Api\ActivityController::class, 'deleteRegistration']); // Supprimer une inscription annulée
    });

    // Routes protégées pour les avis (reviews)
    Route::prefix('reviews')->group(function () {
        Route::get('/my-reviews', [\App\Http\Controllers\Api\ReviewController::class, 'myReviews']); // Mes avis
        Route::put('/{review}', [\App\Http\Controllers\Api\ReviewController::class, 'update']); // Modifier un avis
        Route::delete('/{review}', [\App\Http\Controllers\Api\ReviewController::class, 'destroy']); // Supprimer un avis
        Route::post('/{review}/helpful', [\App\Http\Controllers\Api\ReviewController::class, 'markHelpful']); // Marquer comme utile (toggle)
    });

    // Routes protégées pour les commentaires
    Route::prefix('comments')->group(function () {
        Route::get('/my-comments', [\App\Http\Controllers\Api\CommentController::class, 'myComments']); // Mes commentaires
        Route::put('/{comment}', [\App\Http\Controllers\Api\CommentController::class, 'update']); // Modifier un commentaire
        Route::delete('/{comment}', [\App\Http\Controllers\Api\CommentController::class, 'destroy']); // Supprimer un commentaire
        Route::post('/{comment}/like', [\App\Http\Controllers\Api\CommentController::class, 'toggleLike']); // Liker/Unliker (toggle)
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

/*
|--------------------------------------------------------------------------
| Tour Operator API Routes
|--------------------------------------------------------------------------
|
| Routes API pour les opérateurs touristiques
|
*/

// Routes protégées par authentification Sanctum pour les tour operators
Route::middleware(['auth:operator-api'])->prefix('operator')->name('operator.api.')->group(function () {

    // Gestion des événements
    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\Operator\EventController::class, 'index'])
            ->name('index'); // Liste des événements de l'opérateur
        Route::get('/{event}', [\App\Http\Controllers\Api\Operator\EventController::class, 'show'])
            ->name('show'); // Détails d'un événement
        Route::patch('/{event}', [\App\Http\Controllers\Api\Operator\EventController::class, 'update'])
            ->name('update'); // Mise à jour d'un événement
        Route::get('/{event}/reservations', [\App\Http\Controllers\Api\Operator\EventController::class, 'reservations'])
            ->name('reservations'); // Réservations d'un événement
    });

    // Gestion des réservations
    // TODO: Créer App\Http\Controllers\Api\Operator\ReservationController
    // Route::prefix('reservations')->name('reservations.')->group(function () {
    //     Route::get('/', [\App\Http\Controllers\Api\Operator\ReservationController::class, 'index'])
    //         ->name('index'); // Toutes les réservations de l'opérateur
    //     Route::get('/{reservation}', [\App\Http\Controllers\Api\Operator\ReservationController::class, 'show'])
    //         ->name('show'); // Détails d'une réservation
    //     Route::patch('/{reservation}/confirm', [\App\Http\Controllers\Api\Operator\ReservationController::class, 'confirm'])
    //         ->name('confirm'); // Confirmer une réservation
    //     Route::patch('/{reservation}/cancel', [\App\Http\Controllers\Api\Operator\ReservationController::class, 'cancel'])
    //         ->name('cancel'); // Annuler une réservation
    // });

    // Gestion des tours
    // TODO: Créer App\Http\Controllers\Api\Operator\TourController
    // Route::prefix('tours')->name('tours.')->group(function () {
    //     Route::get('/', [\App\Http\Controllers\Api\Operator\TourController::class, 'index'])
    //         ->name('index'); // Liste des tours de l'opérateur
    //     Route::get('/{tour}', [\App\Http\Controllers\Api\Operator\TourController::class, 'show'])
    //         ->name('show'); // Détails d'un tour
    //     Route::patch('/{tour}', [\App\Http\Controllers\Api\Operator\TourController::class, 'update'])
    //         ->name('update'); // Mise à jour d'un tour

    //     // Gestion des calendriers de tours
    //     Route::prefix('{tour}/schedules')->name('schedules.')->group(function () {
    //         Route::get('/', [\App\Http\Controllers\Api\Operator\TourController::class, 'schedules'])
    //             ->name('index'); // Calendriers d'un tour
    //         Route::post('/', [\App\Http\Controllers\Api\Operator\TourController::class, 'createSchedule'])
    //             ->name('create'); // Créer un calendrier
    //         Route::patch('/{schedule}', [\App\Http\Controllers\Api\Operator\TourController::class, 'updateSchedule'])
    //             ->name('update'); // Mettre à jour un calendrier
    //         Route::delete('/{schedule}', [\App\Http\Controllers\Api\Operator\TourController::class, 'deleteSchedule'])
    //             ->name('delete'); // Supprimer un calendrier
    //     });
    // });

    //     // Statistiques et rapports
    //     Route::prefix('reports')->name('reports.')->group(function () {
    //         Route::get('/dashboard', [\App\Http\Controllers\Api\Operator\DashboardController::class, 'statistics'])
    //             ->name('dashboard'); // Statistiques du dashboard
    //         Route::get('/events', [\App\Http\Controllers\Api\Operator\EventController::class, 'reports'])
    //             ->name('events'); // Rapports sur les événements
    //         Route::get('/tours', [\App\Http\Controllers\Api\Operator\TourController::class, 'reports'])
    //             ->name('tours'); // Rapports sur les tours
    //         Route::get('/reservations', [\App\Http\Controllers\Api\Operator\ReservationController::class, 'reports'])
    //             ->name('reservations'); // Rapports sur les réservations
    //     });
    //
    //     // Profil et opérateur touristique
    //     Route::prefix('profile')->name('profile.')->group(function () {
    //         Route::get('/', [\App\Http\Controllers\Api\Operator\ProfileController::class, 'show'])
    //             ->name('show'); // Profil de l'utilisateur
    //         Route::patch('/', [\App\Http\Controllers\Api\Operator\ProfileController::class, 'update'])
    //             ->name('update'); // Mise à jour du profil
    //         Route::patch('/password', [\App\Http\Controllers\Api\Operator\ProfileController::class, 'updatePassword'])
    //             ->name('password'); // Changement de mot de passe
    //
    //         Route::get('/tour-operator', [\App\Http\Controllers\Api\Operator\ProfileController::class, 'showTourOperator'])
    //             ->name('tour-operator.show'); // Profil de l'opérateur touristique
    //         Route::patch('/tour-operator', [\App\Http\Controllers\Api\Operator\ProfileController::class, 'updateTourOperator'])
    //             ->name('tour-operator.update'); // Mise à jour de l'opérateur touristique
    //     });
});

/*
|--------------------------------------------------------------------------
| Debug Routes (only for development)
|--------------------------------------------------------------------------
*/
if (config('app.debug')) {
    Route::get('/debug/tour-reservations', function () {
        $debug = [];

        // Check if table exists
        $debug['table_exists'] = \Illuminate\Support\Facades\Schema::hasTable('tour_reservations');

        if ($debug['table_exists']) {
            $debug['columns'] = \Illuminate\Support\Facades\Schema::getColumnListing('tour_reservations');
        }

        // Check tours table
        $debug['tours_table_exists'] = \Illuminate\Support\Facades\Schema::hasTable('tours');
        if ($debug['tours_table_exists']) {
            $debug['tours_columns'] = \Illuminate\Support\Facades\Schema::getColumnListing('tours');
            $debug['tour_1_exists'] = \App\Models\Tour::where('id', 1)->exists();

            if ($debug['tour_1_exists']) {
                $tour = \App\Models\Tour::find(1);
                $debug['tour_1_data'] = [
                    'id' => $tour->id,
                    'max_participants' => $tour->max_participants,
                    'current_participants' => $tour->current_participants ?? 'NULL',
                    'status' => $tour->status,
                ];
            }
        }

        // Check app_users table
        $debug['app_users_table_exists'] = \Illuminate\Support\Facades\Schema::hasTable('app_users');

        return response()->json($debug);
    });
}
