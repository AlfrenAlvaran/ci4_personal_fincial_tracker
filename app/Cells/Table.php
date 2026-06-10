<?php

namespace App\Cells;

class Table
{
    public function render(
        array $columns = [],
        array $rows = [],
        array $options = []
    ) {
        return view('components/table', [
            'columns' => $columns,
            'rows' => $rows,
            'options' => array_merge([
                'search' => true,
                'tableId' => 'dynamicTable',
                'filterField' => null
            ], $options)
        ]);
    }
}
