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
            'categories' => $this->categoryService->findAll()
        ]);
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
}