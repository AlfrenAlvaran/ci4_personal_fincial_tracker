<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ForecastingService;

class ForecastingController extends BaseController
{
    public function __construct(
        protected ForecastingService $forecastingService = new ForecastingService()
    ) {
    }

    public function forecasting()
    {
        $data = $this->forecastingService->generate();

        return view('forecasting/forecasting', [
            'title'               => 'Forecasting',
            'pageTitle'           => 'Financial Forecasting',

            // Balance & history
            'currentBalance'      => $data['currentBalance'],
            'historicalMonths'    => $data['historicalMonths'],

            // Budget
            'totalBudget'         => $data['totalBudget'],
            'budgetByCategory'    => $data['budgetByCategory'],
            'expenseByCategory'   => $data['expenseByCategory'],

            // Forecast array (contains ensemble, models, confidence, categories per month)
            'forecast'            => $data['forecast'],

            // Savings milestones
            'savingsGoalProgress' => $data['savingsGoalProgress'],

            // Insights
            'insights'            => $data['insights'],
        ]);
    }
}