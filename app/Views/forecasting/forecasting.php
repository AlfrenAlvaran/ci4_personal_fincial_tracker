<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$labels = [];
$bestBalances = [];
$midBalances = [];
$worstBalances = [];


$firstMonth = $forecast[0] ?? null;
$summaryIncome = $firstMonth['ensemble']['income'] ?? 0;
$summaryExpenses = $firstMonth['ensemble']['expenses'] ?? 0;
$summarySavings = $firstMonth['ensemble']['savings'] ?? 0;

var_dump($summaryExpenses);


if (!empty($forecast)) {
    foreach ($forecast as $item) {
        $labels[] = $item['month'];
        $bestBalances[] = $item['confidence']['best']['balance'];
        $midBalances[] = $item['confidence']['expected']['balance'];
        $worstBalances[] = $item['confidence']['worst']['balance'];
    }
}
?>

<div class="mb-4">
    <h2 class="fw-bold mb-1">Forecasting</h2>
    <p class="text-muted mb-0">
        Predict your future financial performance based on historical data.
        <span class="badge text-bg-secondary ms-1"><?= $historicalMonths ?? 0 ?> months of history</span>
    </p>
</div>

<div class="row g-4">

    <div class="col-md-6 col-xl-3">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Current Balance</small>
                <h2 class="fw-bold text-success mt-3">
                    ₱<?= number_format($currentBalance ?? 0, 2) ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Expected Monthly Income</small>
                <h2 class="fw-bold mt-3">
                    ₱<?= number_format($summaryIncome, 2) ?>
                </h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Expected Monthly Expenses</small>
                <h2 class="fw-bold text-danger mt-3">
                    ₱<?= number_format($summaryExpenses, 2) ?>

                </h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Expected Monthly Savings</small>
                <h2 class="fw-bold text-primary mt-3">
                    ₱<?= number_format($summarySavings, 2) ?>
                </h2>
            </div>
        </div>
    </div>

</div>

<div class="card border mt-4">
    <div class="card-body">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h5 class="fw-semibold mb-1">6-Month Balance Forecast</h5>
                <small class="text-muted">Best / Expected / Worst scenario</small>
            </div>
            <span class="badge text-bg-success">Forecast Active</span>
        </div>

        <div style="height:400px;">
            <canvas id="forecastChart"></canvas>
        </div>

    </div>
</div>

<div class="card border mt-4">
    <div class="card-body">

        <h5 class="fw-semibold mb-1">Model Comparison — Projected Expenses</h5>
        <small class="text-muted d-block mb-4">
            Linear Regression vs Weighted Moving Average vs Exponential Smoothing
        </small>

        <div style="height:320px;">
            <canvas id="modelChart"></canvas>
        </div>

    </div>
</div>

<div class="card border mt-4">
    <div class="card-body">

        <h5 class="fw-semibold mb-4">Forecast Insights</h5>

        <?php if (!empty($insights)): ?>
            <?php foreach ($insights as $insight): ?>
                <div class="alert alert-<?= esc($insight['type']) ?> mb-3">
                    <?= esc($insight['message']) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>


    </div>
</div>

<?php if (!empty($savingsGoalProgress)): ?>
    <div class="card border mt-4">
        <div class="card-body">

            <h5 class="fw-semibold mb-4">Savings Goal Progress</h5>

            <div class="row g-3">
                <?php foreach ($savingsGoalProgress as $goal): ?>

                    <div class="col-md-4">
                        <div class="card border h-100">
                            <div class="card-body">

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-semibold">₱<?= number_format($goal['goal']) ?></span>
                                    <?php if ($goal['status'] === 'achieved'): ?>
                                        <span class="badge text-bg-success">Achieved ✓</span>
                                    <?php elseif ($goal['status'] === 'in_progress'): ?>
                                        <span class="badge text-bg-primary">In Progress</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-danger">Unreachable</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($goal['status'] === 'in_progress'): ?>
                                    <small class="text-muted">
                                        ETA: <strong><?= esc($goal['eta']) ?></strong>
                                        (<?= $goal['months_to_goal'] ?> months)
                                    </small>
                                <?php elseif ($goal['status'] === 'unreachable'): ?>
                                    <small class="text-muted">
                                        Increase your monthly savings to reach this goal.
                                    </small>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

        </div>
    </div>
