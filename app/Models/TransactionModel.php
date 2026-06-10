<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'reference_number',
        'user_id',
        'transaction_type',
        'category_id',
        'amount',
        'notes',
        'transaction_date',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'amount'      => 'float',
        'user_id'     => 'integer',
        'category_id' => 'integer',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'user_id'          => 'required|integer',
        'category_id'      => 'required|integer',
        'amount'           => 'required|numeric|greater_than[0]',
        'transaction_date' => 'required|valid_date',
    ];

    protected $allowCallbacks = true;

    public function getFiltered(int $userId, ?string $month, ?string $category): array
    {
        $builder = $this->db->table('transactions t')
            ->select('t.*, c.category_name, c.category_type, c.icon AS category_icon')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.user_id', $userId);

        if ($month) {
            $builder->where("DATE_FORMAT(t.transaction_date, '%Y-%m')", $month);
        }

        if ($category && $category !== 'all') {
            $builder->where('t.category_id', (int) $category);
        }

        return $builder->get()->getResultArray();
    }

    public function getMonthlyTotals(int $userId, ?string $month): array
    {
        $builder = $this->db->table('transactions t')
            ->select("
                DATE_FORMAT(t.transaction_date, '%b %Y') AS label,
                DATE_FORMAT(t.transaction_date, '%Y-%m') AS sort_key,
                SUM(CASE WHEN t.transaction_type = 'income'   THEN t.amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN t.transaction_type = 'expenses' THEN t.amount ELSE 0 END) AS total_expenses
            ")
            ->where('t.user_id', $userId);

        if ($month) {
            $builder->where("YEAR(t.transaction_date)", substr($month, 0, 4));
        }

        return $builder->groupBy("DATE_FORMAT(t.transaction_date, '%Y-%m')")
            ->orderBy('sort_key', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getCategoryTotals(int $userId, string $type, ?string $month): array
    {
        $builder = $this->db->table('transactions t')
            ->select('c.category_name AS category, SUM(t.amount) AS total')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.user_id', $userId)
            ->where('t.transaction_type', $type);

        if ($month) {
            $builder->where("DATE_FORMAT(t.transaction_date, '%Y-%m')", $month);
        }

        return $builder->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getTopCategories(int $userId, string $type, ?string $month, int $limit = 5): array
    {
        $builder = $this->db->table('transactions t')
            ->select('c.category_name AS category, SUM(t.amount) AS total')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.user_id', $userId)
            ->where('t.transaction_type', $type);

        if ($month) {
            $builder->where("DATE_FORMAT(t.transaction_date, '%Y-%m')", $month);
        }

        return $builder->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getDistinctCategories(int $userId): array
    {
        return $this->db->table('transactions t')
            ->select('c.id, c.category_name')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.user_id', $userId)
            ->groupBy('t.category_id')
            ->orderBy('c.category_name', 'ASC')
            ->get()
            ->getResultArray();
    }
}