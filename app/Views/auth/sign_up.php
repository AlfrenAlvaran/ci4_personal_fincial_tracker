```php
<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>

<div class="text-center mb-4">

    <div class="mb-3">
        <i class="bi bi-person-plus-fill text-success fs-1"></i>
    </div>

    <h2 class="fw-bold">
        Create Account
    </h2>

    <p class="text-muted mb-0">
        Start your financial journey today
    </p>

</div>

<form
    action="<?= site_url('register') ?>"
    method="post"
    id="registerForm">

    <?= csrf_field() ?>

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

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label fw-semibold">
                First Name
            </label>

            <input
                type="text"
                name="first_name"
                value="<?= old('first_name') ?>"
                class="form-control <?= session('errors.first_name') ? 'is-invalid' : '' ?>"
                required>

            <?php if (session('errors.first_name')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.first_name') ?>
                </div>
            <?php endif; ?>

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label fw-semibold">
                Last Name
            </label>

            <input
                type="text"
                name="last_name"
                value="<?= old('last_name') ?>"
                class="form-control <?= session('errors.last_name') ? 'is-invalid' : '' ?>"
                required>

            <?php if (session('errors.last_name')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.last_name') ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="mb-3">

        <label class="form-label fw-semibold">
            Username
        </label>

        <input
            type="text"
            name="username"
            value="<?= old('username') ?>"
            class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
            required>

        <?php if (session('errors.username')): ?>
            <div class="invalid-feedback">
                <?= session('errors.username') ?>
            </div>
        <?php endif; ?>

    </div>

    <div class="mb-3">

        <label class="form-label fw-semibold">
            Email Address
        </label>

        <input
            type="email"
            name="email"
            value="<?= old('email') ?>"
            class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
            placeholder="name@example.com"
            required>

        <?php if (session('errors.email')): ?>
            <div class="invalid-feedback">
                <?= session('errors.email') ?>
            </div>
        <?php endif; ?>

    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label class="form-label fw-semibold">
                Password
            </label>

            <div class="input-group">

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                    required>

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword('password', this)">

                    <i class="bi bi-eye"></i>

                </button>

            </div>

            <?php if (session('errors.password')): ?>
                <div class="invalid-feedback d-block">
                    <?= session('errors.password') ?>
                </div>
            <?php endif; ?>

            <div class="progress mt-2" style="height: 6px;">
                <div
                    id="passwordStrength"
                    class="progress-bar"
                    role="progressbar"
                    style="width:0%">
                </div>
            </div>

            <small
                id="strengthText"
                class="text-muted">
            </small>

        </div>

        <div class="col-md-6 mb-3">

            <label class="form-label fw-semibold">
                Confirm Password
            </label>

            <div class="input-group">

                <input
                    type="password"
                    id="confirm_password"
                    name="confirm_password"
                    class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>"
                    required>

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword('confirm_password', this)">

                    <i class="bi bi-eye"></i>

                </button>

            </div>

            <?php if (session('errors.confirm_password')): ?>
                <div class="invalid-feedback d-block">
                    <?= session('errors.confirm_password') ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="form-check mb-4">

        <input
            class="form-check-input"
            type="checkbox"
            id="terms"
            required>

        <label
            class="form-check-label"
            for="terms">
            I agree to the
            <a href="<?= site_url('terms') ?>" class="text-decoration-none">
                Terms & Conditions
            </a>
            and
            <a href="<?= site_url('privacy') ?>" class="text-decoration-none">
                Privacy Policy
            </a>

        </label>
    </div>

    <button
        type="submit"
        id="registerBtn"
        class="btn btn-success btn-lg w-100">

        Create Account

    </button>

</form>

<div class="text-center mt-3">

    <small class="text-muted">

        <i class="bi bi-shield-check me-1"></i>

        Your information is encrypted and protected

    </small>

</div>

<hr class="my-4">

<p class="text-center mb-0">

    Already have an account?

    <a
        href="<?= site_url('login') ?>"
        class="fw-semibold text-decoration-none">

        Sign In

    </a>

</p>

<script>

function togglePassword(id, button)
{
    const input = document.getElementById(id);
    const icon = button.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

const password = document.getElementById('password');
const meter = document.getElementById('passwordStrength');
const text = document.getElementById('strengthText');

password.addEventListener('input', function () {

    let score = 0;
    const value = this.value;

    if (value.length >= 8) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[a-z]/.test(value)) score++;
    if (/\d/.test(value)) score++;
    if (/[\W_]/.test(value)) score++;

    const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
    const labels = [
        '',
        'Very Weak',
        'Weak',
        'Medium',
        'Strong',
        'Very Strong'
    ];

    meter.style.width = widths[score];
    text.textContent = labels[score];
});

document
    .getElementById('registerForm')
    .addEventListener('submit', function () {

        const btn =
            document.getElementById('registerBtn');

        btn.disabled = true;

        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"></span>
            Creating Account...
        `;
    });

</script>

<?= $this->endSection() ?>