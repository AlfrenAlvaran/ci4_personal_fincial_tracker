<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\CategoryService;
use App\Services\TransactionService;
use CodeIgniter\HTTP\ResponseInterface;

class TransactionController extends BaseController
{
    public function __construct(
        protected CategoryService $categoryService = new CategoryService(),
        protected TransactionService $transactionService = new TransactionService()
    ) {

    }

    public function transaction()
    {
        return view('transaction/transaction', [
            'title' => 'Transactions',
            'pageTitle' => 'Transaction Management',
            'transactions' => $this->transactionService->findAll(),
            'categories' => $this->categoryService->findAll(),
        ]);
    }

    public function create()
    {
        return view('transaction/create', [
            'title' => 'Transactions',
            'pageTitle' => 'Transaction Management',
            'transactions' => null,
            'categories' => $this->categoryService->findAll(),
        ]);
    }

    public function store()
    {
        $response = $this->transactionService->create($this->request->getPost());
        if (!$response['success']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $response['errors'] ?? [
                    'general' => $response['message']
                ]);
        }

        return redirect()
            ->to('/transactions')
            ->with('success', $response['message']);
    }
}
