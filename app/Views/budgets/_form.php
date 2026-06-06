<?php $errors = session('errors') ?? [] ?>

<div class="mb-3">
    <label for="category_id" class="form-label">
        Category
    </label>

    <select name="category_id" id="category_id" class="form-select" required>

        <option value="">
            ---------- Select ----------
        </option>

        <?php if (!empty($categoryExpenses)): ?>
            <?php foreach ($categoryExpenses as $category): ?>
                <option value="<?= esc($category['id']) ?>">
                    <?= old('category_id') == $category['id'] ? 'selected' : '' ?>>
                    <?= esc($category['category_name']) ?>
                </option>
            <?php endforeach; ?>
        <?php endif ?>
    </select>

    <?php if (isset($errors['category_id'])): ?>
        <small class="text-danger">
            <?= esc($errors['category_id']) ?>
        </small>
    <?php endif; ?>

</div>

<div class="mb-3">
    <label for="limit_amount" class="form-label">
        Budget Limit
    </label>

    <div class="input-group">

        <span class="input-group-text">
            ₱
        </span>

        <input type="number" name="limit_amount" id="limit_amount" class="form-control"
            value="<?= old('limit_amount') ?>" min="1" step="0.01" placeholder="3000.00" required>

        <?php if (isset($errors['limit_amount'])): ?>
            <small class="text-danger">
                <?= esc($errors['limit_amount']) ?>
            </small>
        <?php endif; ?>

    </div>

    <small class="text-muted">
        Enter your monthly spending limit for this category.
    </small>



</div>