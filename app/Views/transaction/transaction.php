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
            'field' => 'reference_number',
            'label' => 'REF NO'
        ],
        [
            'field' => 'category_name',
            'label' => 'NAME'
        ],
        [
            'field' => 'transaction_type',
            'label' => 'TYPE',
            'callback' => 'TableFormatter::type'
        ],
        [
            'field' => 'amount',
            'label' => 'AMOUNT',
            'callback' => 'TableFormatter::amount'
        ],
        [
            'field' => 'transaction_date',
            'label' => 'DATE',
            'callback' => 'TableFormatter::date'
        ],
        [
            'field' => 'actions',
            'label' => '',
            'type' => 'actions',
            'actions' => [
                [
                    'label' => 'View',
                    'icon'  => 'bi-eye',
                    'url'   => site_url('transactions/detail/{id}')
                ],
                [
                    'label' => 'Edit',
                    'icon'  => 'bi-pencil',
                    'url'   => site_url('transactions/edit/{id}')
                ],
                [
                    'label'   => 'Delete',
                    'icon'    => 'bi-trash',
                    'url'     => site_url('transactions/delete/{id}'),
                    'class'   => 'text-danger',
                    'confirm' => 'Delete this record?'
                ]
            ]
        ]
    ],
    'rows' => $transactions ?? []
]) ?>


<?= $this->endSection() ?>