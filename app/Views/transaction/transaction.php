<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php

$totalIncome = 0;
$totalExpenses = 0;
$count = count($transactions ?? []);

foreach (($transactions ?? []) as $t) {
    if (($t['transaction_type'] ?? '') === 'income') {
        $totalIncome += (float) ($t['amount'] ?? 0);
    } else {
        $totalExpenses += (float) ($t['amount'] ?? 0);
    }
}

$netTotal = $totalIncome - $totalExpenses;

?>

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

<!-- QUICK STATS -->
<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Total Income</small>
                <h4 class="fw-bold text-success mt-2 mb-0">
                    ₱<span class="counter" data-target="<?= esc($totalIncome) ?>" data-decimals="2">0.00</span>
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Total Expenses</small>
                <h4 class="fw-bold text-danger mt-2 mb-0">
                    ₱<span class="counter" data-target="<?= esc($totalExpenses) ?>" data-decimals="2">0.00</span>
                </h4>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border h-100">
            <div class="card-body">
                <small class="text-muted">Net Total</small>
                <h4 class="fw-bold <?= $netTotal >= 0 ? 'text-primary' : 'text-danger' ?> mt-2 mb-0">
                    ₱<span class="counter" data-target="<?= esc($netTotal) ?>" data-decimals="2">0.00</span>
                </h4>
            </div>
        </div>
    </div>

</div>

<!-- FILTER BAR -->
<div class="card border mb-3">

    <div class="card-body">

        <div class="row g-3 align-items-center">

            <div class="col-md-5">

                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Search by reference, name, or amount...">
                </div>

            </div>

            <div class="col-md-4">

                <div class="btn-group w-100" role="group" id="typeFilter">
                    <button type="button" class="btn btn-outline-dark active" data-filter="all">
                        All
                    </button>
                    <button type="button" class="btn btn-outline-success" data-filter="income">
                        Income
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-filter="expenses">
                        Expenses
                    </button>
                </div>

            </div>

            <div class="col-md-3 text-md-end">

                <small class="text-muted" id="resultCount">
                    Showing <?= $count ?> of <?= $count ?> transactions
                </small>

            </div>

        </div>

    </div>

</div>

<div id="transactionsTableWrapper">

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

</div>

<div id="noResults" class="text-muted text-center py-5 d-none">
    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
    No transactions match your search.
</div>

<style>
    .counter {
        display: inline-block;
    }

    #transactionsTableWrapper table tbody tr {
        transition: background-color 0.15s ease;
    }

    #transactionsTableWrapper table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    #typeFilter .btn.active {
        color: #fff;
    }

    #typeFilter .btn-outline-dark.active {
        background-color: #212529;
    }

    #typeFilter .btn-outline-success.active {
        background-color: #198754;
    }

    #typeFilter .btn-outline-danger.active {
        background-color: #dc3545;
    }
</style>

<script>
    function animateCounter(el) {
        const target = parseFloat(el.dataset.target) || 0;
        const decimals = parseInt(el.dataset.decimals ?? '0', 10);
        const duration = 1000;
        const start = performance.now();

        function easeOutQuad(t) {
            return t * (2 - t);
        }

        function frame(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = easeOutQuad(progress);
            const current = target * eased;

            el.textContent = current.toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });

            if (progress < 1) {
                requestAnimationFrame(frame);
            } else {
                el.textContent = target.toLocaleString('en-US', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }
        }

        requestAnimationFrame(frame);
    }

    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.counter').forEach(animateCounter);

        const searchInput = document.getElementById('searchInput');
        const typeFilter = document.getElementById('typeFilter');
        const resultCount = document.getElementById('resultCount');
        const noResults = document.getElementById('noResults');
        const tableWrapper = document.getElementById('transactionsTableWrapper');

        const table = tableWrapper.querySelector('table');
        const tbody = table ? table.querySelector('tbody') : null;
        const allRows = tbody ? Array.from(tbody.querySelectorAll('tr')) : [];
        const totalCount = allRows.length;

        let activeType = 'all';

        function rowMatchesType(row, type) {
            if (type === 'all') return true;

            const text = row.textContent.toLowerCase();

            if (type === 'income') {
                return text.includes('income');
            }

            if (type === 'expenses') {
                return text.includes('expense');
            }

            return true;
        }

        function applyFilters() {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            allRows.forEach(function (row) {
                const matchesSearch = query === '' || row.textContent.toLowerCase().includes(query);
                const matchesType = rowMatchesType(row, activeType);

                const visible = matchesSearch && matchesType;
                row.classList.toggle('d-none', !visible);

                if (visible) visibleCount++;
            });

            resultCount.textContent = `Showing ${visibleCount} of ${totalCount} transactions`;

            if (table) {
                table.classList.toggle('d-none', visibleCount === 0);
            }
            noResults.classList.toggle('d-none', visibleCount !== 0);
        }

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        if (typeFilter) {
            typeFilter.querySelectorAll('button').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    typeFilter.querySelectorAll('button').forEach(function (b) {
                        b.classList.remove('active');
                    });

                    btn.classList.add('active');
                    activeType = btn.dataset.filter;

                    applyFilters();
                });
            });
        }

        // Sortable columns by clicking header
        if (table) {
            const headers = table.querySelectorAll('thead th');

            headers.forEach(function (th, index) {
                // Skip the actions column (last column, usually empty label)
                if (index === headers.length - 1) return;

                th.style.cursor = 'pointer';
                th.addEventListener('click', function () {
                    sortTableByColumn(index, th);
                });
            });
        }

        let sortState = {};

        function sortTableByColumn(index, th) {
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const ascending = !sortState[index];

            rows.sort(function (a, b) {
                const aText = a.children[index]?.textContent.trim() ?? '';
                const bText = b.children[index]?.textContent.trim() ?? '';

                const aNum = parseFloat(aText.replace(/[^0-9.\-]/g, ''));
                const bNum = parseFloat(bText.replace(/[^0-9.\-]/g, ''));

                let comparison;

                if (!isNaN(aNum) && !isNaN(bNum) && aText.match(/[0-9]/) && bText.match(/[0-9]/)) {
                    comparison = aNum - bNum;
                } else {
                    comparison = aText.localeCompare(bText);
                }

                return ascending ? comparison : -comparison;
            });

            rows.forEach(function (row) {
                tbody.appendChild(row);
            });

            sortState = {};
            sortState[index] = ascending;

            table.querySelectorAll('thead th').forEach(function (header) {
                header.querySelector('.sort-indicator')?.remove();
            });

            const indicator = document.createElement('span');
            indicator.className = 'sort-indicator ms-1';
            indicator.innerHTML = ascending ? '↑' : '↓';
            th.appendChild(indicator);
        }
    });
</script>

<?= $this->endSection() ?>