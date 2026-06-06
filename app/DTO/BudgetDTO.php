<?php

namespace App\DTO;

class BudgetDTO
{
    public function __construct(
        public int $category_id,
        public float $limit_amount,
        public int $budget_month,
        public int $budget_year,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            category_id: (int) $data['category_id'],
            limit_amount: (float) $data['limit_amount'],
            budget_month: (int) ($data['budget_month'] ?? date('n')),
            budget_year: (int) ($data['budget_year'] ?? date('Y')),
        );
    }

    public function toArray(): array
    {
        return [
            'category_id' => $this->category_id,
            'limit_amount' => $this->limit_amount,
            'budget_month' => $this->budget_month,
            'budget_year' => $this->budget_year,
        ];
    }
}