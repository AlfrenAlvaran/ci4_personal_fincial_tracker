<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IncomeSourceModel;
use App\Services\TransactionService;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    public function __construct(
        protected TransactionService $transactionService = new TransactionService(),
    )
    {

    }
    public function dashboard()
    {
       
        return view('dashboard/dashboard', [
            'title' => 'Dashboard',
            'pageTitle' => 'Financial Dashboard',
            'totalIncome' => $this->transactionService->totalIncome(),
            'monthlyExpenses' => $this->transactionService->monthlyExpenses(),
            'totalExpenses' => $this->transactionService->totalExpenses(),
        ]);

    }


}


