<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">

    <div class="col-lg-8">

        <div class="card border">

            <div class="card-body p-4">

                <div class="text-center mb-4">

                    <div
                        class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:120px;height:120px;">

                        <i class="bi bi-person fs-1"></i>

                    </div>

                    <h3 class="fw-bold mb-1">
                        <?= esc(session('username')) ?>
                    </h3>

                    <p class="text-muted mb-0">
                        <?= esc(session('email')) ?>
                    </p>

                </div>

                <form method="post"
                    action="<?= site_url('profile/update') ?>">

                    <?= csrf_field() ?>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="fullname"
                                class="form-control"
                                value="<?= esc($user['fullname'] ?? '') ?>">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                value="<?= esc($user['username'] ?? '') ?>">

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Email Address
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= esc($user['email'] ?? '') ?>">

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