<?php endif; ?>


<?php if (!empty($expenseByCategory)): ?>
    <div class="card border mt-4">
        <div class="card-body">

            <h5 class="fw-semibold mb-4">Category Spending vs Budget</h5>

            <?php foreach ($expenseByCategory as $category => $spent):
                $budget = 0;
                if (!empty($budgetByCategory)) {
                    foreach ($budgetByCategory as $budgetKey => $budgetVal) {
                        if (strtolower($budgetKey) === strtolower($category)) {
                            $budget = $budgetVal;
                            break;
                        }
                    }
                }
                $pct = $budget > 0 ? min(100, round(($spent / $budget) * 100)) : null;
                $barClass = $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success');
            ?>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span><?= esc(ucfirst($category)) ?></span>
                        <small class="text-muted">
                            ₱<?= number_format($spent, 2) ?>
                            <?php if ($budget > 0): ?>
                                / ₱<?= number_format($budget, 2) ?>
                                (<?= $pct ?>%)
                            <?php else: ?>
                                <span class="text-warning">No budget set</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <?php if ($pct !== null): ?>
                        <div class="progress" style="height:8px;">
                            <div class="progress-bar <?= $barClass ?>" style="width:<?= $pct ?>%" role="progressbar"
                                aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endforeach; ?>

        </div>
    </div>
<?php endif; ?>


