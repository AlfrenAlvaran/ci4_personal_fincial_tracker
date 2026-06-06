<?php
namespace App\Services;

use App\DTO\TransactionDTO;
use App\Models\CategoryModel;
use App\Models\TransactionModel;
use App\Validation\TransactionValidator;
use Exception;
class TransactionService extends BaseService
{
    public function __construct(
        protected TransactionModel $transactionModel = new TransactionModel(),
        protected CategoryModel $categoryModel = new CategoryModel(),
    ) {
    }

    public function create(array $data): array
    {
        $validation = TransactionValidator::validate($data);

        if (!$validation['ok']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
            ];
        }

        $dto = TransactionDTO::fromArray($data);


        $category = $this->categoryModel
            ->where('id', $dto->category_id)
            ->where('user_id', $this->userId())
            ->first();

        if (!$category) {
            return [
                'success' => false,
                'message' => 'Invalid category access.',
            ];
        }

        $type = $category['category_type'];

        $db = db_connect();

        $db->transStart();

        $id = $this->transactionModel->insert([
            'user_id' => $this->userId(),
            'transaction_type' => $type,
            'category_id' => $dto->category_id,
            'amount' => $dto->amount,
            'notes' => $dto->notes,
            'transaction_date' => $dto->transaction_date,
        ]);

        $db->transComplete();

        if (!$db->transStatus() || !$id) {
            throw new Exception('Transaction failed.');
        }

        return [
            'success' => true,
            'id' => $id,
            'message' => 'Transaction created successfully.',
        ];

    }

    public function findAll(): array
    {
        $userId = $this->userId();

        return $this->transactionModel
            ->select('transactions.*, categories.category_name, categories.icon')
            ->join('categories', 'categories.id = transactions.category_id')
            ->where('transactions.user_id', $userId)
            ->orderBy('transactions.transaction_date', 'DESC')
            ->findAll();
    }

    public function totalIncome()
    {
        $row = db_connect()
            ->table('transactions')
            ->select('COALESCE(SUM(amount),0) as total')
            ->where('user_id', $this->userId())
            ->where('transaction_type', 'income')
            ->get()
            ->getRowArray();

        return $row['total'] ?? 0;
    }

    public function totalExpenses()
    {
        $row = db_connect()
            ->table('transactions')
            ->select('COALESCE(SUM(amount),0) as total')
            ->where('user_id', $this->userId())
            ->where('transaction_type', 'expense')
            ->get()
            ->getRowArray();

        return $row['total'] ?? 0;
    }
    public function monthlyExpenses(): float
    {
        $row = db_connect()
            ->table('transactions')
            ->select('COALESCE(SUM(amount), 0) as total')
            ->where('user_id', $this->userId())
            ->where('transaction_type', 'expenses')
            ->where('transaction_date >=', date('Y-m-01'))
            ->where('transaction_date <=', date('Y-m-t'))
            ->get()
            ->getRow();

        return (float) ($row->total ?? 0);
    }
}