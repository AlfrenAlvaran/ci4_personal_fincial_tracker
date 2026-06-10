<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\CategoryService;

class CategoryController extends BaseController
{
    protected CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function category()
    {
        return view('category/categories', [
            'title' => 'Category',
            'pageTitle' => 'Category Management',
            'categories' => $this->categoryService->getCategoriesWithUsageCount()
        ]);
    }

    public function edit($id)
    {
        $category = $this->categoryService->findById($id);

        if (!$category) {
            return redirect()
                ->to('/categories')
                ->with('error', 'Category not found.');
        }

        return view('category/edit', [
            'title' => 'Edit Category',
            'pageTitle' => 'Edit Category',
            'category' => $category
        ]);
    }

    public function update($id)
    {
        $response = $this->categoryService->update(
            (int) $id,
            $this->request->getPost()
        );

        if (!$response['success']) {

            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $response['errors'] ?? [
                    'general' => $response['message']
                ]);
        }

        return redirect()
            ->to('/categories')
            ->with('success', $response['message']);
    }

    public function create()
    {
        return view('category/create', [
            'title' => 'Category',
            'pageTitle' => 'Category Management'
        ]);
    }

    public function store()
    {
        $response = $this->categoryService->create(
            $this->request->getPost()
        );

        if (!$response['success']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $response['errors'] ?? [
                    'general' => $response['message']
                ]);
        }

        return redirect()
            ->to('/categories')
            ->with('success', $response['message']);
    }


    public function delete($id)
    {
        $result = $this->categoryService->delete((int) $id);

        if (!$result['success']) {
            return redirect()->back()
                ->with('error', $result['message']);
        }

        return redirect()->to('/categories')
            ->with('success', $result['message']);
    }
}
