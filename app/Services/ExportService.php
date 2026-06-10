<?php

namespace App\Services;

use TCPDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Alignment, Border, Font};

class ExportService
{
    public function exportPdf(array $summary, array $topSpending, array $chartMonthly, ?string $month, ?string $category): void
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('FinanceApp');
        $pdf->SetAuthor('FinanceApp');
        $pdf->SetTitle('Financial Report');
        $pdf->SetSubject('Financial Report');

        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(10);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        $pdf->SetFooterFont(['dejavusans', '', 8]);   // ← Unicode font
        $pdf->setFooterData([0, 0, 0], [100, 100, 100]);

        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetFont('dejavusans', '', 10);          // ← Unicode font
        $pdf->AddPage();

        $filterLabel = '';
        if ($month)    $filterLabel .= 'Month: ' . date('F Y', strtotime($month . '-01')) . '  ';
        if ($category && $category !== 'All') $filterLabel .= 'Category: ' . $category;
        if (!$filterLabel) $filterLabel = 'All time / All categories';

        $html = $this->buildPdfHtml($summary, $topSpending, $chartMonthly, $filterLabel);

        $pdf->writeHTML($html, true, false, true, false, '');

        $filename = 'financial-report-' . date('Ymd-His') . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }
    private function buildPdfHtml(array $summary, array $topSpending, array $chartMonthly, string $filterLabel): string
    {
        $income   = number_format($summary['totalIncome'], 2);
        $expenses = number_format($summary['totalExpenses'], 2);
        $savings  = number_format($summary['netSavings'], 2);
        $rate     = $summary['savingsRate'];
        $generated = date('F d, Y h:i A');

        $topRows = '';
        foreach ($topSpending as $i => $item) {
            $bg    = $i % 2 === 0 ? '#f8f9fa' : '#ffffff';
            $total = number_format($item['total'], 2);
            $topRows .= "
            <tr style='background:{$bg}'>
                <td style='padding:8px 10px;'>" . htmlspecialchars(ucfirst($item['category'])) . "</td>
                <td style='padding:8px 10px;text-align:right;'>₱{$total}</td>
            </tr>
        ";
        }

        $monthlyRows = '';
        foreach ($chartMonthly['labels'] as $i => $label) {
            $bg  = $i % 2 === 0 ? '#f8f9fa' : '#ffffff';
            $inc = number_format($chartMonthly['income'][$i]   ?? 0, 2);
            $exp = number_format($chartMonthly['expenses'][$i] ?? 0, 2);
            $net = number_format(($chartMonthly['income'][$i] ?? 0) - ($chartMonthly['expenses'][$i] ?? 0), 2);
            $monthlyRows .= "
            <tr style='background:{$bg}'>
                <td style='padding:8px 10px;'>{$label}</td>
                <td style='padding:8px 10px;text-align:right;color:#198754;'>₱{$inc}</td>
                <td style='padding:8px 10px;text-align:right;color:#dc3545;'>₱{$exp}</td>
                <td style='padding:8px 10px;text-align:right;color:#0d6efd;'>₱{$net}</td>
            </tr>
        ";
        }

        return <<<HTML
    <style>
        body { font-family: dejavusans; font-size: 11px; color: #212529; }
        h1   { font-size: 20px; color: #212529; margin-bottom: 2px; }
        h2   { font-size: 13px; color: #495057; margin-top: 14px; margin-bottom: 6px; border-bottom: 1px solid #dee2e6; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th   { background: #212529; color: #ffffff; padding: 8px 10px; text-align: left; font-size: 10px; }
        td   { font-size: 10px; color: #212529; }
    </style>

    <h1>Financial Report</h1>
    <p style='color:#6c757d;font-size:9px;margin-top:0;'>Generated: {$generated} &nbsp;&bull;&nbsp; Filter: {$filterLabel}</p>

    <h2>Summary</h2>
    <table>
        <tr>
            <th>Total Income</th>
            <th>Total Expenses</th>
            <th>Net Savings</th>
            <th>Savings Rate</th>
        </tr>
        <tr style='background:#f8f9fa;'>
            <td style='padding:10px;color:#198754;font-size:13px;font-weight:bold;'>₱{$income}</td>
            <td style='padding:10px;color:#dc3545;font-size:13px;font-weight:bold;'>₱{$expenses}</td>
            <td style='padding:10px;color:#0d6efd;font-size:13px;font-weight:bold;'>₱{$savings}</td>
            <td style='padding:10px;font-size:13px;font-weight:bold;'>{$rate}%</td>
        </tr>
    </table>

    <h2>Monthly Breakdown</h2>
    <table>
        <tr>
            <th>Month</th>
            <th style='text-align:right;'>Income</th>
            <th style='text-align:right;'>Expenses</th>
            <th style='text-align:right;'>Net</th>
        </tr>
        {$monthlyRows}
    </table>

    <h2>Top Spending Categories</h2>
    <table>
        <tr>
            <th>Category</th>
            <th style='text-align:right;'>Total</th>
        </tr>
        {$topRows}
    </table>
    HTML;
    }

    public function exportXlsx(array $summary, array $topSpending, array $chartMonthly, ?string $month, ?string $category): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('FinanceApp')
            ->setTitle('Financial Report')
            ->setSubject('Financial Report');

        $this->buildSummarySheet($spreadsheet, $summary, $month, $category);
        $this->buildMonthlySheet($spreadsheet, $chartMonthly);
        $this->buildSpendingSheet($spreadsheet, $topSpending);

        $spreadsheet->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $filename = 'financial-report-' . date('Ymd-His') . '.xlsx';
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function buildSummarySheet(Spreadsheet $spreadsheet, array $summary, ?string $month, ?string $category): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Summary');

        $darkFill  = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '212529']];
        $lightFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']];
        $whiteFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']];
        $thinBorder = ['style' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']];
        $allBorders = ['allBorders' => $thinBorder];

        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Financial Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'name' => 'Arial'],
            'fill'      => $darkFill,
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $filterLabel = 'All time';
        if ($month)    $filterLabel  = 'Month: ' . date('F Y', strtotime($month . '-01'));
        if ($category && $category !== 'all') $filterLabel .= '  |  Category: ' . ucfirst($category);

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', 'Generated: ' . date('F d, Y h:i A') . '   |   Filter: ' . $filterLabel);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'color' => ['rgb' => '6C757D'], 'name' => 'Arial'],
            'fill'      => $whiteFill,
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $sheet->setCellValue('A4', 'Metric');
        $sheet->setCellValue('B4', 'Amount');

        $sheet->getStyle('A4:B4')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => $darkFill,
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => $allBorders,
        ]);

        $rows = [
            ['Total Income',   $summary['totalIncome'],   '198754'],
            ['Total Expenses', $summary['totalExpenses'], 'DC3545'],
            ['Net Savings',    $summary['netSavings'],    '0D6EFD'],
        ];

        foreach ($rows as $i => [$label, $value, $color]) {
            $r = $i + 5;
            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("B{$r}", $value);
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font'      => ['name' => 'Arial', 'size' => 11],
                'fill'      => $i % 2 === 0 ? $lightFill : $whiteFill,
                'borders'   => $allBorders,
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
            $sheet->getStyle("B{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => $color], 'name' => 'Arial', 'size' => 11],
                'fill'      => $i % 2 === 0 ? $lightFill : $whiteFill,
                'borders'   => $allBorders,
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                'numberFormat' => ['formatCode' => '₱#,##0.00'],
            ]);
        }

        $sheet->setCellValue('A8', 'Savings Rate');
        $sheet->setCellValue('B8', $summary['savingsRate'] / 100);
        $sheet->getStyle('A8')->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 11],
            'fill'      => $lightFill,
            'borders'   => $allBorders,
        ]);
        $sheet->getStyle('B8')->applyFromArray([
            'font'           => ['bold' => true, 'name' => 'Arial', 'size' => 11],
            'fill'           => $lightFill,
            'borders'        => $allBorders,
            'numberFormat'   => ['formatCode' => '0.0%'],
            'alignment'      => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(20);
    }

    private function buildMonthlySheet(Spreadsheet $spreadsheet, array $chartMonthly): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Monthly Breakdown');

        $darkFill  = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '212529']];
        $lightFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']];
        $whiteFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']];
        $thinBorder = ['style' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']];
        $allBorders = ['allBorders' => $thinBorder];

        $headers = ['Month', 'Income', 'Expenses', 'Net Savings'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
        }
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => $darkFill,
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => $allBorders,
        ]);

        foreach ($chartMonthly['labels'] as $i => $label) {
            $r   = $i + 2;
            $inc = $chartMonthly['income'][$i]   ?? 0;
            $exp = $chartMonthly['expenses'][$i] ?? 0;
            $net = $inc - $exp;
            $bg  = $i % 2 === 0 ? $lightFill : $whiteFill;

            $sheet->setCellValue("A{$r}", $label);
            $sheet->setCellValue("B{$r}", $inc);
            $sheet->setCellValue("C{$r}", $exp);
            $sheet->setCellValue("D{$r}", $net);

            $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                'font'    => ['name' => 'Arial'],
                'fill'    => $bg,
                'borders' => $allBorders,
            ]);
            $sheet->getStyle("B{$r}")->applyFromArray(['font' => ['color' => ['rgb' => '198754']], 'numberFormat' => ['formatCode' => '₱#,##0.00'], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);
            $sheet->getStyle("C{$r}")->applyFromArray(['font' => ['color' => ['rgb' => 'DC3545']], 'numberFormat' => ['formatCode' => '₱#,##0.00'], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);
            $sheet->getStyle("D{$r}")->applyFromArray(['font' => ['color' => ['rgb' => '0D6EFD'], 'bold' => true], 'numberFormat' => ['formatCode' => '₱#,##0.00'], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);
        }

        foreach (['A' => 16, 'B' => 18, 'C' => 18, 'D' => 18] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
    }

    private function buildSpendingSheet(Spreadsheet $spreadsheet, array $topSpending): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Top Spending');

        $darkFill  = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '212529']];
        $lightFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']];
        $whiteFill = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFFFF']];
        $thinBorder = ['style' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']];
        $allBorders = ['allBorders' => $thinBorder];

        $sheet->setCellValue('A1', 'Category');
        $sheet->setCellValue('B1', 'Total Spent');
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
            'fill'      => $darkFill,
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => $allBorders,
        ]);

        foreach ($topSpending as $i => $item) {
            $r  = $i + 2;
            $bg = $i % 2 === 0 ? $lightFill : $whiteFill;

            $sheet->setCellValue("A{$r}", ucfirst($item['category']));
            $sheet->setCellValue("B{$r}", $item['total']);

            $sheet->getStyle("A{$r}")->applyFromArray(['font' => ['name' => 'Arial'], 'fill' => $bg, 'borders' => $allBorders]);
            $sheet->getStyle("B{$r}")->applyFromArray([
                'font'         => ['name' => 'Arial', 'color' => ['rgb' => 'DC3545']],
                'fill'         => $bg,
                'borders'      => $allBorders,
                'numberFormat' => ['formatCode' => '₱#,##0.00'],
                'alignment'    => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);
        }

        $sheet->getColumnDimension('A')->setWidth(22);
        $sheet->getColumnDimension('B')->setWidth(20);
    }
}
