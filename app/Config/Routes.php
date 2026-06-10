<?php

use App\Controllers\Auth\AuthController;
use App\Controllers\BudgetController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\ForecastingController;
use App\Controllers\ReportController;
use App\Controllers\SettingController;
use App\Controllers\TransactionController;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (PUBLIC)
|--------------------------------------------------------------------------
*/

$routes->get('login', [AuthController::class, 'login']);
$routes->get('register', [AuthController::class, 'register']);
$routes->post('register', [AuthController::class, 'store']);

$routes->post('authenticate', [AuthController::class, 'authenticate']);
$routes->post('logout', [AuthController::class, 'logout']);

$routes->get('verify-email/(:segment)', [AuthController::class, 'verify/$1']);

$routes->get('forgot-password', [AuthController::class, 'forgotPasswordForm']);
$routes->post('forgot-password', [AuthController::class, 'sendResetLink']);

$routes->get('reset-password/(:segment)', [AuthController::class, 'resetPasswordForm/$1']);
$routes->post('reset-password', [AuthController::class, 'updatePassword']);

$routes->get('auth/google', [AuthController::class, 'google']);
$routes->get('auth/google-callback', [AuthController::class, 'googleCallback']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

$routes->group('', ['filter' => 'auth'], function ($routes) {

    /*
    | DASHBOARD
    */
    $routes->get('/', [DashboardController::class, 'dashboard']);

    /*
    | CATEGORIES
    */
    $routes->group('categories', function ($routes) {
        $routes->get('/', [CategoryController::class, 'category']);
        $routes->get('create', [CategoryController::class, 'create']);
        $routes->post('store', [CategoryController::class, 'store']);
        $routes->get('edit/(:num)', [CategoryController::class, 'edit/$1']);
        $routes->post('update/(:num)', [CategoryController::class, 'update/$1']);
        $routes->get('delete/(:num)', [CategoryController::class, 'delete']);
    });

    /*
    | TRANSACTIONS
    */
    $routes->group('transactions', function ($routes) {
        $routes->get('/', [TransactionController::class, 'transaction']);
        $routes->get('create', [TransactionController::class, 'create']);
        $routes->post('store', [TransactionController::class, 'store']);
        $routes->get('detail/(:num)', [TransactionController::class, 'details/$1']);
        $routes->get('edit/(:num)', [TransactionController::class, 'edit']);
        $routes->post('update/(:num)', [TransactionController::class, 'update']);
        $routes->get('delete/(:num)', [TransactionController::class, 'delete']);
    });

    /*
    | BUDGETS
    */
    $routes->group('budgets', function ($routes) {
        $routes->get('/', [BudgetController::class, 'budget']);
        $routes->post('create', [BudgetController::class, 'store']);
    });

    /*
    | FORECASTING
    */
    $routes->group('forecasting', function ($routes) {
        $routes->get('/', [ForecastingController::class, 'forecasting']);
    });

    /*
    | REPORTS
    */
    $routes->group('reports', function ($routes) {
        $routes->get('/', [ReportController::class, 'index']);
        $routes->get('export', [ReportController::class, 'export']);
        $routes->get('debug', [ReportController::class, 'debug']);
    });

    /*
    | SETTINGS
    */

    $routes->group('settings', function ($routes) {
        $routes->get('/', [SettingController::class, 'setting']);
    });

    /*
    | PROFILE
    */

    $routes->group('profile', function ($routes) {
        $routes->get('/', [SettingController::class, 'profile']);
    });
});
