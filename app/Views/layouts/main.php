<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?></title>

    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

    <!-- SIDEBAR -->
    <aside class="position-fixed top-0 start-0 vh-100 bg-white border-end" style="width:260px; z-index:1030;">

        <div class="d-flex flex-column h-100">

            <!-- LOGO -->
            <div class="p-4 border-bottom">

                <h4 class="fw-bold mb-1">
                    FinanceFlow
                </h4>

                <small class="text-muted">
                    Personal Finance Tracker
                </small>

            </div>

            <!-- MENU -->
            <div class="p-3">

                <ul class="nav flex-column gap-1">

                    <li class="nav-item">
                        <a href="<?= site_url('/') ?>"
                            class="nav-link bg-success-subtle text-success fw-semibold rounded-3">
                            <i class="bi bi-grid me-2"></i>
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('transactions') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-receipt me-2"></i>
                            Transactions
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('categories') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-tags me-2"></i>
                            Categories
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a href="<?= site_url('income-sources') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-cash-stack me-2"></i>
                            Income Sources
                        </a>
                    </li> -->

                    <li class="nav-item">
                        <a href="<?= site_url('budgets') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-wallet2 me-2"></i>
                            Budgets
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('forecasting') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-graph-up-arrow me-2"></i>
                            Forecasting
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('reports') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-bar-chart me-2"></i>
                            Reports
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?= site_url('settings') ?>" class="nav-link text-dark rounded-3">
                            <i class="bi bi-gear me-2"></i>
                            Settings
                        </a>
                    </li>

                </ul>

            </div>

            <!-- FOOTER -->
            <div class="mt-auto p-3 border-top">

                <small class="text-muted">
                    Logged in as
                </small>

                <div class="fw-semibold">
                    <?= esc(session('username')) ?>
                </div>

                <form action="<?= site_url('logout') ?>" method="post">
                    <?= csrf_field() ?>

                    <button type="submit" class="btn btn-outline-danger w-100 mt-3">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Logout
                    </button>
                </form>

            </div>

        </div>

    </aside>

    <!-- MAIN -->
    <main style="margin-left:260px;">

        <!-- TOPBAR -->
        <nav class="navbar bg-white border-bottom sticky-top">

            <div class="container-fluid px-4">

                <h5 class="mb-0 fw-semibold">
                    <?= $pageTitle ?? 'Dashboard' ?>
                </h5>

                <div class="dropdown">

                    <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown">

                        <?= esc(session('username')) ?>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                            <a class="dropdown-item" href="<?= site_url('profile') ?>">
                                Profile
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="<?= site_url('settings') ?>">
                                Settings
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                                Logout
                            </a>
                        </li>

                    </ul>

                </div>

            </div>

        </nav>

        <!-- CONTENT -->
        <div class="p-4">

            <?= $this->renderSection('content') ?>

        </div>

    </main>
    <!-- <script src="<?= base_url('/bootstrap/js/bootstrap.min.js') ?>"></script> -->
    <script src="<?= base_url('/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
</body>

</html>