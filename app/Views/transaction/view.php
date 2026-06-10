<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Transaction Details
        </h2>

        <p class="text-muted mb-0">
            Review complete information about this transaction
        </p>

    </div>

    <div>

        <a href="<?= site_url('transactions') ?>"
           class="btn btn-light border">

            <i class="bi bi-arrow-left me-2"></i>
            Back

        </a>

    </div>

</div>

<div class="row g-4">

    <!-- MAIN CARD -->
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4">

                <!-- Amount -->
                <div class="text-center mb-5">

                    <?php if ($transaction['transaction_type'] === 'income'): ?>

                        <span class="badge text-bg-success mb-3">
                            Income
                        </span>

                        <h1 class="fw-bold text-success mb-0">
                            + ₱<?= number_format($transaction['amount'], 2) ?>
                        </h1>

                    <?php else: ?>

                        <span class="badge text-bg-danger mb-3">
                            Expense
                        </span>

                        <h1 class="fw-bold text-danger mb-0">
                            - ₱<?= number_format($transaction['amount'], 2) ?>
                        </h1>

                    <?php endif; ?>

                </div>


                <div class="row g-4">

                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Category
                        </small>

                        <div class="fw-semibold fs-5">
                            <?= esc($transaction['category_name']) ?>
                        </div>

                    </div>


                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Transaction Date
                        </small>

                        <div class="fw-semibold fs-5">
                            <?= date('F d, Y', strtotime($transaction['transaction_date'])) ?>
                        </div>

                    </div>


                 

                    <div class="col-md-6">

                        <small class="text-muted d-block">
                            Reference No.
                        </small>

                        <div class="fw-semibold fs-5">
                            #TRX-<?= str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) ?>
                        </div>

                    </div>

                </div>


                <!-- Notes -->
                <?php if (!empty($transaction['description'])): ?>

                    <hr class="my-4">

                    <small class="text-muted d-block mb-2">
                        Notes
                    </small>

                    <div class="bg-light rounded-3 p-3">

                        <?= esc($transaction['description']) ?>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>


    <!-- SIDE CARD -->
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Transaction Actions
                </h5>

                <div class="d-grid gap-2">

                    <a href="<?= site_url('transactions/edit/' . $transaction['id']) ?>"
                       class="btn btn-dark">

                        <i class="bi bi-pencil-square me-2"></i>
                        Edit Transaction

                    </a>

                    <button class="btn btn-outline-danger">

                        <i class="bi bi-trash me-2"></i>
                        Delete Transaction

                    </button>

                </div>

                <hr>

                <div class="mb-3">

                    <small class="text-muted d-block">
                        Created At
                    </small>

                    <div class="fw-semibold">
                        <?= date('F d, Y h:i A', strtotime($transaction['created_at'])) ?>
                    </div>

                </div>

                <div>

                    <small class="text-muted d-block">
                        Last Updated
                    </small>

                    <div class="fw-semibold">
                        <?= date('F d, Y h:i A', strtotime($transaction['updated_at'])) ?>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div> 




<?= $this->endSection() ?>