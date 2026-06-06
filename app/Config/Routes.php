<?php

use App\Controllers\Auth\AuthController;
use App\Controllers\BudgetController;
use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\ForecastingController;
use App\Controllers\IncomeSourceController;
use App\Controllers\TransactionController;
use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/login', [AuthController::class, 'login']);
$routes->get('/register', [AuthController::class, 'register']);
$routes->post('/register', [AuthController::class, 'store']);
$routes->get('/verify-email/(:segment)', [AuthController::class, 'verify/$1']);
$routes->post('/authenticate', [AuthController::class, 'authenticate']);
$routes->post('/logout', [AuthController::class, 'logout']);
$routes->get('/forgot-password', [AuthController::class, 'forgotPasswordForm']);
$routes->post('/forgot-password', [AuthController::class, 'sendResetLink']);
$routes->get('/reset-password/(:segment)', [AuthController::class, 'resetPasswordForm/$1']);
$routes->post('/reset-password', [AuthController::class, 'updatePassword']);
$routes->get('auth/google', [AuthController::class, 'google']);
$routes->get('auth/google-callback', [AuthController::class, 'googleCallback']);


$routes->group('', ['filter' => 'auth'], function ($routes) {

    $routes->get('/', [DashboardController::class, 'dashboard']);
    #CATEGORY MANAGEMENT
    $routes->group('categories', function ($routes) {
        $routes->get('/', [CategoryController::class, 'category']);
        $routes->get('create', [CategoryController::class, 'create']);
        $routes->post('store', [CategoryController::class, 'store']);
    });
    #CATEGORY MANAGEMENT

    #TRANSACTION MANAGEMENT
    $routes->group('transactions', function ($routes) {
        $routes->get('/', [TransactionController::class, 'transaction']);
        $routes->get('create', [TransactionController::class, 'create']);
        $routes->post('store', [TransactionController::class, 'store']);

    });

    #Budget Management
    $routes->group('budgets', function ($routes) {
        $routes->get('', [BudgetController::class, 'budget']);
        $routes->post('create', [BudgetController::class,'store']);
    });
    #Budget Management
    

    // FORECASTING MANAGEMENT
    $routes->group('forecasting', function ($routes) {
        $routes->get('', [ForecastingController::class, 'forecasting']);
    });
    // FORECASTING MANAGEMENT 
});