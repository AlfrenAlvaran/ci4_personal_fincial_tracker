<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$labels = [];
$balances = [];

foreach (($forecast ?? []) as $item) {
    $labels[] = $item['month'] ?? '';
    $balances[] = $item['confidence']['expected']['balance'] ?? 0;
}

$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');

?>

<div class="mb-5">

    <h2 class="fw-bold mb-1">
        <?= esc($greeting) ?>, <?= esc(session('username')) ?> 👋
    </h2>

    <p class="text-muted mb-0">
        Here's an overview of your finances and projected growth.
    </p>

</div>

<div class="row g-4">

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100 stat-card">

            <div class="card-body">

                <small class="text-muted">Total Balance</small>

                <h2 class="fw-bold mt-3 mb-1">
                    ₱<span class="counter" data-target="<?= esc($totalBalance ?? 0) ?>" data-decimals="2">0.00</span>
                </h2>

                <small class="text-success">
                    Financial overview
                </small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100 stat-card">

            <div class="card-body">

                <small class="text-muted">Monthly Income</small>

                <h2 class="fw-bold text-success mt-3 mb-1">
                    ₱<span class="counter" data-target="<?= esc($totalIncome ?? 0) ?>" data-decimals="2">0.00</span>
                </h2>

                <small class="text-muted">Current month</small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100 stat-card">

            <div class="card-body">

                <small class="text-muted">Monthly Expenses</small>

                <h2 class="fw-bold text-danger mt-3 mb-1">
                    ₱<span class="counter" data-target="<?= esc($monthlyExpenses ?? 0) ?>" data-decimals="2">0.00</span>
                </h2>

                <small class="text-muted">Current month</small>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-xl-3">

        <div class="card border h-100 stat-card">

            <div class="card-body">

                <small class="text-muted">Net Savings</small>

                <h2 class="fw-bold text-primary mt-3 mb-1">
                    ₱<span class="counter" data-target="<?= esc($netSavings ?? 0) ?>" data-decimals="2">0.00</span>
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

                <div class="border rounded p-3 stat-card">

                    <small class="text-muted">Forecasted Balance</small>

                    <h3 class="fw-bold text-success mt-2 mb-0">
                        ₱<span class="counter" data-target="<?= esc($currentBalance ?? 0) ?>" data-decimals="2">0.00</span>
                    </h3>

                </div>

            </div>

            <div class="col-md-4">

                <div class="border rounded p-3 stat-card">

                    <small class="text-muted">Expected Income</small>

                    <h3 class="fw-bold mt-2 mb-0">
                        ₱<span class="counter" data-target="<?= esc($expectedIncome ?? 0) ?>" data-decimals="2">0.00</span>
                    </h3>

                </div>

            </div>

            <div class="col-md-4">

                <div class="border rounded p-3 stat-card">

                    <small class="text-muted">Expected Expenses</small>

                    <h3 class="fw-bold mt-2 mb-0">
                        ₱<span class="counter" data-target="<?= esc($expectedExpenses ?? 0) ?>" data-decimals="2">0.00</span>
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

                <div class="d-flex justify-content-between align-items-center py-3 border-bottom transaction-row">

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

<style>
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: default;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .transaction-row {
        transition: background-color 0.15s ease;
    }

    .transaction-row:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .counter {
        display: inline-block;
    }
</style>

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
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.parsed.y ?? 0;
                                return '₱' + value.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '₱' + value.toLocaleString('en-US');
                            }
                        }
                    }
                }
            }
        }
    );

    function animateCounter(el) {
        const target = parseFloat(el.dataset.target) || 0;
        const decimals = parseInt(el.dataset.decimals ?? '0', 10);
        const duration = 1000;
        const start = performance.now();
        const startValue = 0;

        function easeOutQuad(t) {
            return t * (2 - t);
        }

        function frame(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeOutQuad(progress);
            const current = startValue + (target - startValue) * eased;

            el.textContent = current.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });

            if (progress < 1) {
                requestAnimationFrame(frame);
            } else {
                el.textContent = target.toLocaleString('en-US', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }
        }

        requestAnimationFrame(frame);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const counters = document.querySelectorAll('.counter');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        counters.forEach((el) => observer.observe(el));
    });
</script>

<?= $this->endSection() ?>