<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php $hasPassword = !empty($user['password']); ?>
<?php $isGoogle = ($user['provider'] ?? null) === 'google'; ?>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php endif; ?>

<?php if (session('errors')): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-4">

    <!-- PASSWORD / SECURITY -->
    <div class="col-lg-6">

        <div class="card border h-100">

            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Security
                </h5>

                <?php if (!$hasPassword): ?>

                    <p class="text-muted">
                        Your account is signed in with Google and doesn't have a password yet.
                        Set one below to also be able to log in with your email and password.
                    </p>

                    <form action="<?= site_url('settings/password/set') ?>" method="post">

                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button class="btn btn-dark">
                            Set Password
                        </button>

                    </form>

                <?php else: ?>

                    <form action="<?= site_url('settings/password') ?>" method="post">

                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button class="btn btn-dark">
                            Update Password
                        </button>

                    </form>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- PREFERENCES -->
    <div class="col-lg-6">

        <div class="card border">

            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Preferences
                </h5>

                <form action="<?= site_url('settings/preferences') ?>" method="post">

                    <?= csrf_field() ?>

                    <div class="mb-3">

                        <label class="form-label">Currency</label>

                        <select name="currency" class="form-select">
                            <?php $currency = $user['currency'] ?? 'PHP'; ?>
                            <option value="PHP" <?= $currency === 'PHP' ? 'selected' : '' ?>>
                                Philippine Peso (₱)
                            </option>
                            <option value="USD" <?= $currency === 'USD' ? 'selected' : '' ?>>
                                US Dollar ($)
                            </option>
                            <option value="EUR" <?= $currency === 'EUR' ? 'selected' : '' ?>>
                                Euro (€)
                            </option>
                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Language</label>

                        <select name="language" class="form-select">
                            <option>English</option>
                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">Theme</label>

                        <select name="theme" class="form-select">
                            <?php $theme = $user['theme'] ?? 'Light'; ?>
                            <option value="Light" <?= $theme === 'Light' ? 'selected' : '' ?>>Light</option>
                            <option value="Dark" <?= $theme === 'Dark' ? 'selected' : '' ?>>Dark</option>
                        </select>

                    </div>

                    <button class="btn btn-dark">
                        Save Preferences
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- CONNECTED ACCOUNTS -->
<div class="card border mt-4">

    <div class="card-body">

        <h5 class="fw-semibold mb-4">
            Connected Accounts
        </h5>

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <div class="fw-semibold">
                    Google Account
                </div>

                <small class="text-muted">
                    <?= $isGoogle
                        ? 'Connected — used for quick login'
                        : 'Not connected' ?>
                </small>
            </div>

            <?php if ($isGoogle): ?>

                <?php if ($hasPassword): ?>
                    <form action="<?= site_url('settings/google/disconnect') ?>" method="post" class="m-0">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            Disconnect
                        </button>
                    </form>
                <?php else: ?>
                    <span class="badge text-bg-success">Connected</span>
                <?php endif; ?>

            <?php else: ?>

                <a href="<?= site_url('auth/google') ?>" class="btn btn-outline-dark btn-sm">
                    Connect
                </a>

            <?php endif; ?>

        </div>

        <?php if ($isGoogle && !$hasPassword): ?>
            <small class="text-muted d-block mt-2">
                Set a password above to enable disconnecting your Google account.
            </small>
        <?php endif; ?>

    </div>

</div>

<!-- DANGER ZONE -->
<div class="card border border-danger mt-4">

    <div class="card-body">

        <h5 class="text-danger fw-semibold">
            Danger Zone
        </h5>

        <p class="text-muted">
            Permanently delete your account and all financial data.
        </p>

        <button
            class="btn btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deleteAccountModal">

            Delete Account

        </button>

    </div>

</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteAccountModal">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form action="<?= site_url('account/delete') ?>" method="post">

                <?= csrf_field() ?>

                <div class="modal-header">

                    <h5 class="modal-title">
                        Delete Account
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <p>This action cannot be undone.</p>

                    <?php if ($hasPassword): ?>
                        <div class="mb-2">
                            <label class="form-label">Confirm your password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">
                            Your account will be permanently deleted.
                        </p>
                    <?php endif; ?>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Delete Account
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?= $this->endSection() ?>