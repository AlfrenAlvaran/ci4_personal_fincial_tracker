<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\CategoryService;
use App\Services\TransactionService;

class TransactionController extends BaseController
{
    public function __construct(
        protected CategoryService $categoryService = new CategoryService(),
        protected TransactionService $transactionService = new TransactionService()
    ) {}

    public function transaction()
    {
        return view('transaction/transaction', [
            'title' => 'Transactions',
            'pageTitle' => 'Transaction Management',
            'transactions' => $this->transactionService->findAll(),
        ]);
    }

    public function create()
    {
        return view('transaction/create', [
            'title' => 'Create Transaction',
            'pageTitle' => 'Create Transaction',
            'categories' => $this->categoryService->findAll(),
        ]);
    }

    public function details($id)
    {
        $transaction = $this->transactionService->findById($id);

        if (!$transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaction not found.');
        }


        return view('transaction/view', [
            'title' => 'Transaction Details',
            'pageTitle' => 'Transaction Details',
            'transaction' => $transaction,
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

    public function edit($id)
    {
        $transaction = $this->transactionService->findById($id);

        if (!$transaction) {
            return redirect()->to('/transactions')->with('error', 'Transaction not found.');
        }

        return view('transaction/edit', [
            'title' => 'Edit Transaction',
            'pageTitle' => 'Edit Transaction',
            'transaction' => $transaction,
            'categories' => $this->categoryService->findAll(),
        ]);
    }

    public function update($id)
    {
        $response = $this->transactionService->update($id, $this->request->getPost());

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

    public function delete($id)
    {
        try {
            $result = $this->transactionService->delete($id);

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['message']);
            }

            return redirect()->to(site_url('transactions'))
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }
}
