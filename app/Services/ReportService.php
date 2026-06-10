<?php

namespace App\Services;

use App\Models\TransactionModel;

class ReportService
{
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
    }

    private function userId(): int
    {
        return (int) session()->get('user_id');
    }

    public function getSummary(?string $month, ?string $category): array
    {
        $transactions = $this->transactionModel->getFiltered($this->userId(), $month, $category);

        $totalIncome   = 0;
        $totalExpenses = 0;

        foreach ($transactions as $t) {
            if ($t['transaction_type'] === 'income') {
                $totalIncome += (float) $t['amount'];
            } elseif ($t['transaction_type'] === 'expenses') {
                $totalExpenses += (float) $t['amount'];
            }
        }

        $netSavings  = $totalIncome - $totalExpenses;
        $savingsRate = $totalIncome > 0
            ? round(($netSavings / $totalIncome) * 100)
            : 0;

        return compact('totalIncome', 'totalExpenses', 'netSavings', 'savingsRate');
    }

    public function getMonthlyChartData(?string $month): array
    {
        $rows = $this->transactionModel->getMonthlyTotals($this->userId(), $month);

        $labels   = array_column($rows, 'label');
        $income   = array_map(fn($r) => (float) $r['total_income'],   $rows);
        $expenses = array_map(fn($r) => (float) $r['total_expenses'], $rows);

        return compact('labels', 'income', 'expenses');
    }

    public function getCategoryChartData(?string $month): array
    {
        $rows = $this->transactionModel->getCategoryTotals($this->userId(), 'expenses', $month);

        $labels = array_column($rows, 'category');
        $data   = array_map(fn($r) => (float) $r['total'], $rows);

        return compact('labels', 'data');
    }

    public function getTopSpending(?string $month, int $limit = 5): array
    {
        return $this->transactionModel->getTopCategories($this->userId(), 'expenses', $month, $limit);
    }

    public function getCategories(): array
    {
        return $this->transactionModel->getDistinctCategories($this->userId());
    }
}