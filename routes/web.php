
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\SubscriptionPaymentController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Controllers\RegistrationController;
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
| Connexion
|--------------------------------------------------------------------------
*/
Route::get('/inscription', [RegistrationController::class, 'create'])
    ->name('register');

Route::post('/inscription', [RegistrationController::class, 'store'])
    ->name('register.store');

    
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/connexion', [AuthController::class, 'login'])
        ->name('login.store');
});

/*
|--------------------------------------------------------------------------
| Espace réservé aux utilisateurs connectés
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/deconnexion', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Fonctionnalités avec abonnement valide
    |--------------------------------------------------------------------------
    */

    Route::middleware('subscription')->group(function () {

        Route::resource('medicaments', MedicineController::class)
            ->except(['show']);

        Route::prefix('lots')->name('lots.')->group(function () {
            Route::get('/', [LotController::class, 'index'])->name('index');
            Route::get('/nouveau', [LotController::class, 'create'])->name('create');
            Route::post('/', [LotController::class, 'store'])->name('store');
        });

        Route::prefix('ventes')->name('ventes.')->group(function () {
            Route::get('/', [SaleController::class, 'index'])->name('index');
            Route::get('/nouvelle', [SaleController::class, 'create'])->name('create');
            Route::post('/', [SaleController::class, 'store'])->name('store');
        });

        Route::get('/rapports/ventes', [SalesReportController::class, 'index'])
            ->name('reports.sales');

        Route::middleware(AdminMiddleware::class)->group(function () {
            Route::resource('utilisateurs', UserController::class)
                ->except(['show']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Administration centrale : Super Admin uniquement
    |--------------------------------------------------------------------------
    */

    Route::middleware(SuperAdminMiddleware::class)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::resource('licenses', LicenseController::class)
                ->except(['show', 'destroy']);

            Route::get(
                'licenses/{license}/payments/create',
                [SubscriptionPaymentController::class, 'create']
            )->name('licenses.payments.create');

            Route::post(
                'licenses/{license}/payments',
                [SubscriptionPaymentController::class, 'store']
            )->name('licenses.payments.store');
        });
});