<div class="card border mt-4">
    <div class="card-body">

        <h5 class="fw-semibold mb-4">Forecast Breakdown</h5>

        <div class="table-responsive">
            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Income <small class="text-muted fw-normal">(ensemble)</small></th>
                        <th>Expenses <small class="text-muted fw-normal">(ensemble)</small></th>
                        <th>Savings</th>
                        <th>Best Balance</th>
                        <th>Expected Balance</th>
                        <th>Worst Balance</th>
                        <th>Budget Burn</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($forecast)): ?>
                        <?php foreach ($forecast as $row): ?>
                            <tr>
                                <td><?= esc($row['month']) ?></td>
                                <td>₱<?= number_format($row['ensemble']['income'], 2) ?></td>
                                <td>₱<?= number_format($row['ensemble']['expenses'], 2) ?></td>
                                <td
                                    class="<?= $row['ensemble']['savings'] >= 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                                    ₱<?= number_format($row['ensemble']['savings'], 2) ?>
                                </td>
                                <td class="text-success">
                                    ₱<?= number_format($row['confidence']['best']['balance'], 2) ?>
                                </td>
                                <td class="fw-semibold">
                                    ₱<?= number_format($row['confidence']['expected']['balance'], 2) ?>
                                </td>
                                <td class="text-danger">
                                    ₱<?= number_format($row['confidence']['worst']['balance'], 2) ?>
                                </td>
                                <td>
                                    <?php if ($row['budget_burn_rate_pct'] !== null): ?>
                                        <span
                                            class="badge text-bg-<?= $row['budget_burn_rate_pct'] >= 100 ? 'danger' : ($row['budget_burn_rate_pct'] >= 80 ? 'warning' : 'success') ?>">
                                            <?= $row['budget_burn_rate_pct'] ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<div class="card border mt-4 mb-4">
    <div class="card-body">

        <h5 class="fw-semibold mb-4">Per-Model Predictions</h5>

        <div class="table-responsive">
            <table class="table align-middle table-sm">

                <thead>
                    <tr>
                        <th>Month</th>
                        <th colspan="3" class="text-center border-start">Linear Regression</th>
                        <th colspan="3" class="text-center border-start">Weighted Moving Avg</th>
                        <th colspan="3" class="text-center border-start">Exponential Smoothing</th>
                    </tr>
                    <tr class="table-light">
                        <th></th>
                        <th class="border-start">Income</th>
                        <th>Expenses</th>
                        <th>Savings</th>
                        <th class="border-start">Income</th>
                        <th>Expenses</th>
                        <th>Savings</th>
                        <th class="border-start">Income</th>
                        <th>Expenses</th>
                        <th>Savings</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($forecast)): ?>
                        <?php ?>
                        <?php foreach ($forecast as $row):
                            $lr = $row['models']['linear_regression'];
                            $wma = $row['models']['weighted_moving_average'];
                            $es = $row['models']['exponential_smoothing'];
                        ?>
                            <tr>
                                <td><?= esc($row['month']) ?></td>

                                <td class="border-start">₱<?= number_format($lr['income'], 2) ?></td>
                                <td>₱<?= number_format($lr['expenses'], 2) ?></td>
                                <td class="<?= $lr['savings'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    ₱<?= number_format($lr['savings'], 2) ?>
                                </td>

                                <td class="border-start">₱<?= number_format($wma['income'], 2) ?></td>
                                <td>₱<?= number_format($wma['expenses'], 2) ?></td>
                                <td class="<?= $wma['savings'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    ₱<?= number_format($wma['savings'], 2) ?>
                                </td>

                                <td class="border-start">₱<?= number_format($es['income'], 2) ?></td>
                                <td>₱<?= number_format($es['expenses'], 2) ?></td>
                                <td class="<?= $es['savings'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    ₱<?= number_format($es['savings'], 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels = <?= json_encode($labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const bestBalances = <?= json_encode($bestBalances, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const midBalances = <?= json_encode($midBalances, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const worstBalances = <?= json_encode($worstBalances, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;


    new Chart(document.getElementById('forecastChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                    label: 'Best Case',
                    data: bestBalances,
                    borderColor: 'rgba(25,135,84,0.8)',
                    backgroundColor: 'rgba(25,135,84,0.08)',
                    borderDash: [5, 4],
                    tension: 0.4,
                    fill: false,
                    pointRadius: 3,
                },
                {
                    label: 'Expected',
                    data: midBalances,
                    borderColor: 'rgba(13,110,253,1)',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 2.5,
                    pointRadius: 4,
                },
                {
                    label: 'Worst Case',
                    data: worstBalances,
                    borderColor: 'rgba(220,53,69,0.8)',
                    backgroundColor: 'rgba(220,53,69,0.08)',
                    borderDash: [5, 4],
                    tension: 0.4,
                    fill: false,
                    pointRadius: 3,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: v => '₱' + v.toLocaleString()
                    }
                }
            }
        }
    });


    <?php
    $lrExpenses = array_map(fn($r) => $r['models']['linear_regression']['expenses'], $forecast);
    $wmaExpenses = array_map(fn($r) => $r['models']['weighted_moving_average']['expenses'], $forecast);
    $esExpenses = array_map(fn($r) => $r['models']['exponential_smoothing']['expenses'], $forecast);
    ?>
    new Chart(document.getElementById('modelChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                    label: 'Linear Regression',
                    data: <?= json_encode($lrExpenses, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    backgroundColor: 'rgba(13,110,253,0.7)',
                },
                {
                    label: 'Weighted Moving Avg',
                    data: <?= json_encode($wmaExpenses, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    backgroundColor: 'rgba(255,193,7,0.7)',
                },
                {
                    label: 'Exponential Smoothing',
                    data: <?= json_encode($esExpenses, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    backgroundColor: 'rgba(220,53,69,0.7)',
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: v => '₱' + v.toLocaleString()
                    }
                }
            }
        }
    });
</script>

<?= $this->endSection() ?>