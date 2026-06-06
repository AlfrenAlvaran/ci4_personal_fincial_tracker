<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ForecastingService;
use App\Services\TransactionService;
use CodeIgniter\HTTP\ResponseInterface;

class ForecastingController extends BaseController
{
    public function __construct(
        protected ForecastingService $forecastingService = new ForecastingService()
    )
    {

    }
 public function forecasting()
{
    $data = $this->forecastingService->generate();

    return view('forecasting/forecasting', [
        'title' => 'Forecasting',
        'pageTitle' => 'Financial Forecasting',

        'currentBalance' => $data['currentBalance'],
        'expectedIncome' => $data['expectedIncome'],
        'expectedExpenses' => $data['expectedExpenses'],
        'expectedSavings' => $data['expectedSavings'],
        'totalBudget' => $data['totalBudget'],
        'forecast' => $data['forecast'],
        'insights' => $data['insights'],
    ]);
}
}
