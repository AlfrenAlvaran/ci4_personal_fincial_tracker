<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
?>


<div class="mb-4">

    <a href="<?= site_url('transactions') ?>" class="text-decoration-none">

        <i class="bi bi-arrow-left"></i>
        Back to Transactions

    </a>

</div>
<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= esc(session('success')) ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert">
        </button>
    </div>
<?php endif; ?>

<div class="card border">

    <div class="card  border-0">

        <div class="card-header bg-white py-4">

            <h3 class="fw-bold mb-1">
                Create Category
            </h3>

            <p class="text-muted mb-0">
                Organize your income and expenses with custom categories.
            </p>

        </div>

        <div class="card-body p-4">


            <div class="card bg-light border-0 mb-4">

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center me-3"
                            style="width:60px;height:60px;">

                            <iconify-icon id="liveIcon" icon="<?= old('icon', 'mdi:tag') ?>" width="28">
                            </iconify-icon>

                        </div>

                        <div>

                            <h6 id="liveName" class="mb-1 fw-bold">
                                <?= old('category_name', 'Category Name') ?>
                            </h6>

                            <small id="liveType" class="text-muted">
                                <?= ucfirst(old('category_type', 'expenses')) ?>
                            </small>

                        </div>

                    </div>

                </div>

            </div>

            <form action="<?= site_url('categories/store') ?>" method="post">

                <?= csrf_field() ?>

                <!-- Category Name -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Category Name
                    </label>

                    <input type="text" name="category_name"
                        class="form-control form-control-lg <?= isset($errors['category_name']) ? 'is-invalid' : '' ?>"
                        value="<?= old('category_name') ?>" placeholder="e.g. Food, Salary, Transportation">

                    <?php if (isset($errors['category_name'])): ?>
                        <div class="invalid-feedback">
                            <?= esc($errors['category_name']) ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Category Type -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Category Type
                    </label>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <input type="radio" class="btn-check" name="category_type" id="income" value="income"
                                <?= old('category_type') === 'income' ? 'checked' : '' ?>>

                            <label class="btn btn-outline-success w-100 py-3" for="income">

                                Income

                            </label>

                        </div>

                        <div class="col-md-6">

                            <input type="radio" class="btn-check" name="category_type" id="expenses" value="expenses"
                                <?= old('category_type', 'expenses') === 'expenses' ? 'checked' : '' ?>>

                            <label class="btn btn-outline-danger w-100 py-3" for="expenses">

                                Expense

                            </label>

                        </div>

                    </div>

                    <?php if (isset($errors['category_type'])): ?>
                        <div class="text-danger mt-2 small">
                            <?= esc($errors['category_type']) ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Icon -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Category Icon
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">

                            <iconify-icon id="iconPreview" icon="<?= old('icon', 'mdi:tag') ?>" width="20">
                            </iconify-icon>

                        </span>

                        <input type="text" name="icon" id="iconInput"
                            class="form-control <?= isset($errors['icon']) ? 'is-invalid' : '' ?>"
                            value="<?= old('icon') ?>" readonly>

                        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#iconModal">

                            Choose Icon

                        </button>

                    </div>

                    <?php if (isset($errors['icon'])): ?>
                        <div class="text-danger mt-2 small">
                            <?= esc($errors['icon']) ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Note -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Note
                    </label>

                    <textarea name="note" rows="4" class="form-control"><?= old('note') ?></textarea>

                </div>

                <div class="d-flex justify-content-end">

                    <button type="submit" class="btn btn-dark btn-lg">

                        <iconify-icon icon="mdi:content-save" width="20">
                        </iconify-icon>

                        Save Category

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



<!-- Icon Modal -->
<div class="modal fade" id="iconModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Select Icon
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <input type="text" id="iconSearch" class="form-control mb-3" placeholder="Search icons...">

                <div id="iconList" class="d-flex flex-wrap gap-3">
                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

<style>
    .icon-item {
        width: 90px;
        cursor: pointer;
        transition: .2s ease;
    }

    .icon-item:hover {
        transform: scale(1.05);
        background: #f8f9fa;
    }
</style>

<script>
    const icons = [
        "mdi:cash",
        "mdi:wallet",
        "mdi:bank",
        "mdi:cart",
        "mdi:food",
        "mdi:car",
        "mdi:home",
        "mdi:shopping",
        "mdi:chart-line",
        "mdi:credit-card",
        "mdi:gift",
        "mdi:phone",
        "mdi:book",
        "mdi:tag",
        "mdi:account",
        "mdi:airplane",
        "mdi:hospital",
        "mdi:school",
        "mdi:wifi",
        "mdi:lightning-bolt",
        "mdi:gamepad",
        "mdi:movie",
        "mdi:coffee",
        "mdi:basketball",
        "mdi:train",
        "mdi:bike",
        "mdi:gas-station"
    ];
    const iconList = document.getElementById('iconList');
    const iconSearch = document.getElementById('iconSearch');

    function renderIcons(filter = '') {

        iconList.innerHTML = '';

        icons
            .filter(icon =>
                icon.toLowerCase().includes(
                    filter.toLowerCase()
                )
            )
            .forEach(icon => {

                const item = document.createElement('div');

                item.className =
                    'icon-item p-3 border rounded text-center';

                item.innerHTML = `
                <iconify-icon
                    icon="${icon}"
                    width="28">
                </iconify-icon>

                <div class="small mt-2">
                    ${icon}
                </div>
            `;

                item.onclick = () => {

                    document.getElementById(
                        'iconInput'
                    ).value = icon;

                    document.getElementById(
                        'iconPreview'
                    ).setAttribute(
                        'icon',
                        icon
                    );

                    document.getElementById(
                        'liveIcon'
                    ).setAttribute(
                        'icon',
                        icon
                    );

                    bootstrap.Modal
                        .getInstance(
                            document.getElementById(
                                'iconModal'
                            )
                        )
                        .hide();
                };

                iconList.appendChild(item);

            });
    }

    iconSearch.addEventListener(
        'input',
        event => renderIcons(event.target.value)
    );

    document.getElementById(
        'iconModal'
    ).addEventListener(
        'shown.bs.modal',
        () => {
            iconSearch.value = '';
            renderIcons();
            iconSearch.focus();
        }
    );

    document.querySelector(
        '[name="category_name"]'
    ).addEventListener(
        'input',
        event => {

            document.getElementById(
                'liveName'
            ).textContent =
                event.target.value ||
                'Category Name';

        }
    );

    document.querySelectorAll(
        '[name="category_type"]'
    ).forEach(element => {

        element.addEventListener(
            'change',
            () => {

                document.getElementById(
                    'liveType'
                ).textContent =
                    element.value
                        .charAt(0)
                        .toUpperCase() +
                    element.value.slice(1);

            }
        );

    });
</script>

<?= $this->endSection() ?>