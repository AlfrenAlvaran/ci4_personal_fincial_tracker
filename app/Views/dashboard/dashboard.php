<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$labels = [];
$balances = [];

foreach (($forecast ?? []) as $item) {
    $labels[] = $item['month'] ?? '';

    $balances[] = $item['confidence']['expected']['balance'] ?? 0;
}


?>



<div class="mb-5">

    <h2 class="fw-bold mb-1">
        Good Morning, <?= esc(session('username')) ?> 👋
    </h2>

    <p class="text-muted mb-0">
        Here's an overview of your finances and projected growth.
    </p>

</div>

<div class="row g-4">

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">Total Balance</small>

                <h2 class="fw-bold mt-3 mb-1">
                    ₱<?= number_format($totalBalance ?? 0, 2) ?>
                </h2>

                <small class="text-success">
                    Financial overview
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">Monthly Income</small>

                <h2 class="fw-bold text-success mt-3 mb-1">
                    ₱<?= number_format($totalIncome ?? 0, 2) ?>
                </h2>

                <small class="text-muted">Current month</small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">Monthly Expenses</small>

                <h2 class="fw-bold text-danger mt-3 mb-1">
                    ₱<?= number_format($monthlyExpenses ?? 0, 2) ?>
                </h2>

                <small class="text-muted">Current month</small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">Net Savings</small>

                <h2 class="fw-bold text-primary mt-3 mb-1">
                    ₱<?= number_format($netSavings ?? 0, 2) ?>
                </h2>

                <small class="text-muted">Available savings</small>

            </div>

        </div>

    </div>

</div>

<div class="card border mt-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h5 class="fw-semibold mb-1">Financial Forecast</h5>

                <small class="text-muted">
                    Projected balance over the next 6 months
                </small>

            </div>

            <span class="badge text-bg-success">Forecast Active</span>

        </div>

        <div class="row mb-4">

            <div class="col-md-4">

                <div class="border rounded p-3">

                    <small class="text-muted">Forecasted Balance</small>

                    <h3 class="fw-bold text-success mt-2 mb-0">
                        ₱<?= number_format($currentBalance ?? 0, 2) ?>
                    </h3>

                </div>

            </div>

            <div class="col-md-4">

                <div class="border rounded p-3">

                    <small class="text-muted">Expected Income</small>

                    <h3 class="fw-bold mt-2 mb-0">
                        ₱<?= number_format($expectedIncome ?? 0, 2) ?>
                    </h3>

                </div>

            </div>

            <div class="col-md-4">

                <div class="border rounded p-3">

                    <small class="text-muted">Expected Expenses</small>

                    <h3 class="fw-bold mt-2 mb-0">
                        ₱<?= number_format($expectedExpenses ?? 0, 2) ?>
                    </h3>

                </div>

            </div>

        </div>

        <div style="height:400px;">
            <canvas id="forecastChart"></canvas>
        </div>

    </div>

</div>

<div class="card border mt-4">

    <div class="card-body">

        <h5 class="fw-semibold mb-4">Recent Activity</h5>

        <?php if (!empty($recentTransactions)): ?>


            <?php foreach ($recentTransactions as $t): ?>

                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

                    <div>
                        <div class="fw-medium">
                            <?= esc($t['notes'] ?? 'Transaction') ?>
                        </div>
                        <small class="text-muted d-block">
                            Ref #: <?= esc($t['reference_number'] ?? 'N/A') ?>
                        </small>
                        <small class="text-muted">
                            <?= date('M d, Y', strtotime($t['transaction_date'])) ?>
                        </small>

                    </div>

                    <div class="fw-semibold <?= $t['transaction_type'] === 'income' ? 'text-success' : 'text-danger' ?>">

                        <?= $t['transaction_type'] === 'income' ? '+' : '-' ?>
                        ₱<?= number_format($t['amount'], 2) ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="text-muted text-center py-4">
                No recent transactions found.
            </div>

        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    new Chart(
        document.getElementById('forecastChart'), {
            type: 'line',
            data: {
                labels: <?= json_encode($labels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                datasets: [{
                    label: 'Projected Balance',
                    data: <?= json_encode($balances, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        }
    );
</script>

<?= $this->endSection() ?>