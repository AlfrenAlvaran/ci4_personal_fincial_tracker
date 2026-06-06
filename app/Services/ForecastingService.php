<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BudgetModel;
use App\Models\TransactionModel;

class ForecastingService extends BaseService
{
    public function __construct(
        protected TransactionModel $transactionModel = new TransactionModel(),
        protected BudgetModel $budgetModel = new BudgetModel()
    ) {

    }

    public function generate(): array
    {
        $userId = $this->userId();

        $startDate = date('Y-m-d', strtotime('-6 months'));

        $incomeRow = $this->transactionModel
            ->select('COALESCE(SUM(amount),0) as total')
            ->where('user_id', $userId)
            ->where('transaction_type', 'income')
            ->where('transaction_date >=', $startDate)
            ->get()
            ->getRowArray();

        $expenseRow = $this->transactionModel
            ->select('COALESCE(SUM(amount),0) as total')
            ->where('user_id', $userId)
            ->where('transaction_type', 'expenses')
            ->where('transaction_date >=', $startDate)
            ->get()
            ->getRowArray();

        $budgetRow = $this->budgetModel
            ->select('COALESCE(SUM(limit_amount),0) as total')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        $totalIncome = (float) ($incomeRow['total'] ?? 0);
        $totalExpenses = (float) ($expenseRow['total'] ?? 0);
        $totalBudget = (float) ($budgetRow['total'] ?? 0);

        $incomeMonths = $this->transactionModel
            ->select('COUNT(DISTINCT DATE_FORMAT(transaction_date,"%Y-%m")) as total')
            ->where('user_id', $userId)
            ->where('transaction_type', 'income')
            ->where('transaction_date >=', $startDate)
            ->get()
            ->getRowArray();

        $expenseMonths = $this->transactionModel
            ->select('COUNT(DISTINCT DATE_FORMAT(transaction_date,"%Y-%m")) as total')
            ->where('user_id', $userId)
            ->where('transaction_type', 'expense')
            ->where('transaction_date >=', $startDate)
            ->get()
            ->getRowArray();

        $incomeMonthCount = max(1, (int) ($incomeMonths['total'] ?? 1));
        $expenseMonthCount = max(1, (int) ($expenseMonths['total'] ?? 1));

        $expectedIncome = round(
            $totalIncome / $incomeMonthCount,
            2
        );

        $expectedExpenses = round(
            $totalExpenses / $expenseMonthCount,
            2
        );

        $expectedSavings = round(
            $expectedIncome - $expectedExpenses,
            2
        );

        $currentBalance = round(
            $totalIncome - $totalExpenses,
            2
        );

        $forecast = [];

        $runningBalance = $currentBalance;

        for ($i = 1; $i <= 6; $i++) {

            $runningBalance += $expectedSavings;

            $forecast[] = [
                'month' => date(
                    'M Y',
                    strtotime("+{$i} month")
                ),
                'income' => $expectedIncome,
                'expenses' => $expectedExpenses,
                'balance' => round(
                    $runningBalance,
                    2
                ),
            ];
        }

        $insights = [];

        if ($expectedSavings > 0) {
            $insights[] = [
                'type' => 'success',
                'message' => 'Your savings trend is positive.'
            ];
        }

        if ($expectedSavings < 0) {
            $insights[] = [
                'type' => 'danger',
                'message' => 'Projected expenses exceed income.'
            ];
        }

        if (
            $totalBudget > 0 &&
            $expectedExpenses > $totalBudget
        ) {
            $insights[] = [
                'type' => 'warning',
                'message' => 'Projected expenses may exceed your budget.'
            ];
        }

        if (empty($insights)) {
            $insights[] = [
                'type' => 'info',
                'message' => 'Not enough historical data to create advanced predictions.'
            ];
        }

        return [
            'currentBalance' => $currentBalance,
            'expectedIncome' => $expectedIncome,
            'expectedExpenses' => $expectedExpenses,
            'expectedSavings' => $expectedSavings,
            'totalBudget' => $totalBudget,
            'forecast' => $forecast,
            'insights' => $insights,
        ];
    }
}