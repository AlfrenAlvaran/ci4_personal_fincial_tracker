<?php

use App\Tables\TableFormatter;

$tableId = $options['tableId'] ?? 'table';
$filterField = $options['filterField'] ?? null;

$options = array_merge([
    'search' => true,
], $options ?? []);
?>

<div class="card border-0 rounded-4 shadow-sm overflow-hidden">

    <?php if ($options['search']): ?>
        <div class="card-body border-bottom bg-white">
            <div class="col-md-4 position-relative">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input
                    type="text"
                    id="<?= esc($tableId) ?>Search"
                    class="form-control ps-5 rounded-3 bg-light border-0"
                    placeholder="Search...">
            </div>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table modern-table align-middle mb-0">
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th><?= esc($column['label']) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody id="<?= esc($tableId) ?>Body">
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($columns as $column): ?>
                            <?php
                            $field = $column['field'] ?? '';
                            $type  = $column['type'] ?? 'text';
                            ?>
                            <td data-field="<?= esc($field) ?>"
                                class="<?= $type === 'actions' ? 'text-end position-relative' : '' ?>">

                                <?php if ($type === 'actions'): ?>
                                    <button
                                        class="btn btn-sm btn-light border-0 rounded-circle action-toggle"
                                        style="width:34px;height:34px"
                                        type="button"
                                        data-id="<?= esc($row['id']) ?>"
                                        data-actions='<?= json_encode($column["actions"], JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                        <i class="bi bi-three-dots"></i>
                                    </button>

                                <?php elseif (!empty($column['callback'])): ?>
                                    <?php
                                    $method = $column['callback'];
                                    if (is_string($method) && strpos($method, '::') !== false) {
                                        [$class, $func] = explode('::', $method);
                                        echo TableFormatter::$func($row);
                                    } else {
                                        echo esc($row[$field] ?? '');
                                    }
                                    ?>

                                <?php else: ?>
                                    <?= esc($row[$field] ?? '') ?>
                                <?php endif; ?>

                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-body bg-white border-top d-flex justify-content-between align-items-center">
        <small class="text-muted" id="<?= esc($tableId) ?>Info">
            Showing 0 - 0
        </small>
        <ul class="pagination mb-0" id="<?= esc($tableId) ?>Pagination"></ul>
    </div>

</div>

<div id="globalActionMenu" class="global-action-menu shadow border bg-white rounded-3"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const menu = document.getElementById("globalActionMenu");

        if (!menu) return;

        document.querySelectorAll(".action-toggle").forEach(btn => {

            btn.addEventListener("click", function(e) {

                e.stopPropagation();

                menu.classList.remove("show");
                menu.innerHTML = "";

                const rect = btn.getBoundingClientRect();

                let actions = [];

                try {
                    actions = JSON.parse(btn.dataset.actions || "[]");
                } catch (err) {
                    console.error("Invalid actions JSON", err);
                    return;
                }

                const id = btn.dataset.id;

                menu.innerHTML = actions.map(action => {

                    let url = action.url
                        .replace('{id}', id)
                        .replace('%7Bid%7D', id);

                    return `
                        <a href="${url}"
                           class="${action.class ?? ''}"
                           ${action.confirm ? `onclick="return confirm('${action.confirm}')"` : ''}>
                            <i class="bi ${action.icon} me-2"></i>
                            ${action.label}
                        </a>
                    `;
                }).join('');

                menu.classList.add("show");

                let top  = rect.bottom + window.scrollY;
                let left = rect.right - 180 + window.scrollX;

                if (left < 10) left = 10;

                menu.style.top  = top + "px";
                menu.style.left = left + "px";
            });

        });

        document.addEventListener("click", function() {
            menu.classList.remove("show");
            menu.innerHTML = "";
        });

        window.addEventListener("scroll", function() {
            menu.classList.remove("show");
            menu.innerHTML = "";
        });

    });

    document.addEventListener("DOMContentLoaded", function() {

        const tableId = "<?= esc($tableId) ?>";

        const searchInput  = document.getElementById(tableId + "Search");
        const filterInput  = document.getElementById(tableId + "Filter");
        const perPageInput = document.getElementById(tableId + "PerPage");

        const tableBody = document.getElementById(tableId + "Body");
        const rows      = Array.from(tableBody.querySelectorAll("tr"));

        const pagination = document.getElementById(tableId + "Pagination");
        const info       = document.getElementById(tableId + "Info");

        let currentPage = 1;

        function getFilteredRows() {

            const searchValue = searchInput ? searchInput.value.toLowerCase() : "";
            const filterValue = filterInput ? filterInput.value.toLowerCase() : "";
            const filterField = "<?= esc($filterField) ?>";

            return rows.filter(row => {

                const rowText = row.innerText.toLowerCase();

                let filterMatch = true;

                if (filterField) {
                    const cell  = row.querySelector(`td[data-field="${filterField}"]`);
                    const value = cell ? cell.innerText.toLowerCase() : "";
                    filterMatch = !filterValue || value === filterValue;
                }

                return rowText.includes(searchValue) && filterMatch;
            });
        }

        function renderTable() {

            const filteredRows = getFilteredRows();

            const perPage = perPageInput ? parseInt(perPageInput.value) : 10;

            const totalPages = Math.ceil(filteredRows.length / perPage) || 1;

            if (currentPage > totalPages) currentPage = 1;

            rows.forEach(row => row.style.display = "none");

            const start = (currentPage - 1) * perPage;
            const end   = start + perPage;

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