<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Personal Tracker' ?></title>

    <link rel="stylesheet" href="<?= base_url('bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

    <div class="container-fluid vh-100">
        <div class="row h-100">

            <!-- LEFT PANEL -->
            <div class="col-lg-7 d-none d-lg-flex bg-dark text-white align-items-center">

                <div class="px-5">

                    <span class="badge bg-success mb-3 px-3 py-2">
                        Personal Tracker
                    </span>

                    <h1 class="display-4 fw-bold">
                        <?= $heroTitle ?? 'Manage your finances with confidence' ?>
                    </h1>

                    <p class="lead text-secondary mt-3">
                        <?= $heroDescription ?? 'Track income, expenses, savings goals, budgets, and financial growth in one dashboard.' ?>
                    </p>

                    <div class="row mt-5">

                        <div class="col-md-4">
                            <h3 class="fw-bold text-success">₱25K+</h3>
                            <small>Tracked Savings</small>
                        </div>

                        <div class="col-md-4">
                            <h3 class="fw-bold text-success">100%</h3>
                            <small>Budget Control</small>
                        </div>

                        <div class="col-md-4">
                            <h3 class="fw-bold text-success">24/7</h3>
                            <small>Access Anywhere</small>
                        </div>

                    </div>

                </div>

            </div>

            <!-- RIGHT PANEL -->
            <div class="col-lg-5 d-flex align-items-center justify-content-center">

                <div class=" p-md-5" style="max-width: <?= $cardWidth ?? '450px' ?>; width:100%;">

                    <?= $this->renderSection('content') ?>

                </div>

            </div>

        </div>
    </div>

    <script src="<?= base_url('bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

</body>

</html>