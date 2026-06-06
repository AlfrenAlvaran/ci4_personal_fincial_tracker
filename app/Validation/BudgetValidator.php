<?php

namespace App\Validation;

class BudgetValidator
{
    public static function validate(array $data): array
    {
        $validation = service('validation');

        $validation->setRules([
            'category_id' => [
                'label' => 'Category',
                'rules' => 'required|is_natural_no_zero',
                'errors' => [
                    'required' => 'Please select a category.',
                    'is_natural_no_zero' => 'Invalid category selected.',
                ],
            ],

            'limit_amount' => [
                'label' => 'Budget Limit',
                'rules' => 'required|decimal|greater_than[0]',
                'errors' => [
                    'required' => 'Budget limit is required.',
                    'decimal' => 'Budget limit must be a valid amount.',
                    'greater_than' => 'Budget limit must be greater than zero.',
                ],
            ],
        ]);

        if (! $validation->run($data)) {
            return [
                'ok'     => false,
                'errors' => $validation->getErrors(),
            ];
        }

        return [
            'ok'     => true,
            'errors' => [],
        ];
    }
}