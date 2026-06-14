<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card border">

            <div class="card-body p-4">

                <div class="text-center mb-4">

                    <?php if (!empty($user['avatar'])): ?>
                        <img
                            src="<?= esc($user['avatar']) ?>"
                            alt="Avatar"
                            class="rounded-circle mb-3"
                            style="width:120px;height:120px;object-fit:cover;">
                    <?php else: ?>
                        <div
                            class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                            style="width:120px;height:120px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                    <?php endif; ?>

                    <h3 class="fw-bold mb-1">
                        <?= esc($user['username']) ?>
                    </h3>

                    <p class="text-muted mb-0">
                        <?= esc($user['email']) ?>
                    </p>

                </div>

                <?php if (session('success')): ?>
                    <div class="alert alert-success">
                        <?= esc(session('success')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session('error')) ?>
                    </div>
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

                <form method="post" action="<?= site_url('profile/update') ?>">

                    <?= csrf_field() ?>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                First Name
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                class="form-control"
                                value="<?= esc($user['first_name'] ?? '') ?>">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Last Name
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                class="form-control"
                                value="<?= esc($user['last_name'] ?? '') ?>">

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Username
                        </label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?= esc($user['username'] ?? '') ?>">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= esc($user['email'] ?? '') ?>"
                            <?= !empty($user['provider']) ? 'readonly' : '' ?>>

                        <?php if (!empty($user['provider'])): ?>
                            <div class="form-text">
                                Email is managed by your <?= esc(ucfirst($user['provider'])) ?> account.
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= esc($user['phone'] ?? '') ?>">

                    </div>

                    <button class="btn btn-dark">
                        <i class="bi bi-check-lg me-2"></i>
                        Save Changes
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>