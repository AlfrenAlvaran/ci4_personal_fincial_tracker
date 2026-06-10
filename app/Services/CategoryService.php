<?php

namespace App\Services;

use App\Models\CategoryModel;
use Config\Services;
use Exception;

class CategoryService
{
    protected CategoryModel $categoryModel;
    protected $validation;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->validation = Services::validation();
    }

    public function create(array $data): array
    {
        try {
            // 1. Validate input
            if (!$this->validation->setRules($this->getCreateRules())->run($data)) {
                return [
                    'success' => false,
                    'errors' => $this->validation->getErrors(),
                ];
            }

            $userId = AuthContext::id();

            // 2. Anti-duplicate per user (FIXED SECURITY MODEL)
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

            // 3. Transaction safety
            $db = db_connect();
            $db->transStart();

            $categoryId = $this->categoryModel->insert([
                'user_id' => $userId,
                'category_name' => trim($data['category_name']),
                'category_type' => trim($data['category_type']),
                'icon' => trim($data['icon']),
                'note' => trim($data['note']),
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


    public function findByExpenses()
    {
        return $this->categoryModel->where('user_id', AuthContext::id())->where('category_type', 'expenses')->findAll();
    }

    public function findAll(): array
    {
        return $this->categoryModel
            ->where('user_id', AuthContext::id())
            ->orderBy('category_name', 'ASC')
            ->findAll();
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
    public function update(int $id, array $data): array
    {
        try {

            if (
                !$this->validation
                    ->setRules($this->getCreateRules())
                    ->run($data)
            ) {

                return [
                    'success' => false,
                    'errors' => $this->validation->getErrors(),
                ];
            }

            $category = $this->findById($id);

            if (!$category) {
                return [
                    'success' => false,
                    'message' => 'Category not found.'
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
                        'category_name' => 'Category already exists.'
                    ]
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
                'message' => 'Category updated successfully.'
            ];

        } catch (\Exception $e) {

            log_message('error', $e->getMessage());

            return [
                'success' => false,
                'message' => 'Server error occurred.'
            ];
        }
    }
    public function findById($id)
    {
        return $this->categoryModel
            ->where('id', $id)
            ->where('user_id', AuthContext::id())
            ->first();
    }
}