<?php

use function GuzzleHttp\json_encode;
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Reports
        </h2>

        <p class="text-muted mb-0">
            Analyze your financial performance and spending habits
        </p>

    </div>

    <div class="dropdown">
        <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-download me-2"></i>Export Report
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= site_url('reports/export') ?>?format=pdf
    &month=<?= esc($filters['month'] ?? '') ?>
    &category=<?= esc($filters['category'] ?? '') ?>
    &category_name=<?= esc($filters['category'] === 'all' || empty($filters['category']) ? 'All' : collect($categories)->firstWhere('id', $filters['category'])['name'] ?? 'All') ?>">
                    <i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Export as PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= site_url('reports/export') ?>?format=xlsx
                         &month=<?= esc($filters['month'] ?? '') ?>
                         &category=<?= esc($filters['category'] ?? '') ?>
                         &category_name=<?= esc($filters['category'] === 'all' || empty($filters['category']) ? 'All' : collect($categories)->firstWhere('id', $filters['category'])['name'] ?? 'All') ?>">
                    <i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Export as Excel
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- FILTER -->
<form method="get" action="<?= site_url('reports') ?>">

    <div class="card border mb-4">

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <input
                        type="month"
                        name="month"
                        value="<?= esc($filters['month'] ?? '') ?>"
                        class="form-control">

                </div>

                <div class="col-md-4">

                    <select name="category" class="form-select">

                        <option value="all">All Categories</option>

                        <?php foreach ($categories as $cat): ?>
                            <option
                                value="<?= esc($cat['id']) ?>"
                                <?= ($filters['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc(ucfirst($cat['category_name'])) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-4">

                    <button type="submit" class="btn btn-primary w-100">
                        Generate Report
                    </button>

                </div>

            </div>

        </div>

    </div>

</form>
<!-- KPI CARDS -->
<div class="row g-4">

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Total Income
                </small>

                <h3 class="fw-bold text-success mt-2">
                    ₱<?= number_format($summary['totalIncome'] ?? 0, 2) ?>
                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Total Expenses
                </small>

                <h3 class="fw-bold text-danger mt-2">
                    ₱<?= number_format($summary['totalExpenses'] ?? 0, 2) ?>
                </h3>


            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Net Savings
                </small>

                <h3 class="fw-bold text-primary mt-2">
                    ₱<?= number_format($summary['netSavings'] ?? 0, 2) ?>
                </h3>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100">

            <div class="card-body">

                <small class="text-muted">
                    Savings Rate
                </small>

                <h3 class="fw-bold mt-2"><?= esc($summary['savingsRate'] ?? 0) ?>%</h3>

            </div>

        </div>

    </div>

</div>

<!-- CHARTS -->
<div class="row g-4 mt-1">

    <div class="col-lg-8">

        <div class="card border">

            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Income vs Expenses
                </h5>

                <div style="height:350px;">
                    <canvas id="incomeExpenseChart"></canvas>
                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card border">

            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Spending by Category
                </h5>

                <div style="height:350px;">
                    <canvas id="categoryChart"></canvas>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- TOP SPENDING -->
<div class="card border mt-4">

    <div class="card-body">

        <h5 class="fw-semibold mb-4">
            Top Spending Categories
        </h5>

        <?php if (!empty($topSpending)): ?>
            <?php foreach ($topSpending as $i => $item): ?>
                <div class="d-flex justify-content-between py-3 <?= $i < count($topSpending) - 1 ? 'border-bottom' : '' ?>">
                    <span><?= esc(ucfirst($item['category'])) ?></span>
                    <strong>₱<?= number_format($item['total'], 2) ?></strong>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <p class="text-muted mb-0">No expense data found.</p>
        <?php endif; ?>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const monthlyData = <?= json_encode($chartMonthly ?? ['labels' => [], 'income' => [], 'expenses' => []], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    const categoryData = <?= json_encode($chartCategory ?? ['labels' => [], 'data' => []], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    new Chart(document.getElementById('incomeExpenseChart'), {
        type: 'bar',
        data: {
            labels: monthlyData.labels,
            datasets: [{
                    label: 'Income',
                    data: monthlyData.income
                },
                {
                    label: 'Expenses',
                    data: monthlyData.expenses
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: categoryData.labels,
            datasets: [{
                data: categoryData.data
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>

<?= $this->endSection() ?>