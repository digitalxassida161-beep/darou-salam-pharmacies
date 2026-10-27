
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Page d'accueil
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Connexion : utilisateurs non connectés
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/connexion', [AuthController::class, 'login'])
        ->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Espace sécurisé : utilisateurs connectés
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Déconnexion
    Route::post('/deconnexion', [AuthController::class, 'logout'])
        ->name('logout');

    // Gestion des médicaments
    Route::resource('medicaments', MedicineController::class)
        ->except(['show']);

    // Gestion des lots et des entrées de stock
    Route::prefix('lots')->name('lots.')->group(function () {
        Route::get('/', [LotController::class, 'index'])
            ->name('index');

        Route::get('/nouveau', [LotController::class, 'create'])
            ->name('create');

        Route::post('/', [LotController::class, 'store'])
            ->name('store');
    });

    // Gestion des ventes
    Route::prefix('ventes')->name('ventes.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])
            ->name('index');

        Route::get('/nouvelle', [SaleController::class, 'create'])
            ->name('create');

        Route::post('/', [SaleController::class, 'store'])
            ->name('store');
    });

    // Rapports réservés à l'administrateur
    // Rapports accessibles aux utilisateurs connectés
Route::get('/rapports/ventes', [SalesReportController::class, 'index'])
    ->name('reports.sales');
});

// Gestion des utilisateurs : administrateurs uniquement
Route::middleware(AdminMiddleware::class)->group(function () {
    Route::resource('utilisateurs', UserController::class)
        ->except(['show']);
});



Route::middleware('auth')->group(function () {

    // Les autres routes de l'application restent ici.

    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('utilisateurs', UserController::class)
            ->except(['show']);
    });
});