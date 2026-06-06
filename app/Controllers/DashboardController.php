<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\TransactionService;
use App\Services\ForecastingService;

class DashboardController extends BaseController
{
    public function __construct(
        protected TransactionService $transactionService = new TransactionService(),
        protected ForecastingService $forecastingService = new ForecastingService()
    ) {
    }

    public function dashboard()
    {
        $forecastData = $this->forecastingService->generate();

        $totalIncome = $this->transactionService->totalIncome();
        $totalExpenses = $this->transactionService->totalExpenses();
        $monthlyExpenses = $this->transactionService->monthlyExpenses();

        $totalBalance = $totalIncome - $totalExpenses;
        $netSavings = $totalIncome - $totalExpenses;

        $recentTransactions = $this->transactionService->recent(5);

        return view('dashboard/dashboard', [
            'title' => 'Dashboard',
            'pageTitle' => 'Financial Dashboard',

            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'monthlyExpenses' => $monthlyExpenses,

            'totalBalance' => $totalBalance,
            'netSavings' => $netSavings,

            'currentBalance' => $forecastData['currentBalance'],
            'expectedIncome' => $forecastData['expectedIncome'],
            'expectedExpenses' => $forecastData['expectedExpenses'],

            'forecast' => $forecastData['forecast'],
            'insights' => $forecastData['insights'],

            'recentTransactions' => $recentTransactions,
        ]);
    }
}