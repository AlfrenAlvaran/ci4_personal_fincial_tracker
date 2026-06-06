<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\IncomeSourceService;
use CodeIgniter\HTTP\ResponseInterface;

class IncomeSourceController extends BaseController
{
    protected IncomeSourceService $incomeSourceService;
    public function __construct()
    {
        $this->incomeSourceService = new IncomeSourceService();
    }
    public function index()
    {
        return view('income/income_source', [
            'title' => 'Income Source',
            'pageTitle' => "Income Stream Management",
            'sources' => $this->incomeSourceService->getAllIncomeSources(session()->get('user_id')),
        ]);
    }

    public function store()
    {
        $result = $this->incomeSourceService->createIncomeSource($this->request->getPost());

        if (!$result['status']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $result['errors'] ?? [
                    'general' => $result['message']
                ]);
        }
        return redirect()
            ->to('income-sources')
            ->with(
                'success',
                $result['message']
            );
    }
}
