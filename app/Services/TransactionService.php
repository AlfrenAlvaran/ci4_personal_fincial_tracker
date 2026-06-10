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
    ) {}

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

        $db = db_connect();
        $db->transStart();

        $id = $this->transactionModel->insert([
            'reference_number' => $this->generateReferenceNumber(),
            'user_id' => $this->userId(),
            'transaction_type' => $category['category_type'],
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
        return $this->transactionModel
            ->select('transactions.*, categories.category_name, categories.icon')
            ->join('categories', 'categories.id = transactions.category_id')
            ->where('transactions.user_id', $this->userId())
            ->orderBy('transactions.transaction_date', 'DESC')
            ->findAll();
    }

    public function findById($id): ?array
    {
        return $this->transactionModel
            ->select('
            transactions.*,
            categories.category_name,
            categories.category_type,
            categories.icon
        ')
            ->join('categories', 'categories.id = transactions.category_id')
            ->where('transactions.id', $id)
            ->where('transactions.user_id', $this->userId())
            ->first();
    }
    public function update($id, array $data): array
    {
        $validation = TransactionValidator::validate($data);

        if (!$validation['ok']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
            ];
        }

        $transaction = $this->findById($id);

        if (!$transaction) {
            return [
                'success' => false,
                'message' => 'Transaction not found.',
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

        $this->transactionModel->update($id, [
            'transaction_type' => $category['category_type'],
            'category_id' => $dto->category_id,
            'amount' => $dto->amount,
            'notes' => $dto->notes,
            'transaction_date' => $dto->transaction_date,
        ]);

        return [
            'success' => true,
            'message' => 'Transaction updated successfully.',
        ];
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
            ->where('transaction_type', 'expenses')
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

    public function recent(int $limit = 5): array
    {
        return $this->transactionModel
            ->where('user_id', $this->userId())
            ->orderBy('transaction_date', 'DESC')
            ->findAll($limit);
    }

    private function generateReferenceNumber(): string
    {
        $last = $this->transactionModel
            ->select('reference_number')
            ->where('user_id', $this->userId())
            ->orderBy('id', 'DESC')
            ->first();

        if (!$last || empty($last['reference_number'])) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) str_replace('#TRX-', '', $last['reference_number']);
            $nextNumber = $lastNumber + 1;
        }

        return '#TRX-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public function delete(int $id): array
    {
        $transaction = $this->transactionModel
            ->where('id', $id)
            ->where('user_id', $this->userId())
            ->first();

        if (!$transaction) {
            return [
                'success' => false,
                'message' => 'Transaction not found.',
            ];
        }

        $this->transactionModel->delete($id);

        return [
            'success' => true,
            'message' => 'Transaction deleted successfully.',
        ];
    }
}
