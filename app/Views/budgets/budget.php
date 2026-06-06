<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

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
<div class="row g-4">
    <?php if (!empty($budgets)): ?>
        <?php foreach ($budgets as $budget): ?>

            <?php
            $percentage = ($budget['spent'] / $budget['limit_amount']) * 100;
            $remaining = $budget['limit_amount'] - $budget['spent'];

            if ($percentage < 70) {
                $color = 'success';
            } elseif ($percentage < 90) {
                $color = 'warning';
            } else {
                $color = 'danger';
            }
            ?>

            <div class="col-md-6 col-xl-4">

                <div class="card border h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h5 class="fw-semibold mb-0">
                                <?= esc($budget['category_name']) ?>
                            </h5>

                            <span class="badge text-bg-<?= $color ?>">
                                <?= round($percentage) ?>%
                            </span>

                        </div>

                        <div class="mb-2">

                            <span class="fw-semibold">
                                ₱<?= number_format($budget['spent']) ?>
                            </span>

                            <span class="text-muted">
                                / ₱<?= number_format($budget['limit_amount']) ?>
                            </span>

                        </div>

                        <div class="progress mb-3">

                            <div class="progress-bar bg-<?= $color ?>" style="width: <?= min($percentage, 100) ?>%">
                            </div>

                        </div>

                        <?php if ($remaining >= 0): ?>

                            <small class="text-muted">
                                Remaining:
                            </small>

                            <div class="fw-semibold text-success">
                                ₱<?= number_format($remaining) ?>
                            </div>

                        <?php else: ?>

                            <small class="text-muted">
                                Over Budget:
                            </small>

                            <div class="fw-semibold text-danger">
                                ₱<?= number_format(abs($remaining)) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>
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

<?= $this->endSection() ?>