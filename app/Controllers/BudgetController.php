<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\BudgetService;
use App\Services\CategoryService;
use CodeIgniter\HTTP\ResponseInterface;

class BudgetController extends BaseController
{
    public function __construct(
        protected BudgetService $budgetService = new BudgetService(),
        protected CategoryService $categoryService = new CategoryService(),
    ) {

    }
    public function budget()
    {
        return view('budgets/budget', [
            'title' => 'Budget',
            'pageTitle' => 'Budget Management',
            'categoryExpenses' => $this->categoryService->findByExpenses(),
            'budgets' => $this->budgetService->findAllWithSpent()
        ]);
    }

    public function store()
    {
        $result = $this->budgetService->create($this->request->getPost());

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('errors', $result['errors'] ?? ['Something went wrong']);
        }

        return redirect()
            ->to('/budgets')
            ->with('success', $result['message']);
    }    
}
