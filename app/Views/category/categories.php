<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Categories
        </h2>

        <p class="text-muted mb-0">
            Organize and monitor your financial categories
        </p>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

    </div>

    <a href="<?= site_url('categories/create') ?>" class="btn btn-dark">

        <i class="bi bi-plus-lg me-2"></i>
        New Category

    </a>

</div>

<div class="row g-4">

    <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $category): ?>

            <div class="col-md-6 col-xl-3">

                <div class="card border h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                style="width:55px;height:55px;">

                                <iconify-icon icon="<?= esc($category['icon']) ?>" width="28"></iconify-icon>

                            </div>

                            <div class="dropdown">

                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li>
                                        <a class="dropdown-item" href="<?= site_url('categories/edit/' . $category['id']) ?>">
                                            Edit
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item text-danger"
                                            href="<?= site_url('categories/delete/' . $category['id']) ?>">
                                            Delete
                                        </a>
                                    </li>

                                </ul>

                            </div>

                        </div>

                        <h5 class="fw-semibold mb-1">
                            <?= esc($category['category_name']) ?>
                        </h5>

                        <small class="text-muted d-block mb-2">
                            <?= esc($category['category_type']) ?>
                        </small>
                        <?php if ((int)$category['usage_count'] > 0): ?>
                            <span class="dropdown-item text-muted">
                                Cannot delete (<?= $category['usage_count'] ?> uses)
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($category['note'])): ?>
                            <p class="text-muted small mb-0">
                                <?= esc($category['note']) ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted small mb-0 fst-italic">
                                No note added
                            </p>
                        <?php endif; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>
    <?php endif ?>

</div>

<?= $this->endSection() ?>