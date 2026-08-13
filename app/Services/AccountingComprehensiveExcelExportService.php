<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingComprehensiveExcelExportService
{
    protected array $colors = [
        'primary' => '2563EB',
        'primary_dark' => '1E40AF',
        'header' => '1E293B',
        'header_text' => 'FFFFFF',
        'accent_green' => '059669',
        'accent_red' => 'DC2626',
        'accent_amber' => 'D97706',
        'accent_violet' => '7C3AED',
        'subtitle' => 'F1F5F9',
        'alternate' => 'F8FAFC',
        'border' => 'CBD5E1',
        'kpi_bg' => 'EFF6FF',
    ];

    /**
     * @param  array<string, mixed>  $report
     */
    public function download(array $report, Carbon $start, Carbon $end): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Mindlytics Academy')
            ->setTitle('التحليل المالي الشامل')
            ->setSubject('إيرادات — مصروفات — تحصيلات');

        $this->buildDashboardSheet($spreadsheet->getActiveSheet(), $report, $start, $end);
        $this->buildRevenueSheet($spreadsheet->createSheet(), $report);
        $this->buildCollectionsSheet($spreadsheet->createSheet(), $report);
        $this->buildExpensesSheet($spreadsheet->createSheet(), $report);
        $this->buildMonthlySheet($spreadsheet->createSheet(), $report);
        $this->buildPaymentsDetailSheet($spreadsheet->createSheet(), $report);
        $this->buildExpensesDetailSheet($spreadsheet->createSheet(), $report);

        $spreadsheet->setActiveSheetIndex(0);

        $filenameAscii = 'Mindlytics_financial_analysis_'.$start->format('Y-m-d').'_'.$end->format('Y-m-d').'.xlsx';
        $filenameUtf8 = 'تحليل_مالي_شامل_'.$start->format('Y-m-d').'_'.$end->format('Y-m-d').'.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filenameAscii.'"; filename*=UTF-8\'\''.rawurlencode($filenameUtf8),
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildDashboardSheet(Worksheet $sheet, array $report, Carbon $start, Carbon $end): void
    {
        $sheet->setTitle('لوحة التحليل');
        $sheet->setRightToLeft(true);

        $summary = $report['summary'];
        $breakEven = $report['break_even'];

        $this->writeTitleBlock($sheet, 'Mindlytics — التحليل المالي الشامل', $start, $end, 'A1:H3');

        $kpis = [
            ['إجمالي الإيرادات المحصّلة', $summary['total_revenue'], $this->colors['accent_green']],
            ['إجمالي المصروفات', $summary['total_expenses'], $this->colors['accent_red']],
            ['صافي الربح / الخسارة', $summary['net_profit'], $summary['net_profit'] >= 0 ? $this->colors['accent_green'] : $this->colors['accent_red']],
            ['كورسات مسجّلة', $summary['recorded_course'] ?? 0, $this->colors['primary']],
            ['جروبات أونلاين', $summary['live_online_group'] ?? 0, $this->colors['accent_violet']],
            ['جروبات أوفلاين', $summary['live_offline_group'] ?? 0, $this->colors['accent_amber']],
            ['تحصيل بوابة', $summary['online_collections'], $this->colors['primary']],
            ['تحصيل نقدي/تحويل', $summary['offline_collections'], $this->colors['accent_violet']],
            ['عمولات البوابات', $summary['gateway_fees'], $this->colors['accent_amber']],
        ];

        $row = 5;
        $col = 1;
        foreach ($kpis as $i => [$label, $value, $color]) {
            if ($i > 0 && $i % 3 === 0) {
                $row += 4;
                $col = 1;
            }
            $startCol = chr(64 + $col);
            $endCol = chr(64 + $col + 1);
            $this->writeKpiCard($sheet, $startCol.$row, $endCol.($row + 2), $label, $value, $color);
            $col += 3;
        }

        $row += 8;
        $sheet->setCellValue('A'.$row, 'تحليل بر الأمان');
        $sheet->mergeCells('A'.$row.':D'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':D'.$row);
        $row++;

        $beRows = [
            ['إيرادات الفترة', $breakEven['revenue']],
            ['مصروفات من الإيراد', $breakEven['expenses_from_revenue']],
            ['مصروفات من جيب الشركة', $breakEven['expenses_out_of_pocket']],
            ['صافي تشغيلي', $breakEven['operational_net']],
            ['صافي حقيقي', $breakEven['true_net']],
            ['حالة بر الأمان', $breakEven['label']],
        ];
        foreach ($beRows as [$label, $value]) {
            $sheet->setCellValue('A'.$row, $label);
            $sheet->setCellValue('B'.$row, is_numeric($value) ? (float) $value : $value);
            $this->styleDataRow($sheet, 'A'.$row.':B'.$row, $row % 2 === 0);
            if (is_numeric($value)) {
                $sheet->getStyle('B'.$row)->getNumberFormat()->setFormatCode('#,##0.00" ج.م"');
            }
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A'.$row, 'توزيع الإيرادات حسب نوع المنتج');
        $sheet->mergeCells('A'.$row.':E'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':E'.$row);
        $row++;

        $headers = ['نوع المنتج', 'عدد العمليات', 'المبلغ', 'النسبة %'];
        $this->writeTableHeader($sheet, $row, $headers, 'A');
        $row++;

        $totalRev = max(0.01, (float) $summary['total_revenue']);
        foreach ($report['revenue_by_type'] as $item) {
            $pct = round(((float) $item['total'] / $totalRev) * 100, 1);
            $this->writeTableRow($sheet, $row, [
                $item['label'],
                $item['count'],
                (float) $item['total'],
                $pct.'%',
            ], 'A', $row % 2 === 0);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'E') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildRevenueSheet(Worksheet $sheet, array $report): void
    {
        $sheet->setTitle('الإيرادات بالتفصيل');
        $sheet->setRightToLeft(true);

        $row = 1;
        $sheet->setCellValue('A'.$row, 'تفصيل الإيرادات — من أين جاء كل جنيه');
        $sheet->mergeCells('A'.$row.':H'.$row);
        $this->styleMainTitle($sheet, 'A'.$row.':H'.$row);
        $row += 2;

        $headers = ['نوع الإيراد', 'المنتج / الخدمة', 'عدد', 'إجمالي', 'أونلاين', 'أوفلاين', 'نسبة من الإجمالي'];
        $this->writeTableHeader($sheet, $row, $headers, 'A');
        $row++;

        $totalRev = max(0.01, (float) $report['summary']['total_revenue']);
        foreach ($report['revenue_by_product'] as $item) {
            $pct = round(((float) $item['total'] / $totalRev) * 100, 1);
            $this->writeTableRow($sheet, $row, [
                $item['type_label'],
                $item['product_name'],
                $item['count'],
                (float) $item['total'],
                (float) $item['online'],
                (float) $item['offline'],
                $pct.'%',
            ], 'A', $row % 2 === 0);
            foreach (['D', 'E', 'F'] as $c) {
                $sheet->getStyle($c.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $row++;
        }

        $row += 2;
        $sheet->setCellValue('A'.$row, 'تقاطع المصدر × قناة التحصيل');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':F'.$row);
        $row++;

        $headers2 = ['نوع الإيراد', 'قناة التحصيل', 'عدد', 'المبلغ', 'نسبة'];
        $this->writeTableHeader($sheet, $row, $headers2, 'A');
        $row++;

        foreach ($report['revenue_by_type_channel'] as $item) {
            $pct = round(((float) $item['total'] / $totalRev) * 100, 1);
            $this->writeTableRow($sheet, $row, [
                $item['type_label'],
                $item['channel_label'],
                $item['count'],
                (float) $item['total'],
                $pct.'%',
            ], 'A', $row % 2 === 0);
            $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'H') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildCollectionsSheet(Worksheet $sheet, array $report): void
    {
        $sheet->setTitle('التحصيلات');
        $sheet->setRightToLeft(true);

        $summary = $report['summary'];
        $collections = $report['collections'];

        $row = 1;
        $sheet->setCellValue('A'.$row, 'تحليل التحصيلات — أونلاين vs أوفلاين');
        $sheet->mergeCells('A'.$row.':F'.$row);
        $this->styleMainTitle($sheet, 'A'.$row.':F'.$row);
        $row += 2;

        $this->writeKpiCard($sheet, 'A'.$row, 'B'.($row + 2), 'تحصيل أونلاين', $summary['online_collections'], $this->colors['primary']);
        $this->writeKpiCard($sheet, 'D'.$row, 'E'.($row + 2), 'تحصيل أوفلاين', $summary['offline_collections'], $this->colors['accent_violet']);
        $row += 4;

        $sheet->setCellValue('A'.$row, 'نسبة أونلاين: '.$summary['online_pct'].'%');
        $sheet->setCellValue('C'.$row, 'نسبة أوفلاين: '.$summary['offline_pct'].'%');
        $row += 2;

        $sheet->setCellValue('A'.$row, 'تفصيل البوابات (أونلاين)');
        $sheet->mergeCells('A'.$row.':D'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':D'.$row);
        $row++;

        $this->writeTableHeader($sheet, $row, ['البوابة', 'عدد', 'المبلغ', 'نسبة من أونلاين'], 'A');
        $row++;
        $onlineTotal = max(0.01, (float) $collections['online']['total']);
        foreach ($collections['online']['by_gateway'] as $gw) {
            $pct = round(((float) $gw['total'] / $onlineTotal) * 100, 1);
            $this->writeTableRow($sheet, $row, [$gw['label'], $gw['count'], (float) $gw['total'], $pct.'%'], 'A', $row % 2 === 0);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        $row += 2;
        $sheet->setCellValue('A'.$row, 'تفصيل طرق الدفع (أوفلاين / يدوي)');
        $sheet->mergeCells('A'.$row.':D'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':D'.$row);
        $row++;

        $this->writeTableHeader($sheet, $row, ['طريقة الدفع', 'عدد', 'المبلغ', 'نسبة من أوفلاين'], 'A');
        $row++;
        $offlineTotal = max(0.01, (float) $collections['offline']['total']);
        foreach ($collections['offline']['by_method'] as $method) {
            $pct = round(((float) $method['total'] / $offlineTotal) * 100, 1);
            $this->writeTableRow($sheet, $row, [$method['label'], $method['count'], (float) $method['total'], $pct.'%'], 'A', $row % 2 === 0);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        $row += 2;
        $sheet->setCellValue('A'.$row, 'تحصيلات الجروبات حسب قناة الحضور (أونلاين لايف vs حضور)');
        $sheet->mergeCells('A'.$row.':D'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':D'.$row);
        $row++;

        $this->writeTableHeader($sheet, $row, ['قناة الحضور', 'عدد', 'المبلغ'], 'A');
        $row++;
        $groupChannels = $collections['groups']['by_channel'] ?? $collections['offline_courses']['by_channel'] ?? [];
        foreach ($groupChannels as $channel => $data) {
            $label = $data['label'] ?? (is_string($channel) ? $channel : 'غير محدد');
            $this->writeTableRow($sheet, $row, [$label, $data['count'], (float) $data['total']], 'A', $row % 2 === 0);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildExpensesSheet(Worksheet $sheet, array $report): void
    {
        $sheet->setTitle('المصروفات');
        $sheet->setRightToLeft(true);

        $summary = $report['summary'];
        $row = 1;
        $sheet->setCellValue('A'.$row, 'تحليل المصروفات — إجمالي: '.number_format($summary['total_expenses'], 2).' ج.م');
        $sheet->mergeCells('A'.$row.':E'.$row);
        $this->styleMainTitle($sheet, 'A'.$row.':E'.$row);
        $row += 2;

        $sheet->setCellValue('A'.$row, 'حسب الفئة');
        $sheet->mergeCells('A'.$row.':D'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':D'.$row);
        $row++;

        $this->writeTableHeader($sheet, $row, ['الفئة', 'عدد', 'المبلغ', 'نسبة'], 'A');
        $row++;
        $totalExp = max(0.01, (float) $summary['total_expenses']);
        foreach ($report['expenses_by_category'] as $item) {
            $pct = round(((float) $item['total'] / $totalExp) * 100, 1);
            $this->writeTableRow($sheet, $row, [$item['label'], $item['count'], (float) $item['total'], $pct.'%'], 'A', $row % 2 === 0);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        $row += 2;
        $sheet->setCellValue('A'.$row, 'حسب مصدر التمويل');
        $sheet->mergeCells('A'.$row.':D'.$row);
        $this->styleSectionTitle($sheet, 'A'.$row.':D'.$row);
        $row++;

        $this->writeTableHeader($sheet, $row, ['مصدر التمويل', 'عدد', 'المبلغ', 'نسبة'], 'A');
        $row++;
        foreach ($report['expenses_by_funding'] as $item) {
            $pct = round(((float) $item['total'] / $totalExp) * 100, 1);
            $this->writeTableRow($sheet, $row, [$item['label'], $item['count'], (float) $item['total'], $pct.'%'], 'A', $row % 2 === 0);
            $sheet->getStyle('C'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        foreach (range('A', 'E') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildMonthlySheet(Worksheet $sheet, array $report): void
    {
        $sheet->setTitle('الاتجاه الشهري');
        $sheet->setRightToLeft(true);

        $monthly = $report['monthly'];
        $row = 1;
        $sheet->setCellValue('A'.$row, 'الاتجاه الشهري — إيراد vs مصروف vs صافي');
        $sheet->mergeCells('A'.$row.':E'.$row);
        $this->styleMainTitle($sheet, 'A'.$row.':E'.$row);
        $row += 2;

        $this->writeTableHeader($sheet, $row, ['الشهر', 'الإيرادات', 'المصروفات', 'الصافي', 'هامش %'], 'A');
        $row++;

        foreach ($monthly['labels'] as $i => $label) {
            $rev = (float) ($monthly['revenue'][$i] ?? 0);
            $exp = (float) ($monthly['expenses'][$i] ?? 0);
            $net = (float) ($monthly['net'][$i] ?? 0);
            $margin = $rev > 0 ? round(($net / $rev) * 100, 1).'%' : '—';
            $this->writeTableRow($sheet, $row, [$label, $rev, $exp, $net, $margin], 'A', $row % 2 === 0);
            foreach (['B', 'C', 'D'] as $c) {
                $sheet->getStyle($c.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $row++;
        }

        foreach (range('A', 'E') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildPaymentsDetailSheet(Worksheet $sheet, array $report): void
    {
        $sheet->setTitle('سجل التحصيلات');
        $sheet->setRightToLeft(true);

        $headers = [
            'رقم الدفعة', 'التاريخ', 'العميل', 'نوع المنتج', 'المنتج / الجروب', 'قناة الحضور',
            'قناة التحصيل', 'البوابة/الطريقة', 'المبلغ', 'عمولة', 'صافي', 'فاتورة', 'فرع', 'مرجع',
        ];
        $row = 1;
        $this->writeTableHeader($sheet, $row, $headers, 'A');
        $row++;

        foreach ($report['payment_rows'] as $p) {
            $this->writeTableRow($sheet, $row, [
                $p['payment_number'] ?? $p['payment_id'],
                $p['paid_at'],
                $p['client_name'],
                $p['revenue_type_label'],
                $p['product_name'],
                $p['enrollment_channel_label'] ?? '—',
                $p['channel_label'],
                $p['sub_channel_label'],
                (float) $p['amount'],
                (float) $p['gateway_fee'],
                (float) $p['net_amount'],
                $p['invoice_number'] ?? '—',
                $p['branch'] ?? '—',
                $p['reference'] ?? '—',
            ], 'A', $row % 2 === 0);
            foreach (['I', 'J', 'K'] as $c) {
                $sheet->getStyle($c.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $row++;
        }

        for ($c = 1; $c <= 14; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function buildExpensesDetailSheet(Worksheet $sheet, array $report): void
    {
        $sheet->setTitle('سجل المصروفات');
        $sheet->setRightToLeft(true);

        $headers = ['رقم المصروف', 'العنوان', 'الفئة', 'المبلغ', 'مصدر التمويل', 'طريقة الدفع', 'الموقع', 'التاريخ', 'أُنشئ بواسطة'];
        $row = 1;
        $this->writeTableHeader($sheet, $row, $headers, 'A');
        $row++;

        foreach ($report['expense_rows'] as $e) {
            $this->writeTableRow($sheet, $row, [
                $e['expense_number'] ?? '—',
                $e['title'],
                $e['category'],
                (float) $e['amount'],
                $e['funding_source'],
                $e['payment_method'],
                $e['location'] ?? '—',
                $e['expense_date'],
                $e['created_by'] ?? '—',
            ], 'A', $row % 2 === 0);
            $sheet->getStyle('D'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
            $row++;
        }

        for ($c = 1; $c <= 9; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
        }
    }

    protected function writeTitleBlock(Worksheet $sheet, string $title, Carbon $start, Carbon $end, string $range): void
    {
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells($range);
        $this->styleMainTitle($sheet, $range);
        $sheet->setCellValue('A4', 'الفترة: '.$start->format('Y-m-d').' → '.$end->format('Y-m-d').'  |  تاريخ التصدير: '.now()->format('Y-m-d H:i'));
        $sheet->mergeCells('A4:H4');
        $sheet->getStyle('A4')->getFont()->setSize(10)->getColor()->setRGB('64748B');
    }

    protected function writeKpiCard(Worksheet $sheet, string $startCell, string $endCell, string $label, float|string $value, string $accentColor): void
    {
        $sheet->mergeCells($startCell.':'.$endCell);
        $sheet->setCellValue($startCell, $label);
        $sheet->getStyle($startCell.':'.$endCell)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->colors['kpi_bg']]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $accentColor]]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getStyle($startCell)->getFont()->setBold(true)->setSize(10);

        [$col, $row] = [$startCell[0], (int) substr($startCell, 1)];
        $valueCell = $col.($row + 1);
        $sheet->mergeCells($valueCell.':'.($endCell[0]).($row + 1));
        $display = is_numeric($value) ? (float) $value : $value;
        $sheet->setCellValue($valueCell, $display);
        $sheet->getStyle($valueCell)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB($accentColor);
        if (is_numeric($value)) {
            $sheet->getStyle($valueCell)->getNumberFormat()->setFormatCode('#,##0.00" ج.م"');
        }
    }

    protected function styleMainTitle(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $this->colors['header_text']]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->colors['primary']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension((int) filter_var($range, FILTER_SANITIZE_NUMBER_INT))->setRowHeight(32);
    }

    protected function styleSectionTitle(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $this->colors['header_text']]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->colors['header']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    /**
     * @param  list<string>  $headers
     */
    protected function writeTableHeader(Worksheet $sheet, int $row, array $headers, string $startCol): void
    {
        $col = $startCol;
        foreach ($headers as $header) {
            $sheet->setCellValue($col.$row, $header);
            $sheet->getStyle($col.$row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $this->colors['header_text']]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $this->colors['primary_dark']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $this->colors['border']]]],
            ]);
            $col++;
        }
        $sheet->getRowDimension($row)->setRowHeight(28);
    }

    /**
     * @param  list<mixed>  $values
     */
    protected function writeTableRow(Worksheet $sheet, int $row, array $values, string $startCol, bool $alternate): void
    {
        $col = $startCol;
        foreach ($values as $value) {
            $sheet->setCellValue($col.$row, $value);
            $col++;
        }
        $endCol = chr(ord($startCol) + count($values) - 1);
        $this->styleDataRow($sheet, $startCol.$row.':'.$endCol.$row, $alternate);
    }

    protected function styleDataRow(Worksheet $sheet, string $range, bool $alternate): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $alternate ? $this->colors['alternate'] : 'FFFFFF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $this->colors['border']]]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
    }
}
