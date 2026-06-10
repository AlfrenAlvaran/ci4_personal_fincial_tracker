<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ExportService;
use App\Services\ReportService;
use CodeIgniter\HTTP\ResponseInterface;

class ReportController extends BaseController
{
    public function __construct(
        protected ReportService $reportService = new ReportService(),
        protected ExportService $exportService = new ExportService(),
    ) {}
    public function index()
    {
        $month = $this->request->getGet('months');
        $category = $this->request->getGet('category');

        return view('reports/report', [
            'title' => 'report',
            'pageTitle' => 'Financial Reports',

            'summary' => $this->reportService->getSummary($month, $category),
            'chartMonthly'  => $this->reportService->getMonthlyChartData($month),
            'chartCategory' => $this->reportService->getCategoryChartData($month),
            'topSpending'   => $this->reportService->getTopSpending($month),
            'categories'    => $this->reportService->getCategories(),
            'filters'       => compact('month', 'category'),
        ]);
    }

    public function export()
    {
        $month        = $this->sanitizeMonth($this->request->getGet('months'));
        $category     = $this->sanitizeCategory($this->request->getGet('category'));
        $format       = $this->request->getGet('format') ?? 'pdf';
        $categoryName = $this->request->getGet('category_name') ?? 'All';

        $summary      = $this->reportService->getSummary($month, $category);
        $topSpending  = $this->reportService->getTopSpending($month);
        $chartMonthly = $this->reportService->getMonthlyChartData($month);
        if ($format === 'xlsx') {
            return $this->exportService->exportXlsx($summary, $topSpending, $chartMonthly, $month, $category);
        }

        return $this->exportService->exportPdf($summary, $topSpending, $chartMonthly, $month, $categoryName);
    }

    public function debug(): void
    {
        $userId = session()->get('user_id');

        $db = \Config\Database::connect();

        $transactions = $db->table('transactions')
            ->where('user_id', $userId)
            ->get()->getResultArray();

        $filtered = $db->table('transactions')
            ->where('user_id', $userId)
            ->where("DATE_FORMAT(transaction_date, '%Y-%m')", '2026-06')
            ->get()->getResultArray();

        $types = $db->table('transactions')
            ->select('transaction_type, COUNT(*) as count')
            ->where('user_id', $userId)
            ->groupBy('transaction_type')
            ->get()->getResultArray();

        $dates = $db->table('transactions')
            ->select('id, transaction_date, transaction_type, amount')
            ->where('user_id', $userId)
            ->limit(5)
            ->get()->getResultArray();

        echo '<pre>';
        print_r([
            'user_id'           => $userId,
            'total_rows'        => count($transactions),
            'filtered_by_month' => count($filtered),
            'types'             => $types,
            'sample_dates'      => $dates,
        ]);
        echo '</pre>';
        die();
    }

    private function sanitizeMonth(?string $value): ?string
    {
        if ($value === null) return null;
        $value = trim($value);
        return $value !== '' ? $value : null;
    }

    private function sanitizeCategory(?string $value): ?string
    {
        if ($value === null) return null;
        $value = trim($value);
        if ($value === '' || $value === 'all' || $value === 'All') return null;
        return $value;
    }
}
