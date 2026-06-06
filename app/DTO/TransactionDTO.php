<?php

namespace App\DTO;

class TransactionDTO
{
    public function __construct(
        public int $category_id,
        public float $amount,
        public string $notes,
        public string $transaction_date
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            (int) $data['category_id'],
            (float) $data['amount'],
            trim($data['notes'] ?? ''),
            $data['transaction_date']
        );
    }
}