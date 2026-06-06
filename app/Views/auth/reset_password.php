<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="text-center mb-4">

    <div class="mb-3">
        <i class="bi bi-key text-primary fs-1"></i>
    </div>

    <h2 class="fw-bold">
        Reset Password
    </h2>

    <p class="text-muted mb-0">
        Enter your new password below
    </p>

</div>

<form action="<?= site_url('reset-password') ?>" method="post">

    <?= csrf_field() ?>

    <input type="hidden" name="token" value="<?= $token ?? '' ?>">

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="mb-3">

        <label class="form-label fw-semibold">New Password</label>

        <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Enter new password"
            required>

    </div>

    <div class="mb-3">

        <label class="form-label fw-semibold">Confirm Password</label>

        <input
            type="password"
            name="password_confirm"
            class="form-control"
            placeholder="Confirm password"
            required>

    </div>

    <button type="submit" class="btn btn-primary btn-lg w-100">
        Update Password
    </button>

</form>

<hr class="my-4">

<p class="text-center mb-0">
    <a href="<?= site_url('login') ?>" class="text-decoration-none">
        Back to Login
    </a>
</p>

<?= $this->endSection() ?>