<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$totalLimit = 0;
$totalSpent = 0;

foreach (($budgets ?? []) as $b) {
    $totalLimit += (float) ($b['limit_amount'] ?? 0);
    $totalSpent += (float) ($b['spent'] ?? 0);
}

$totalRemaining = $totalLimit - $totalSpent;
$overallPercentage = $totalLimit > 0 ? ($totalSpent / $totalLimit) * 100 : 0;

?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Budgets
        </h2>

        <p class="text-muted mb-0">
            Manage your monthly spending goals
        </p>

    </div>

    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#budgetModal">

        <i class="bi bi-plus-lg me-2"></i>
        New Budget

    </button>

</div>

<?php if (session('errors')): ?>
    <?php foreach (session('errors') as $error): ?>
        <div class="alert alert-danger">
            <?= esc($error) ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (session('success')): ?>
    <div class="alert alert-success">
        <?= esc(session('success')) ?>
    </div>
<?php endif; ?>

<?php if (!empty($budgets)): ?>

    <!-- OVERVIEW SUMMARY -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <small class="text-muted">Total Budgeted</small>
                    <h4 class="fw-bold mt-2 mb-0">
                        ₱<span class="counter" data-target="<?= esc($totalLimit) ?>" data-decimals="2">0.00</span>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <small class="text-muted">Total Spent</small>
                    <h4 class="fw-bold <?= $overallPercentage >= 90 ? 'text-danger' : ($overallPercentage >= 70 ? 'text-warning' : 'text-success') ?> mt-2 mb-0">
                        ₱<span class="counter" data-target="<?= esc($totalSpent) ?>" data-decimals="2">0.00</span>
                    </h4>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border h-100">
                <div class="card-body">
                    <small class="text-muted">
                        <?= $totalRemaining >= 0 ? 'Remaining' : 'Over Budget' ?>
                    </small>
                    <h4 class="fw-bold <?= $totalRemaining >= 0 ? 'text-primary' : 'text-danger' ?> mt-2 mb-0">
                        ₱<span class="counter" data-target="<?= esc(abs($totalRemaining)) ?>" data-decimals="2">0.00</span>
                    </h4>
                </div>
            </div>
        </div>

    </div>

<?php endif; ?>

<div class="row g-4">

    <?php if (!empty($budgets)): ?>

        <?php foreach ($budgets as $budget): ?>

            <?php
            $limit = (float) ($budget['limit_amount'] ?? 0);
            $spent = (float) ($budget['spent'] ?? 0);
            $percentage = $limit > 0 ? ($spent / $limit) * 100 : 0;
            $remaining = $limit - $spent;

            if ($percentage < 70) {
                $color = 'success';
            } elseif ($percentage < 90) {
                $color = 'warning';
            } else {
                $color = 'danger';
            }
            ?>

            <div class="col-md-6 col-xl-4">

                <div class="card border h-100 budget-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <h5 class="fw-semibold mb-0">
                                <?= esc($budget['category_name']) ?>
                            </h5>

                            <div class="d-flex align-items-center gap-2">

                                <span class="badge text-bg-<?= $color ?> percentage-badge" data-target="<?= round($percentage) ?>">
                                    0%
                                </span>

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button
                                                class="dropdown-item edit-budget-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#budgetModal"
                                                data-id="<?= esc($budget['id']) ?>"
                                                data-category="<?= esc($budget['category_id'] ?? '') ?>"
                                                data-limit="<?= esc($budget['limit_amount']) ?>">
                                                <i class="bi bi-pencil me-2"></i>
                                                Edit
                                            </button>
                                        </li>
                                        <li>
                                            <form action="<?= site_url('budgets/delete/' . $budget['id']) ?>" method="post" onsubmit="return confirm('Delete this budget?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash me-2"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                            </div>

                        </div>

                        <div class="mb-2">

                            <span class="fw-semibold">
                                ₱<span class="counter" data-target="<?= esc($spent) ?>" data-decimals="2">0.00</span>
                            </span>

                            <span class="text-muted">
                                / ₱<?= number_format($limit, 2) ?>
                            </span>

                        </div>

                        <div class="progress mb-3" style="height: 8px;">

                            <div
                                class="progress-bar bg-<?= $color ?> progress-bar-animated-fill"
                                role="progressbar"
                                data-width="<?= min($percentage, 100) ?>"
                                style="width: 0%;">
                            </div>

                        </div>

                        <?php if ($remaining >= 0): ?>

                            <small class="text-muted">
                                Remaining:
                            </small>

                            <div class="fw-semibold text-success">
                                ₱<span class="counter" data-target="<?= esc($remaining) ?>" data-decimals="2">0.00</span>
                            </div>

                        <?php else: ?>

                            <small class="text-muted">
                                Over Budget:
                            </small>

                            <div class="fw-semibold text-danger">
                                ₱<span class="counter" data-target="<?= esc(abs($remaining)) ?>" data-decimals="2">0.00</span>
                            </div>

                        <?php endif; ?>

                        <?php if ($percentage >= 90 && $percentage < 100): ?>
                            <div class="mt-2 small text-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Approaching limit
                            </div>
                        <?php elseif ($percentage >= 100): ?>
                            <div class="mt-2 small text-danger">
                                <i class="bi bi-exclamation-octagon me-1"></i>
                                Over budget
                            </div>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="col-12">
            <div class="card border">
                <div class="card-body text-center py-5">
                    <i class="bi bi-piggy-bank fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-semibold mb-1">No budgets yet</h5>
                    <p class="text-muted mb-3">
                        Create your first budget to start tracking your spending goals.
                    </p>
                    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#budgetModal">
                        <i class="bi bi-plus-lg me-2"></i>
                        New Budget
                    </button>
                </div>
            </div>
        </div>

    <?php endif; ?>

