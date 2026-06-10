<?php

namespace App\Services;

use App\Models\CategoryModel;
use App\Models\TransactionModel;
use Config\Services;
use Exception;

class CategoryService
{
    protected CategoryModel $categoryModel;
    protected TransactionModel $transactionModel;
    protected $validation;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->transactionModel = new TransactionModel();
        $this->validation = Services::validation();
    }

    public function create(array $data): array
    {
        try {

            if (!$this->validation->setRules($this->getCreateRules())->run($data)) {
                return [
                    'success' => false,
                    'errors' => $this->validation->getErrors(),
                ];
            }

            $userId = AuthContext::id();

            $exists = $this->categoryModel
                ->where('user_id', $userId)
                ->where('category_name', trim($data['category_name']))
                ->first();

            if ($exists) {
                return [
                    'success' => false,
                    'errors' => [
                        'category_name' => 'Category already exists.',
                    ],
                ];
            }

            $db = db_connect();

            $db->transStart();

            $categoryId = $this->categoryModel->insert([
                'user_id' => $userId,
                'category_name' => trim($data['category_name']),
                'category_type' => trim($data['category_type']),
                'icon' => trim($data['icon']),
                'note' => trim($data['note'] ?? ''),
            ]);

            $db->transComplete();

            if (!$db->transStatus() || !$categoryId) {
                throw new Exception('Failed to create category.');
            }

            return [
                'success' => true,
                'categoryId' => $categoryId,
                'message' => 'Category created successfully.',
            ];

        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            return [
                'success' => false,
                'message' => 'Server error occurred.',
            ];
        }
    }

    public function update(int $id, array $data): array
    {
        try {

            if (!$this->validation->setRules($this->getCreateRules())->run($data)) {
                return [
                    'success' => false,
                    'errors' => $this->validation->getErrors(),
                ];
            }

            $category = $this->findById($id);

            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Category not found.',
                ];
            }

            $duplicate = $this->categoryModel
                ->where('user_id', AuthContext::id())
                ->where('category_name', trim($data['category_name']))
                ->where('id !=', $id)
                ->first();

            if ($duplicate) {
                return [
                    'success' => false,
                    'errors' => [
                        'category_name' => 'Category already exists.',
                    ],
                ];
            }

            $this->categoryModel->update($id, [
                'category_name' => trim($data['category_name']),
                'category_type' => trim($data['category_type']),
                'icon' => trim($data['icon']),
                'note' => trim($data['note'] ?? ''),
            ]);

            return [
                'success' => true,
                'message' => 'Category updated successfully.',
            ];

        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            return [
                'success' => false,
                'message' => 'Server error occurred.',
            ];
        }
    }

    public function delete(int $id): array
    {
        try {

            $category = $this->findById($id);

            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Category not found.',
                ];
            }

            $usageCount = $this->transactionModel
                ->where('category_id', $id)
                ->countAllResults();

            if ($usageCount > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete category because it is already used by transactions.',
                ];
            }

            $db = db_connect();

            $db->transStart();

            $this->categoryModel->delete($id);

            $db->transComplete();

            if (!$db->transStatus()) {
                throw new Exception('Failed to delete category.');
            }

            return [
                'success' => true,
                'message' => 'Category deleted successfully.',
            ];

        } catch (Exception $e) {

            log_message('error', $e->getMessage());

            return [
                'success' => false,
                'message' => 'Server error occurred.',
            ];
        }
    }

    public function findByExpenses(): array
    {
        return $this->categoryModel
            ->where('user_id', AuthContext::id())
            ->where('category_type', 'expenses')
            ->findAll();
    }

    public function findByIncome(): array
    {
        return $this->categoryModel
            ->where('user_id', AuthContext::id())
            ->where('category_type', 'income')
            ->findAll();
    }

    public function findAll(): array
    {
        return $this->categoryModel
            ->where('user_id', AuthContext::id())
            ->orderBy('category_name', 'ASC')
            ->findAll();
    }

    public function findById(int $id): ?array
    {
        return $this->categoryModel
            ->where('id', $id)
            ->where('user_id', AuthContext::id())
            ->first();
    }

    public function getUsageCount(int $categoryId): int
    {
        return $this->transactionModel
            ->where('category_id', $categoryId)
            ->countAllResults();
    }

    public function getCategoriesWithUsageCount(): array
    {
        $categories = $this->findAll();

        foreach ($categories as &$category) {
            $category['usage_count'] = $this->getUsageCount($category['id']);
        }

        return $categories;
    }

    private function getCreateRules(): array
    {
        return [
            'category_name' => [
                'rules' => 'required|min_length[2]|max_length[50]',
                'errors' => [
                    'required' => 'Please enter category name.',
                ],
            ],
            'category_type' => [
                'rules' => 'required|in_list[expenses,income]',
            ],
            'icon' => [
                'rules' => 'required|max_length[50]',
            ],
            'note' => [
                'rules' => 'permit_empty|max_length[1000]',
            ],
        ];
    }
}