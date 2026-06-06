<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            Transactions
        </h2>

        <p class="text-muted mb-0">
            Track and manage your financial activity
        </p>

    </div>

    <a href="<?= site_url('transactions/create') ?>" class="btn btn-dark">

        <i class="bi bi-plus-lg me-2"></i>
        Add Transaction

    </a>

</div>



<?= view_cell('Table::render', [
    'columns' => [
        [
            'field' => 'id',
            'label' => '#'
        ],
        [
            'field' => 'category_name',
            'label' => 'NAME'
        ],
        [
            'field' => 'transaction_type',
            'label' => 'TYPE'
        ],
        [
            'field' => 'amount',
            'label' => 'AMOUNT'
        ],
        [
            'field' => 'transaction_date',
            'label' => 'DATE'
        ],

        
    ],
    'rows' => $transactions ?? null


]) ?>


<?= $this->endSection() ?>