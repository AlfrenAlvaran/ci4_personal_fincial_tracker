<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="text-center mb-4">

    <div class="mb-3">
        <i class="bi bi-shield-lock text-warning fs-1"></i>
    </div>

    <h2 class="fw-bold">
        Forgot Password
    </h2>

    <p class="text-muted mb-0">
        Enter your email and we’ll send you a reset link
    </p>

</div>

<form action="<?= site_url('forgot-password') ?>" method="post" id="forgotForm">

    <?= csrf_field() ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ERROR MESSAGE -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="mb-3">

        <label class="form-label fw-semibold">
            Email Address
        </label>

        <input
            type="email"
            name="email"
            class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
            value="<?= old('email') ?>"
            placeholder="name@example.com"
            required>

        <?php if (session('errors.email')): ?>
            <div class="invalid-feedback">
                <?= session('errors.email') ?>
            </div>
        <?php endif; ?>

    </div>

    <button
        type="submit"
        id="forgotBtn"
        class="btn btn-warning btn-lg w-100">

        Send Reset Link

    </button>

</form>

<div class="text-center mt-3">

    <small class="text-muted">
        We’ll email you a secure password reset link
    </small>

</div>

<hr class="my-4">

<p class="text-center mb-0">

    Remember your password?

    <a
        href="<?= site_url('login') ?>"
        class="fw-semibold text-decoration-none">

        Back to Login

    </a>

</p>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('forgotForm');
    const btn = document.getElementById('forgotBtn');

    form.addEventListener('submit', () => {

        btn.disabled = true;

        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Sending...
        `;
    });

});

</script>

<?= $this->endSection() ?>