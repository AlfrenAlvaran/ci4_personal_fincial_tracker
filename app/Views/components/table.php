<?php

$tableId = $options['tableId'] ?? 'table';
$filterField = $options['filterField'] ?? '';


$options = array_merge([
    'search' => true,
    'filter' => true,
    'perPage' => true,
    'perPageOptions' => [10, 25, 50],
    'filterLabel' => 'All',
], $options ?? []);

?>

<div class="card border-0 rounded-4 shadow-sm overflow-hidden">

    
    <?php if ($options['search']): ?>
    <div class="card-body border-bottom bg-white">

        <div class="row">

            <div class="col-md-4">

                <div class="position-relative">

                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>

                    <input
                        type="text"
                        id="<?= esc($tableId) ?>Search"
                        class="form-control ps-5"
                        placeholder="Search...">

                </div>

            </div>

        </div>

    </div>
<?php endif; ?>


   
    <div class="table-responsive">
        <table class="table modern-table align-middle mb-0">

            <thead>
            <tr>
               <?php if(!empty($columns)): ?>
                 <?php foreach ($columns as $column): ?>
                    <th><?= esc($column['label']) ?></th>
                <?php endforeach; ?>
               <?php endif ?>
            </tr>
            </thead>

            <tbody id="<?= esc($tableId) ?>Body">

           <?php if(!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <tr>

                 <?php if(!empty($columns)): ?>
                       <?php foreach ($columns as $column): ?>

                        <?php
                        $field = $column['field'] ?? '';
                        $type  = $column['type'] ?? 'text';
                        ?>

                        <td data-field="<?= esc($field) ?>">

                            <?php if ($type === 'actions'): ?>

                                <div class="d-flex gap-1">

                                    <?php foreach ($column['actions'] ?? [] as $action): ?>

                                        <?php
                                        $icon = $action['icon'] ?? 'bi-circle';
                                        $class = $action['class'] ?? 'btn-secondary';

                                        $id = $row['id'] ?? '';

                                        $url = isset($action['url'])
                                            ? str_replace('{id}', $id, $action['url'])
                                            : '#';

                                        $typeAction = $action['type'] ?? 'link';
                                        $target = $action['target'] ?? '';
                                        $confirm = $action['confirm'] ?? '';
                                        ?>

                                        <?php if ($typeAction === 'modal'): ?>

                                            <button type="button"
                                                    class="btn btn-sm <?= esc($class) ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="<?= esc($target) ?>"
                                                    data-id="<?= esc($id) ?>">
                                                <i class="bi <?= esc($icon) ?>"></i>
                                            </button>

                                        <?php else: ?>

                                            <a href="<?= esc($url) ?>"
                                               class="btn btn-sm <?= esc($class) ?>"
                                               <?php if (!empty($confirm)): ?>
                                                   onclick="return confirm('<?= esc($confirm) ?>')"
                                               <?php endif; ?>>

                                                <i class="bi <?= esc($icon) ?>"></i>
                                            </a>

                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                </div>

                            <?php elseif (isset($column['callback']) && is_callable($column['callback'])): ?>

                                <?= $column['callback']($row) ?>

                            <?php else: ?>

                                <?= esc($row[$field] ?? '') ?>

                            <?php endif; ?>

                        </td>

                    <?php endforeach; ?>
                 <?php endif; ?>

                </tr>
            <?php endforeach; ?>
           <?php endif ?>

            </tbody>

        </table>
    </div>


 
    <div class="card-body bg-white border-top">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <small class="text-muted" id="<?= esc($tableId) ?>Info">
                Showing 0 - 0
            </small>

            <ul class="pagination mb-0" id="<?= esc($tableId) ?>Pagination"></ul>

        </div>

    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const tableId = "<?= esc($tableId) ?>";

    const searchInput  = document.getElementById(tableId + "Search");
    const filterInput  = document.getElementById(tableId + "Filter");
    const perPageInput = document.getElementById(tableId + "PerPage");

    const tableBody = document.getElementById(tableId + "Body");
    const rows = Array.from(tableBody.querySelectorAll("tr"));

    const pagination = document.getElementById(tableId + "Pagination");
    const info = document.getElementById(tableId + "Info");

    let currentPage = 1;

    function getFilteredRows() {

        const searchValue = searchInput ? searchInput.value.toLowerCase() : "";
        const filterValue = filterInput ? filterInput.value.toLowerCase() : "";
        const filterField = "<?= esc($filterField) ?>";

        return rows.filter(row => {

            const rowText = row.innerText.toLowerCase();

            let filterMatch = true;

            if (filterField) {
                const cell = row.querySelector(`td[data-field="${filterField}"]`);
                const value = cell ? cell.innerText.toLowerCase() : "";
                filterMatch = !filterValue || value === filterValue;
            }

            const searchMatch = rowText.includes(searchValue);

            return searchMatch && filterMatch;
        });
    }

    function renderTable() {

        const filteredRows = getFilteredRows();

        const perPage = perPageInput
            ? parseInt(perPageInput.value)
            : filteredRows.length;

        const totalPages = Math.ceil(filteredRows.length / perPage) || 1;

        if (currentPage > totalPages) currentPage = 1;

        rows.forEach(row => row.style.display = "none");

        const start = (currentPage - 1) * perPage;
        const end = start + perPage;

        filteredRows.slice(start, end).forEach(row => {
            row.style.display = "";
        });

        info.textContent = filteredRows.length
            ? `Showing ${start + 1} - ${Math.min(end, filteredRows.length)} of ${filteredRows.length}`
            : "No results found";

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {

        pagination.innerHTML = "";

        for (let i = 1; i <= totalPages; i++) {

            const li = document.createElement("li");
            li.className = "page-item " + (i === currentPage ? "active" : "");

            li.innerHTML = `<button class="page-link">${i}</button>`;

            li.onclick = () => {
                currentPage = i;
                renderTable();
            };

            pagination.appendChild(li);
        }
    }

    // SAFE EVENT BINDING
    searchInput?.addEventListener("input", () => {
        currentPage = 1;
        renderTable();
    });

    filterInput?.addEventListener("change", () => {
        currentPage = 1;
        renderTable();
    });

    perPageInput?.addEventListener("change", () => {
        currentPage = 1;
        renderTable();
    });

    renderTable();

});
</script>