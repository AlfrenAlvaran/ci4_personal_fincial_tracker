<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row g-4">

    <!-- PASSWORD -->
    <div class="col-lg-6">

        <div class="card border h-100">

            <div class="card-body">

                <h5 class="fw-semibold mb-4">
                    Security
                </h5>

                <form action="<?= site_url('settings/password') ?>"
                    method="post">

                    <?= csrf_field() ?>

                    <div class="mb-3">

                        <label class="form-label">
                            Current Password
                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            New Password
                        </label>

                        <input
                            type="password"
                            name="new_password"
                            class="form-control">

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control">

                    </div>

                    <button class="btn btn-dark">
                        Update Password
                    </button>

                </form>

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

                <form action="<?= site_url('settings/preferences') ?>"
                    method="post">

                    <?= csrf_field() ?>

                    <div class="mb-3">

                        <label class="form-label">
                            Currency
                        </label>

                        <select
                            name="currency"
                            class="form-select">

                            <option value="PHP">
                                Philippine Peso (₱)
                            </option>

                            <option value="USD">
                                US Dollar ($)
                            </option>

                            <option value="EUR">
                                Euro (€)
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Language
                        </label>

                        <select
                            name="language"
                            class="form-select">

                            <option>
                                English
                            </option>

                        </select>

                    </div>

                    <div class="mb-4">

                        <label class="form-label">
                            Theme
                        </label>

                        <select
                            name="theme"
                            class="form-select">

                            <option>
                                Light
                            </option>

                            <option>
                                Dark
                            </option>

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
                    Connected for quick login
                </small>

            </div>

            <span class="badge text-bg-success">
                Connected
            </span>

        </div>

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

            <div class="modal-header">

                <h5 class="modal-title">
                    Delete Account
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                This action cannot be undone.

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-light"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <a href="<?= site_url('account/delete') ?>"
                    class="btn btn-danger">

                    Delete Account

                </a>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>