</div>

<?= view_cell('Modal::render', [
    'id' => "budgetModal",
    'title' => 'New Budget',
    'view' => 'budgets/_form',
    'data' => [
        'action' => site_url('budgets/create'),
        'budget' => []
    ]
]); ?>

<style>
    .budget-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .budget-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .progress-bar-animated-fill {
        transition: width 1s ease-out;
    }

    .counter,
    .percentage-badge {
        display: inline-block;
    }
</style>

<script>
    function animateCounter(el) {
        const target = parseFloat(el.dataset.target) || 0;
        const decimals = parseInt(el.dataset.decimals ?? '0', 10);
        const duration = 1000;
        const start = performance.now();

        function easeOutQuad(t) {
            return t * (2 - t);
        }

        function frame(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeOutQuad(progress);
            const current = target * eased;

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

    function animatePercentageBadge(el) {
        const target = parseInt(el.dataset.target, 10) || 0;
        const duration = 1000;
        const start = performance.now();

        function frame(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const current = Math.round(target * progress);

            el.textContent = current + '%';

            if (progress < 1) {
                requestAnimationFrame(frame);
            }
        }

        requestAnimationFrame(frame);
    }

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.counter').forEach(animateCounter);
        document.querySelectorAll('.percentage-badge').forEach(animatePercentageBadge);

        document.querySelectorAll('.progress-bar-animated-fill').forEach(function (bar) {
            const width = bar.dataset.width;
            requestAnimationFrame(function () {
                bar.style.width = width + '%';
            });
        });

        const budgetModal = document.getElementById('budgetModal');

        if (budgetModal) {
            budgetModal.addEventListener('show.bs.modal', function (event) {
                const trigger = event.relatedTarget;

                const titleEl = budgetModal.querySelector('.modal-title');
                const form = budgetModal.querySelector('form');

                if (!trigger || !trigger.classList.contains('edit-budget-btn')) {
                    if (titleEl) titleEl.textContent = 'New Budget';
                    if (form) {
                        form.reset();
                        form.action = "<?= site_url('budgets/create') ?>";
                    }
                    return;
                }

                const id = trigger.dataset.id;
                const categoryId = trigger.dataset.category;
                const limit = trigger.dataset.limit;

                if (titleEl) titleEl.textContent = 'Edit Budget';

                if (form) {
                    form.action = "<?= site_url('budgets/update') ?>/" + id;

                    const categoryField = form.querySelector('[name="category_id"]');
                    const limitField = form.querySelector('[name="limit_amount"]');

                    if (categoryField) categoryField.value = categoryId;
                    if (limitField) limitField.value = limit;
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>