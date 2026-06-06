<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="mb-5">

    <h2 class="fw-bold mb-1">
        Good Morning, <?= esc(session('username')) ?> 👋
    </h2>

    <p class="text-muted mb-0">
        Here's an overview of your finances and projected growth.
    </p>

</div>

<!-- SUMMARY CARDS -->
<div class="row g-4">

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Total Balance
                </small>

                <h2 class="fw-bold mt-3 mb-1">
                    ₱25,450
                </h2>

                <small class="text-success">
                    ↑ 12% from last month
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Monthly Income
                </small>

                <h2 class="fw-bold text-success mt-3 mb-1">
                    ₱<?= number_format($totalIncome ?? 0 , 2) ?>
                </h2>

                <small class="text-muted">
                    Current month
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Monthly Expenses
                </small>

                <h2 class="fw-bold text-danger mt-3 mb-1">
                    ₱ <?= number_format($monthlyExpenses ?? 0, 2) ?>
                </h2>

                <small class="text-muted">
                    Current month
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Net Savings
                </small>

                <h2 class="fw-bold text-primary mt-3 mb-1">
                    ₱10,900
                </h2>

                <small class="text-muted">
                    Available savings
                </small>

            </div>

        </div>

    </div>

</div>

<!-- FORECAST SECTION -->
<div class="card border mt-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h5 class="fw-semibold mb-1">
                    Financial Forecast
                </h5>

                <small class="text-muted">
                    Projected balance over the next 6 months
                </small>

            </div>

            <span class="badge text-bg-success">
                Forecast Active
            </span>

        </div>

        <div class="row mb-4">

            <div class="col-md-4">

                <div class="border rounded p-3">

                    <small class="text-muted">
                        Forecasted Balance
                    </small>

                    <h3 class="fw-bold text-success mt-2 mb-0">
                        ₱31,500
                    </h3>

                </div>

            </div>

            <div class="col-md-4">

                <div class="border rounded p-3">

                    <small class="text-muted">
                        Expected Income
                    </small>

                    <h3 class="fw-bold mt-2 mb-0">
                        ₱40,000
                    </h3>

                </div>

            </div>

            <div class="col-md-4">

                <div class="border rounded p-3">

                    <small class="text-muted">
                        Expected Expenses
                    </small>

                    <h3 class="fw-bold mt-2 mb-0">
                        ₱14,550
                    </h3>

                </div>

            </div>

        </div>

        <div style="height:400px;">

            <canvas id="forecastChart"></canvas>

        </div>

    </div>

</div>

<!-- RECENT ACTIVITY -->
<div class="card border mt-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h5 class="fw-semibold mb-0">
                Recent Activity
            </h5>

            <a href="<?= site_url('transactions') ?>" class="text-decoration-none">

                View All

            </a>

        </div>

        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

            <div>

                <div class="fw-medium">
                    Monthly Salary
                </div>

                <small class="text-muted">
                    May 29, 2026
                </small>

            </div>

            <div class="fw-semibold text-success">
                + ₱20,000
            </div>

        </div>

        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

            <div>

                <div class="fw-medium">
                    Lunch
                </div>

                <small class="text-muted">
                    May 30, 2026
                </small>

            </div>

            <div class="fw-semibold text-danger">
                - ₱250
            </div>

        </div>

        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">

            <div>

                <div class="fw-medium">
                    Transportation
                </div>

                <small class="text-muted">
                    May 30, 2026
                </small>

            </div>

            <div class="fw-semibold text-danger">
                - ₱180
            </div>

        </div>

        <div class="d-flex justify-content-between align-items-center py-3">

            <div>

                <div class="fw-medium">
                    Savings Deposit
                </div>

                <small class="text-muted">
                    May 27, 2026
                </small>

            </div>

            <div class="fw-semibold text-primary">
                + ₱5,000
            </div>

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
                labels: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun Forecast'
                ],
                datasets: [{
                    label: 'Projected Balance',
                    data: [
                        15000,
                        18000,
                        21000,
                        23500,
                        25450,
                        31500
                    ],
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