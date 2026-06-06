<?php

namespace App\Services;

use App\Models\IncomeSourceModel;
use Config\Services;

class IncomeSourceService
{
    protected IncomeSourceModel $incomeSourceModel;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->incomeSourceModel = new IncomeSourceModel();
        $this->validation = Services::validation();
        $this->request = Services::request();
    }

    public function createIncomeSource(array $incomeSourceData): array
    {
        if (
            !$this->validation
                ->setRules($this->createIncomeSourceRules())
                ->run($incomeSourceData)
        ) {
            return [
                'status' => false,
                'errors' => $this->validation->getErrors()
            ];
        }

        $incomeSourceImage =
            $this->request->getFile('income_source_image');

        $imagePath = null;

        if (
            $incomeSourceImage &&
            $incomeSourceImage->isValid() &&
            !$incomeSourceImage->hasMoved()
        ) {
            $generatedImageName =
                $incomeSourceImage->getRandomName();

            $incomeSourceImage->move(
                ROOTPATH . 'public/uploads/income-sources',
                $generatedImageName
            );

            $imagePath =
                'uploads/income-sources/' .
                $generatedImageName;
        }

        $incomeSourceId = $this->incomeSourceModel->insert([
            'user_id' => session()->get('user_id'),
            'name' => trim($incomeSourceData['income_source_name']),
            'type' => trim($incomeSourceData['income_source_type']),
            'monthly_average' => (float) $incomeSourceData['monthly_average_amount'],
            'image' => $imagePath
        ]);

        if (!$incomeSourceId) {
            return [
                'status' => false,
                'message' => 'Failed to create income source.',
                'errors' => $this->incomeSourceModel->errors()
            ];
        }

        return [
            'status' => true,
            'income_source_id' => $incomeSourceId,
            'message' => 'Income source created successfully.'
        ];
    }

    public function findIncomeSourceById(
        int $incomeSourceId
    ): ?array {
        return $this->incomeSourceModel->find($incomeSourceId);
    }

    public function getAllIncomeSources(int $userId): array
    {
        return $this->incomeSourceModel
            ->where('user_id', $userId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function deleteIncomeSource(
        int $incomeSourceId
    ): bool {
        $incomeSource =
            $this->incomeSourceModel->find($incomeSourceId);

        if (!$incomeSource) {
            return false;
        }

        if (
            !empty($incomeSource['image']) &&
            file_exists(ROOTPATH . 'public/' . $incomeSource['image'])
        ) {
            unlink(
                ROOTPATH . 'public/' .
                $incomeSource['image']
            );
        }

        return (bool) $this->incomeSourceModel
            ->delete($incomeSourceId);
    }

    private function createIncomeSourceRules(): array
    {
        return [
            'income_source_name' => [
                'rules' => 'required|min_length[2]|max_length[100]',
                'errors' => [
                    'required' => 'Income source name is required.'
                ]
            ],

            'income_source_type' => [
                'rules' => 'required|min_length[2]|max_length[50]',
                'errors' => [
                    'required' => 'Income source type is required.'
                ]
            ],

            'monthly_average_amount' => [
                'rules' => 'required|decimal|greater_than_equal_to[0]',
                'errors' => [
                    'required' => 'Monthly average amount is required.',
                    'decimal' => 'Monthly average amount must be valid.'
                ]
            ],

            'income_source_image' => [
                'rules' =>
                    'permit_empty'
                    . '|is_image[income_source_image]'
                    . '|max_size[income_source_image,2048]'
                    . '|mime_in[income_source_image,image/png,image/jpeg,image/webp]',
                'errors' => [
                    'is_image' => 'Please upload a valid image.',
                    'max_size' => 'Image must not exceed 2MB.',
                    'mime_in' => 'Only PNG, JPG, JPEG, and WEBP are allowed.'
                ]
            ]
        ];
    }
}