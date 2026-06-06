<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">

    <h2 class="fw-bold mb-1">
        Forecasting
    </h2>

    <p class="text-muted mb-0">
        Predict your future financial performance based on historical data.
    </p>

</div>

<!-- SUMMARY -->
<div class="row g-4">

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Projected Balance
                </small>

                <h2 class="fw-bold text-success mt-3">
                    ₱31,500
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
                    ₱40,000
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
                    ₱14,550
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
                    ₱16,950
                </h2>

            </div>

        </div>

    </div>

</div>

<!-- CHART -->
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

<!-- INSIGHTS -->
<div class="card border mt-4">

    <div class="card-body">

        <h5 class="fw-semibold mb-4">
            Forecast Insights
        </h5>

        <div class="alert alert-success mb-3">
            Your balance is projected to increase by 23% next month.
        </div>

        <div class="alert alert-warning mb-3">
            Food spending may exceed your budget within 2 weeks.
        </div>

        <div class="alert alert-info mb-0">
            Your savings goal can be achieved in approximately 3 months.
        </div>

    </div>

</div>

<!-- FORECAST TABLE -->
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

                    <tr>
                        <td>Jun</td>
                        <td>₱40,000</td>
                        <td>₱14,550</td>
                        <td class="fw-semibold text-success">
                            ₱31,500
                        </td>
                    </tr>

                    <tr>
                        <td>Jul</td>
                        <td>₱40,000</td>
                        <td>₱15,100</td>
                        <td class="fw-semibold text-success">
                            ₱56,400
                        </td>
                    </tr>

                    <tr>
                        <td>Aug</td>
                        <td>₱40,000</td>
                        <td>₱15,800</td>
                        <td class="fw-semibold text-success">
                            ₱80,600
                        </td>
                    </tr>

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
            labels: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug'
            ],
            datasets: [{
                label: 'Projected Balance',
                data: [
                    15000,
                    18000,
                    21000,
                    23500,
                    25450,
                    31500,
                    56400,
                    80600
                ],
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    }
);

</script>

<?= $this->endSection() ?>