<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BudgetModel;
use App\Models\TransactionModel;
use RuntimeException;

class ForecastingService extends BaseService
{
    private const ALPHA = 0.3;

    public function __construct(
        protected TransactionModel $transactionModel = new TransactionModel(),
        protected BudgetModel $budgetModel = new BudgetModel()
    ) {
    }

    public function generate(int $forecastMonths = 6): array
    {
        $userId = $this->userId();
        if (!$userId) {
            throw new RuntimeException('Unauthenticated: valid user ID required.');
        }

        $endDate = date('Y-m-d');

        $incomeSeries = $this->getMonthlySeries($userId, ['income'], $endDate);
        $expenseSeries = $this->getMonthlySeries($userId, ['expense', 'expenses'], $endDate);
        
        $totalBudget = $this->getTotalBudget($userId);
        $budgetByCategory = $this->getBudgetByCategory($userId);
        $expenseByCategory = $this->getExpensesByCategory($userId, $endDate);

        if (empty($incomeSeries) && empty($expenseSeries)) {
            return $this->emptyResponse($totalBudget);
        }

        $totalIncome = array_sum($incomeSeries);
        $totalExpenses = array_sum($expenseSeries);
        $currentBalance = round($totalIncome - $totalExpenses, 2);

        $allMonths = array_unique(
            array_merge(array_keys($incomeSeries), array_keys($expenseSeries))
        );
        sort($allMonths);

        $incomeValues = array_values(array_map(fn($m) => $incomeSeries[$m] ?? 0.0, $allMonths));
        $expenseValues = array_values(array_map(fn($m) => $expenseSeries[$m] ?? 0.0, $allMonths));

        $lrIncome = $this->linearRegressionForecast($incomeValues, $forecastMonths);
        $lrExpense = $this->linearRegressionForecast($expenseValues, $forecastMonths);

        $wmaIncome = $this->weightedMovingAverageForecast($incomeValues, $forecastMonths);
        $wmaExpense = $this->weightedMovingAverageForecast($expenseValues, $forecastMonths);

        $esIncome = $this->exponentialSmoothingForecast($incomeValues, $forecastMonths);
        $esExpense = $this->exponentialSmoothingForecast($expenseValues, $forecastMonths);

        $forecast = [];
        $runningBalanceMid = $currentBalance;

        $monthlyAvgExpenses = count($allMonths) > 0
            ? $totalExpenses / count($allMonths)
            : 0.0;

        for ($i = 0; $i < $forecastMonths; $i++) {
            $incomeEstimates = [$lrIncome[$i], $wmaIncome[$i], $esIncome[$i]];
            $expenseEstimates = [$lrExpense[$i], $wmaExpense[$i], $esExpense[$i]];

            $avgIncome = $this->mean($incomeEstimates);
            $avgExpense = $this->mean($expenseEstimates);

            $incomeStd = $this->stdDev($incomeEstimates);
            $expenseStd = $this->stdDev($expenseEstimates);

            $bestIncome = round($avgIncome + $incomeStd, 2);
            $worstIncome = round(max(0, $avgIncome - $incomeStd), 2);
            $bestExpense = round(max(0, $avgExpense - $expenseStd), 2);
            $worstExpense = round($avgExpense + $expenseStd, 2);

            $savingsMid = round($avgIncome - $avgExpense, 2);
            $savingsBest = round($bestIncome - $bestExpense, 2);
            $savingsWorst = round($worstIncome - $worstExpense, 2);

            $runningBalanceMid += $savingsMid;
            $runningBalanceBest = round($runningBalanceMid + ($savingsBest - $savingsMid), 2);
            $runningBalanceWorst = round($runningBalanceMid + ($savingsWorst - $savingsMid), 2);

            $scaleFactor = $monthlyAvgExpenses > 0 ? $avgExpense / $monthlyAvgExpenses : 1.0;
            $categoryForecast = $this->projectCategories($expenseByCategory, $scaleFactor, count($allMonths));

            $burnRate = $totalBudget > 0
                ? round(($avgExpense / $totalBudget) * 100, 1)
                : null;

            $forecast[] = [
                'month' => date('M Y', strtotime('+' . ($i + 1) . ' month')),
                'models' => [
                    'linear_regression' => [
                        'income' => round($lrIncome[$i], 2),
                        'expenses' => round($lrExpense[$i], 2),
                        'savings' => round($lrIncome[$i] - $lrExpense[$i], 2),
                    ],
                    'weighted_moving_average' => [
                        'income' => round($wmaIncome[$i], 2),
                        'expenses' => round($wmaExpense[$i], 2),
                        'savings' => round($wmaIncome[$i] - $wmaExpense[$i], 2),
                    ],
                    'exponential_smoothing' => [
                        'income' => round($esIncome[$i], 2),
                        'expenses' => round($esExpense[$i], 2),
                        'savings' => round($esIncome[$i] - $esExpense[$i], 2),
                    ],
                ],
                'ensemble' => [
                    'income' => round($avgIncome, 2),
                    'expenses' => round($avgExpense, 2),
                    'savings' => $savingsMid,
                ],
                'confidence' => [
                    'best' => [
                        'income' => $bestIncome,
                        'expenses' => $bestExpense,
                        'savings' => $savingsBest,
                        'balance' => $runningBalanceBest,
                    ],
                    'expected' => [
                        'income' => round($avgIncome, 2),
                        'expenses' => round($avgExpense, 2),
                        'savings' => $savingsMid,
                        'balance' => round($runningBalanceMid, 2),
                    ],
                    'worst' => [
                        'income' => $worstIncome,
                        'expenses' => $worstExpense,
                        'savings' => $savingsWorst,
                        'balance' => $runningBalanceWorst,
                    ],
                ],
                'categories' => $categoryForecast,
                'budget_burn_rate_pct' => $burnRate,
            ];
        }

        $savingsValues = array_map(
            fn($i) => $incomeValues[$i] - $expenseValues[$i],
            array_keys($incomeValues)
        );
        $monthlyAvgSavings = $this->mean($savingsValues);
        $savingsGoalProgress = $this->buildSavingsGoalProgress($currentBalance, $monthlyAvgSavings);

        $avgProjectedExpense = $this->mean(
            array_column(array_column($forecast, 'ensemble'), 'expenses')
        );

        $insights = $this->buildInsights(
            $monthlyAvgSavings,
            $totalBudget,
            $avgProjectedExpense,
            $incomeValues,
            $expenseValues
        );

        return [
            'currentBalance' => $currentBalance,
            'historicalMonths' => count($allMonths),
            'totalBudget' => $totalBudget,
            'budgetByCategory' => $budgetByCategory,
            'expenseByCategory' => $expenseByCategory,
            'savingsGoalProgress' => $savingsGoalProgress,
            'forecast' => $forecast,
            'insights' => $insights,
        ];
    }

