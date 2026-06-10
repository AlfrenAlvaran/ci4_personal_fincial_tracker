<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= site_url('transactions') ?>" class="text-decoration-none">
        <i class="bi bi-arrow-left"></i>
        Back to Transactions
    </a>
</div>

<div class="card border shadow-sm">
    <div class="card-body p-4">

        <div class="card bg-light border-0 mb-4">
            <div class="card-body">

                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center me-3"
                        style="width:60px;height:60px;">

                        <i class="bi bi-cash-coin fs-4 text-dark"></i>

                    </div>

                    <div>
                        <h5 id="liveName" class="mb-1 fw-bold">
                            Edit Transaction
                        </h5>

                        <small id="liveType" class="text-muted">
                            <?= ucfirst(old('transaction_type', $transaction['transaction_type'])) ?>
                        </small>
                    </div>
                </div>

            </div>
        </div>

        <form action="<?= site_url('transactions/update/' . $transaction['id']) ?>" method="post">

            <?= csrf_field() ?>
  

            <!-- Transaction Type -->
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Transaction Type
                </label>

                <div class="row g-3">

                    <div class="col-md-6">

                        <input type="radio" class="btn-check" name="transaction_type" id="income" value="income"
                            <?= old('transaction_type', $transaction['transaction_type']) === 'income' ? 'checked' : '' ?>>

                        <label class="btn btn-outline-success w-100 py-3" for="income">
                            <i class="bi bi-arrow-down-circle me-2"></i>
                            Income
                        </label>

                    </div>

                    <div class="col-md-6">

                        <input type="radio" class="btn-check" name="transaction_type" id="expense" value="expense"
                            <?= old('transaction_type', $transaction['transaction_type']) === 'expense' ? 'checked' : '' ?>>

                        <label class="btn btn-outline-danger w-100 py-3" for="expense">
                            <i class="bi bi-arrow-up-circle me-2"></i>
                            Expense
                        </label>

                    </div>

                </div>

                <?php if (session('errors.transaction_type')): ?>
                    <small class="text-danger">
                        <?= session('errors.transaction_type') ?>
                    </small>
                <?php endif; ?>

            </div>

            <!-- Category -->
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Category
                </label>

                <select name="category_id" id="category_id" class="form-select">

                    <option value="">----- Select Category -----</option>

                </select>

                <?php if (session('errors.category_id')): ?>
                    <small class="text-danger">
                        <?= session('errors.category_id') ?>
                    </small>
                <?php endif; ?>

            </div>

            <!-- Amount -->
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Amount
                </label>

                <div class="input-group">

                    <span class="input-group-text">₱</span>

                    <input type="number" step="0.01" min="0" name="amount" class="form-control"
                        value="<?= old('amount', $transaction['amount']) ?>">

                </div>

                <?php if (session('errors.amount')): ?>
                    <small class="text-danger">
                        <?= session('errors.amount') ?>
                    </small>
                <?php endif; ?>

            </div>

            <!-- Notes -->
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Notes
                </label>

                <textarea name="notes" rows="4" class="form-control"><?= old('notes', $transaction['notes']) ?></textarea>

                <?php if (session('errors.notes')): ?>
                    <small class="text-danger">
                        <?= session('errors.notes') ?>
                    </small>
                <?php endif; ?>

            </div>

            <!-- Transaction Date -->
            <div class="mb-4">

                <label class="form-label fw-bold">
                    Transaction Date
                </label>

                <input type="date" name="transaction_date" class="form-control"
                    value="<?= old('transaction_date', $transaction['transaction_date']) ?>">

                <?php if (session('errors.transaction_date')): ?>
                    <small class="text-danger">
                        <?= session('errors.transaction_date') ?>
                    </small>
                <?php endif; ?>

            </div>

            <div class="d-flex justify-content-end gap-2">

                <a href="<?= site_url('transactions') ?>" class="btn btn-light">
                    Cancel
                </a>

                <button type="submit" class="btn btn-dark">
                    <i class="bi bi-check-circle me-1"></i>
                    Update Transaction
                </button>

            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const categories = <?= json_encode($categories ?? []) ?>;
    const currentCategoryId = '<?= old('category_id', $transaction['category_id']) ?>';

    const categorySelect = document.getElementById('category_id');
    const liveType = document.getElementById('liveType');

    const typeInputs = document.querySelectorAll('input[name="transaction_type"]');

    function loadCategories(type) {

        categorySelect.innerHTML =
            '<option value="">----- Select Category -----</option>';

        categories.forEach(category => {

            if (category.category_type === type) {

                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.category_name;

                if (currentCategoryId == category.id) {
                    option.selected = true;
                }

                categorySelect.appendChild(option);
            }
        });
    }

    function updateType() {

        const selected = document.querySelector('input[name="transaction_type"]:checked');
        if (!selected) return;

        const type = selected.value;

        liveType.innerText = type.charAt(0).toUpperCase() + type.slice(1);

        loadCategories(type);
    }

    typeInputs.forEach(input => {
        input.addEventListener('change', updateType);
    });

    updateType();

});
</script>

<?= $this->endSection() ?>