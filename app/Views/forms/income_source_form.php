<div class="mb-3">
    <label for="income_source_name" class="form-label">
        Stream Name
    </label>

    <input type="text" id="income_source_name" name="income_source_name" class="form-control"
        value="<?= old('income_source_name', $incomeSource['name'] ?? '') ?>" placeholder="e.g. Salary" required>
</div>

<div class="mb-3">
    <label for="income_source_type" class="form-label">
        Type
    </label>

    <input type="text" id="income_source_type" name="income_source_type" class="form-control"
        value="<?= old('income_source_type', $incomeSource['type'] ?? '') ?>" placeholder="e.g. Employment" required>
    <?php if (session('errors.income_source_type')): ?>
        <div class="invalid-feedback d-block">
            <?= session('errors.income_source_type') ?>
        </div>
    <?php endif ?>
</div>

<div class="mb-3">
    <label for="monthly_average_amount" class="form-label">
        Monthly Average
    </label>

    <div class="input-group">

        <span class="input-group-text">
            ₱
        </span>

        <input type="number" id="monthly_average_amount" name="monthly_average_amount" class="form-control" min="0"
            step="0.01" value="<?= old('monthly_average_amount', $incomeSource['monthly_average'] ?? '') ?>"
            placeholder="0.00" required>
        <?php if (session('errors.monthly_average_amount')): ?>
            <div class="invalid-feedback d-block">
                <?= session('errors.monthly_average_amount') ?>
            </div>
        <?php endif ?>
    </div>
</div>

<div class="mb-3">
    <label for="income_source_image" class="form-label">
        Income Source Image
    </label>

    <input type="file" id="income_source_image" name="income_source_image" class="form-control"
        accept=".jpg,.jpeg,.png,.webp">

    <div class="form-text">
        Upload a logo or image representing this income source.
    </div>
    <?php if (session('errors.income_source_image')): ?>
        <div class="invalid-feedback d-block">
            <?= session('errors.income_source_image') ?>
        </div>
    <?php endif ?>
</div>

<?php if (!empty($incomeSource['image_path'])): ?>
    <div class="mb-3">
        <img src="<?= base_url($incomeSource['image_path']) ?>" alt="Income Source" class="img-thumbnail"
            style="max-height:120px;">
    </div>
<?php endif; ?>