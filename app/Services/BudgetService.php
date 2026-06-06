<?php

namespace App\Services;

use App\DTO\BudgetDTO;
use App\Models\BudgetModel;
use App\Models\CategoryModel;
use App\Validation\BudgetValidator;
use Exception;

class BudgetService extends BaseService
{

    public function __construct(
        protected BudgetModel $budgetModel = new BudgetModel(),
        protected CategoryModel $categoryModel = new CategoryModel()
    ) {
    }


    public function create(array $data): array
    {

        $validation = BudgetValidator::validate($data);

        if (!$validation['ok']) {
            return [
                'success' => false,
                'errors' => $validation['errors']
            ];
        }

        $dto = BudgetDTO::fromArray($data);

        $category = $this->categoryModel
            ->where('id', $dto->category_id)
            ->where('user_id', $this->userId())
            ->first();

        if (!$category) {
            return [
                'success' => false,
                'message' => 'Invalid category access.',
            ];
        }
        $month = date('n');
        $year = date('Y');

        $exists = $this->budgetModel
            ->where('user_id', $this->userId())
            ->where('category_id', $dto->category_id)
            ->where('budget_month', $month)
            ->where('budget_year', $year)
            ->first();

        if ($exists) {
            return [
                'success' => false,
                'message' => 'A budget already exists for this category this month.',
            ];
        }

        $id = $this->budgetModel->insert([
            'user_id' => $this->userId(),
            'category_id' => $dto->category_id,
            'limit_amount' => $dto->limit_amount,
            'budget_month' => $month,
            'budget_year' => $year,
        ]);

        if (!$id) {
            return [
                'success' => false,
                'message' => 'Failed to insert budget',
                'db_error' => $this->budgetModel->errors()
            ];
        }

        return [
            'success' => true,
            'message' => ''
        ];
    }


    public function findAllWithSpent()
    {
        $month = (int) date('n');
        $year = (int) date('Y');

        return $this->budgetModel
            ->select('
            budgets.*,
            categories.category_name,
            COALESCE(SUM(transactions.amount), 0) as spent
        ')
            ->join('categories', 'categories.id = budgets.category_id')
            ->join(
                'transactions',
                "transactions.category_id = budgets.category_id
            AND transactions.user_id = budgets.user_id
            AND transactions.transaction_type = 'expenses'
            AND MONTH(transactions.transaction_date) = {$month}
            AND YEAR(transactions.transaction_date) = {$year}",
                'left'
            )
            ->where('budgets.user_id', $this->userId())
            ->groupBy('budgets.id')
            ->findAll();
    }
}