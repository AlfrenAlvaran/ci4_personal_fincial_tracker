<?php

namespace App\Tables;

class TableFormatter
{
    public static function amount($row)
    {
        return '₱ ' . number_format($row['amount'], 2);
    }

    public static function type($row)
    {
        return $row['transaction_type'] === 'income'
            ? '<span class="badge bg-success">Income</span>'
            : '<span class="badge bg-danger">Expense</span>';
    }

    public static function date($row)
    {
        return date('M d, Y', strtotime($row['transaction_date']));
    }
}