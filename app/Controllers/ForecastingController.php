<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class ForecastingController extends BaseController
{
    public function forecasting()
    {
        return view('forecasting/forecasting', ['title' => 'Forecasting', 'pageTitle' => 'Financial Forecasting']);
    }
}
