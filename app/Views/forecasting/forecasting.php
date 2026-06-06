<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$labels = [];
$balances = [];

foreach ($forecast as $item) {
    $labels[] = $item['month'];
    $balances[] = $item['balance'];
}

?>

<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Forecasting
    </h2>

    <p class="text-muted mb-0">
        Predict your future financial performance based on historical data.
    </p>

</div>

<div class="row g-4">

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Projected Balance
                </small>

                <h2 class="fw-bold text-success mt-3">
                    ₱<?= number_format($currentBalance, 2) ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Expected Income
                </small>

                <h2 class="fw-bold mt-3">
                    ₱<?= number_format($expectedIncome, 2) ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Expected Expenses
                </small>

                <h2 class="fw-bold text-danger mt-3">
                    ₱<?= number_format($expectedExpenses, 2) ?>
                </h2>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Expected Savings
                </small>

                <h2 class="fw-bold text-primary mt-3">
                    ₱<?= number_format($expectedSavings, 2) ?>
                </h2>

            </div>

        </div>

    </div>

</div>

<div class="card border mt-4">

    <div class="card-body">

        <div class="d-flex justify-content-between mb-4">

            <div>

                <h5 class="fw-semibold mb-1">
                    6-Month Forecast
                </h5>

                <small class="text-muted">
                    Projected balance trend
                </small>

            </div>

            <span class="badge text-bg-success">
                Forecast Active
            </span>

        </div>

        <div style="height:400px;">
            <canvas id="forecastChart"></canvas>
        </div>

    </div>

</div>

<div class="card border mt-4">

    <div class="card-body">

        <h5 class="fw-semibold mb-4">
            Forecast Insights
        </h5>

        <?php foreach ($insights as $insight): ?>

            <div class="alert alert-<?= esc($insight['type']) ?> mb-3">
                <?= esc($insight['message']) ?>
            </div>

        <?php endforeach; ?>

    </div>

</div>

<div class="card border mt-4">

    <div class="card-body">

        <h5 class="fw-semibold mb-4">
            Forecast Breakdown
        </h5>

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>
                        <th>Month</th>
                        <th>Income</th>
                        <th>Expenses</th>
                        <th>Projected Balance</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($forecast as $row): ?>

                        <tr>

                            <td>
                                <?= esc($row['month']) ?>
                            </td>

                            <td>
                                ₱<?= number_format($row['income'], 2) ?>
                            </td>

                            <td>
                                ₱<?= number_format($row['expenses'], 2) ?>
                            </td>

                            <td class="fw-semibold text-success">
                                ₱<?= number_format($row['balance'], 2) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(
    document.getElementById('forecastChart'),
    {
        type: 'line',
        data: {
            labels: <?= json_encode(
                $labels,
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_QUOT |
                JSON_HEX_AMP
            ) ?>,
            datasets: [{
                label: 'Projected Balance',
                data: <?= json_encode(
                    $balances,
                    JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT |
                    JSON_HEX_AMP
                ) ?>,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    }
);

</script>

<?= $this->endSection() ?>