    private function linearRegressionForecast(array $values, int $steps): array
    {
        $n = count($values);
        if ($n === 0) {
            return array_fill(0, $steps, 0.0);
        }
        if ($n === 1) {
            return array_fill(0, $steps, $values[0]);
        }

        $xMean = ($n - 1) / 2.0;
        $yMean = $this->mean($values);

        $numerator = 0.0;
        $denominator = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $numerator += ($i - $xMean) * ($values[$i] - $yMean);
            $denominator += ($i - $xMean) ** 2;
        }

        $slope = $denominator != 0 ? $numerator / $denominator : 0.0;
        $intercept = $yMean - $slope * $xMean;

        $result = [];
        for ($s = 1; $s <= $steps; $s++) {
            $result[] = max(0.0, $intercept + $slope * ($n - 1 + $s));
        }

        return $result;
    }

    private function weightedMovingAverageForecast(array $values, int $steps): array
    {
        $n = count($values);
        if ($n === 0) {
            return array_fill(0, $steps, 0.0);
        }

        $window = min(6, $n);
        $series = array_values($values);
        $result = [];

        for ($s = 0; $s < $steps; $s++) {
            $tail = array_slice($series, -$window);
            $w = range(1, count($tail));
            $wSum = array_sum($w);
            $point = 0.0;
            foreach ($tail as $idx => $val) {
                $point += $val * $w[$idx];
            }
            $point = max(0.0, $point / $wSum);
            $result[] = $point;
            $series[] = $point;
        }

        return $result;
    }

    private function exponentialSmoothingForecast(array $values, int $steps): array
    {
        $n = count($values);
        if ($n === 0) {
            return array_fill(0, $steps, 0.0);
        }
        if ($n === 1) {
            return array_fill(0, $steps, $values[0]);
        }

        $alpha = self::ALPHA;
        $beta = $alpha / 2.0;

        $level = $values[0];
        $trend = $values[1] - $values[0];

        for ($i = 1; $i < $n; $i++) {
            $prevLevel = $level;
            $level = $alpha * $values[$i] + (1 - $alpha) * ($level + $trend);
            $trend = $beta * ($level - $prevLevel) + (1 - $beta) * $trend;
        }

        $result = [];
        for ($s = 1; $s <= $steps; $s++) {
            $result[] = max(0.0, round($level + $s * $trend, 2));
        }

        return $result;
    }


    private function getMonthlySeries(string|int $userId, array $types, string $endDate): array
    {
        $db = \Config\Database::connect();
        $placeholders = implode(',', array_fill(0, count($types), '?'));

        $sql = "SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS month,
                       COALESCE(SUM(amount), 0) AS total
                FROM transactions
                WHERE user_id = ?
                  AND transaction_type IN ({$placeholders})
                  AND transaction_date <= ?
                GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
                ORDER BY month ASC";

        $bindings = array_merge([$userId], $types, [$endDate]);
        $rows = $db->query($sql, $bindings)->getResultArray();

        $series = [];
        foreach ($rows as $row) {
            $series[$row['month']] = (float) $row['total'];
        }

        return $series;
    }

    private function getTotalBudget(string|int $userId): float
    {
        $db = \Config\Database::connect();
        $sql = "SELECT COALESCE(SUM(limit_amount), 0) AS total
                FROM budgets
                WHERE user_id = ?";

        $row = $db->query($sql, [$userId])->getRowArray();

        return (float) ($row['total'] ?? 0);
    }

    private function getBudgetByCategory(string|int $userId): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT c.category_name, COALESCE(SUM(b.limit_amount), 0) AS total
                FROM budgets b
                LEFT JOIN categories c ON c.id = b.category_id
                WHERE b.user_id = ?
                GROUP BY b.category_id, c.category_name";

        $rows = $db->query($sql, [$userId])->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $name = $row['category_name'] ?? 'Uncategorized';
            $result[$name] = (float) $row['total'];
        }

        return $result;
    }

    private function getExpensesByCategory(string|int $userId, string $endDate): array
    {
        $db = \Config\Database::connect();
        $sql = "SELECT c.category_name, COALESCE(SUM(t.amount), 0) AS total
                FROM transactions t
                LEFT JOIN categories c ON c.id = t.category_id
                WHERE t.user_id = ?
                  AND t.transaction_type IN ('expense', 'expenses')
                  AND t.transaction_date <= ?
                GROUP BY t.category_id, c.category_name";

        $rows = $db->query($sql, [$userId, $endDate])->getResultArray();

        $result = [];
        foreach ($rows as $row) {
            $name = $row['category_name'] ?? 'Uncategorized';
            $result[$name] = (float) $row['total'];
        }

        return $result;
    }



    private function projectCategories(
        array $expenseByCategory,
        float $scaleFactor,
        int $historyMonths
    ): array {
        $projected = [];
        foreach ($expenseByCategory as $category => $historicalTotal) {
            $monthlyAvg = $historyMonths > 0 ? $historicalTotal / $historyMonths : $historicalTotal;
            $projected[$category] = round($monthlyAvg * $scaleFactor, 2);
        }

        return $projected;
    }

    private function buildSavingsGoalProgress(
        float $currentBalance,
        float $monthlyAvgSavings
    ): array {
        $milestones = [1_000, 5_000, 10_000, 25_000, 50_000, 100_000];
        $progress = [];

        foreach ($milestones as $goal) {
            if ($currentBalance >= $goal) {
                $progress[] = [
                    'goal' => $goal,
                    'status' => 'achieved',
                    'months_to_goal' => 0,
                    'eta' => null,
                ];
                continue;
            }

            $remaining = $goal - $currentBalance;

            if ($monthlyAvgSavings > 0) {
                $monthsNeeded = (int) ceil($remaining / $monthlyAvgSavings);
                $progress[] = [
                    'goal' => $goal,
                    'status' => 'in_progress',
                    'months_to_goal' => $monthsNeeded,
                    'eta' => date('M Y', strtotime("+{$monthsNeeded} month")),
                ];
            } else {
                $progress[] = [
                    'goal' => $goal,
                    'status' => 'unreachable',
                    'months_to_goal' => null,
                    'eta' => null,
                ];
            }
        }

        return $progress;
    }

    private function buildInsights(
        float $monthlyAvgSavings,
        float $totalBudget,
        float $avgProjectedExpense,
        array $incomeValues,
        array $expenseValues
    ): array {
        $insights = [];

        if ($monthlyAvgSavings > 0) {
            $insights[] = ['type' => 'success', 'message' => 'Your savings trend is positive.'];
        } elseif ($monthlyAvgSavings < 0) {
            $insights[] = ['type' => 'danger', 'message' => 'Projected expenses exceed income.'];
        }

        if ($totalBudget > 0 && $avgProjectedExpense > $totalBudget) {
            $insights[] = ['type' => 'warning', 'message' => 'Projected expenses may exceed your total budget.'];
        }

        if (count($incomeValues) >= 3) {
            $cv = $this->coefficientOfVariation($incomeValues);
            if ($cv > 0.3) {
                $insights[] = [
                    'type' => 'warning',
                    'message' => 'Your income is highly variable. Forecasts carry higher uncertainty.',
                ];
            }
        }

        if (count($expenseValues) >= 3) {
            $projected = $this->linearRegressionForecast($expenseValues, 1);
            if ($projected[0] > $this->mean($expenseValues) * 1.1) {
                $insights[] = [
                    'type' => 'warning',
                    'message' => 'Your expenses show an upward trend.',
                ];
            }
        }

        if (empty($insights)) {
            $insights[] = [
                'type' => 'info',
                'message' => 'Not enough historical data to create advanced predictions.',
            ];
        }

        return $insights;
    }

    private function mean(array $values): float
    {
        $n = count($values);
        return $n > 0 ? array_sum($values) / $n : 0.0;
    }

    private function stdDev(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }
        $mean = $this->mean($values);
        $variance = array_sum(array_map(fn($v) => ($v - $mean) ** 2, $values)) / $n;

        return sqrt($variance);
    }

    private function coefficientOfVariation(array $values): float
    {
        $mean = $this->mean($values);
        return $mean != 0 ? $this->stdDev($values) / $mean : 0.0;
    }

    private function emptyResponse(float $totalBudget): array
    {
        return [
            'currentBalance' => 0.0,
            'historicalMonths' => 0,
            'totalBudget' => $totalBudget,
            'budgetByCategory' => [],
            'expenseByCategory' => [],
            'savingsGoalProgress' => [],
            'forecast' => [],
            'insights' => [
                [
                    'type' => 'info',
                    'message' => 'No transaction data found. Start adding income and expenses to generate a forecast.',
                ],
            ],
        ];
    }
}