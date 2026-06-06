```php
<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="text-center mb-4">

    <div class="mb-3">
        <i class="bi bi-wallet2 text-success fs-1"></i>
    </div>

    <h2 class="fw-bold">
        Welcome Back
    </h2>

    <p class="text-muted mb-0">
        Sign in to continue tracking your financial goals
    </p>

</div>

<form action="<?= site_url('authenticate') ?>" method="post" id="loginForm">

    <?= csrf_field() ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= session()->getFlashdata('error') ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= session()->getFlashdata('success') ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>

    <div class="d-grid mb-4">

        <a
            href="<?= site_url('auth/google') ?>"
            class="btn btn-outline-dark">

            <i class="bi bi-google me-2"></i>

            Continue with Google

        </a>

    </div>

    <div class="position-relative text-center my-4">

        <hr>

        <span
            class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">

            OR

        </span>

    </div>

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

    <div class="mb-3">

        <label class="form-label fw-semibold">
            Password
        </label>

        <div class="input-group">

            <input
                type="password"
                id="password"
                name="password"
                class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                placeholder="Enter password"
                required>

            <button
                class="btn btn-outline-secondary"
                type="button"
                id="togglePassword">

                <i class="bi bi-eye"></i>

            </button>

            <?php if (session('errors.password')): ?>
                <div class="invalid-feedback d-block">
                    <?= session('errors.password') ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div class="form-check">

            <input
                class="form-check-input"
                type="checkbox"
                name="remember"
                id="remember">

            <label
                class="form-check-label"
                for="remember">

                Remember me

            </label>

        </div>

        <a
            href="<?= site_url('forgot-password') ?>"
            class="text-decoration-none">

            Forgot Password?

        </a>

    </div>

    <button
        type="submit"
        id="loginBtn"
        class="btn btn-success btn-lg w-100">

        Login

    </button>

</form>

<div class="text-center mt-3">

    <small class="text-muted">

        <i class="bi bi-shield-lock me-1"></i>

        Protected with secure authentication

    </small>

</div>

<hr class="my-4">

<p class="text-center mb-0">

    Don't have an account?

    <a
        href="<?= site_url('register') ?>"
        class="fw-semibold text-decoration-none">

        Create Account

    </a>

</p>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const togglePassword =
        document.getElementById('togglePassword');

    const password =
        document.getElementById('password');

    togglePassword.addEventListener('click', () => {

        const type =
            password.getAttribute('type') === 'password'
                ? 'text'
                : 'password';

        password.setAttribute('type', type);

        togglePassword.innerHTML =
            type === 'password'
                ? '<i class="bi bi-eye"></i>'
                : '<i class="bi bi-eye-slash"></i>';
    });

    document
        .getElementById('loginForm')
        .addEventListener('submit', () => {

            const btn =
                document.getElementById('loginBtn');

            btn.disabled = true;

            btn.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm me-2">
                </span>
                Signing In...
            `;
        });

});

</script>

<?= $this->endSection() ?>
