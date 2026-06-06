<?php

namespace App\Validation;

use Config\Services;

class TransactionValidator
{
    public static function validate(array $data): array
    {
        $v = Services::validation();

        $v->setRules([
            'category_id' => 'required|integer',
            'amount' => 'required|numeric|greater_than[0]',
            'transaction_date' => 'required|valid_date',
            'notes' => 'permit_empty|max_length[1000]',
        ]);

        if (! $v->run($data)) {
            return [
                'ok' => false,
                'errors' => $v->getErrors(),
            ];
        }

        return ['ok' => true];
    }
}
