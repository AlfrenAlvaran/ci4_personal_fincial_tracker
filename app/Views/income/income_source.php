<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Income Sources
        </h2>

        <p class="text-muted mb-0">
            Manage and track where your income comes from.
        </p>

    </div>

    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#sourceModal">

        <i class="bi bi-plus-lg me-2"></i>
        Add Source

    </button>

</div>

<div class="row g-4">

    <?php if (!empty($sources)): ?>
        <?php foreach ($sources as $source): ?>

            <div class="col-md-6 col-xl-4">

                <div class="card border h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-3">

                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                style="width:55px;height:55px;overflow:hidden;">

                                <?php if (!empty($source['image'])): ?>
                                    <img src="<?= $source['image'] ?>" alt="<?= esc($source['name']) ?>"
                                        style="width:100%;height:100%;object-fit:cover;">
                                <?php else: ?>
                                    <i class="bi bi-wallet2 fs-4 text-secondary"></i>
                                <?php endif; ?>

                            </div>

                            <?php if ($source['status'] == 1): ?>
                                <span class="badge text-bg-success align-self-start">
                                    Active
                                </span>
                            <?php else: ?>
                                <span class="badge text-bg-danger align-self-start">
                                    Inactive
                                </span>
                            <?php endif ?>

                        </div>

                        <h5 class="fw-semibold">
                            <?= esc($source['name']) ?>
                        </h5>

                        <small class="text-muted">
                            <?= esc($source['type']) ?>
                        </small>

                        <hr>

                        <small class="text-muted">
                            Monthly Average
                        </small>

                        <h4 class="fw-bold text-success mt-2">
                            ₱<?= number_format($source['monthly_average']) ?>
                        </h4>

                        <div class="mt-auto text-end">
                            <a href="<?= site_url('income-sources/delete/' . $source['id']) ?>" class="text-danger">
                                <i class="bi bi-trash fs-5"></i>
                            </a>
                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p>No Income Source Found</p>
    <?php endif ?>

</div>
<?= view_cell('Modal::render', [
    'id' => 'sourceModal',
    'title' => 'Add Income Stream',
    'view' => 'forms/income_source_form',
    'data' => [
        'action' => site_url('income-sources/store'),
        'incomeSource' => []
    ]
]) ?>
<?= $this->endSection() ?>