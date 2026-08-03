<?php

namespace App\Services;

use App\Models\SalesLead;
use Illuminate\Database\Eloquent\Builder;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesLeadsExcelExportService
{
    /** ألوان هوية المبيعات (زمردي) */
    private const EMERALD_950 = '022C22';

    private const EMERALD_800 = '065F46';

    private const EMERALD_700 = '047857';

    private const EMERALD_600 = '059669';

    private const EMERALD_500 = '10B981';

    private const EMERALD_100 = 'D1FAE5';

    private const EMERALD_50 = 'ECFDF5';

    private const SLATE_700 = '334155';

    private const SLATE_600 = '475569';

    private const WHITE = 'FFFFFF';

    private const BORDER = 'A7F3D0';

    private const ROSE_SOFT = 'FFE4E6';

    private const AMBER_SOFT = 'FEF3C7';

    public function buildSpreadsheet(Builder $query, bool $includeAssignee, string $contextLabel): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('العملاء المحتملون');
        $sheet->setRightToLeft(true);

        $lastColIndex = $includeAssignee ? 14 : 13;
        $lastCol = Coordinate::stringFromColumnIndex($lastColIndex);

        $statsQuery = clone $query;
        $totalRows = $statsQuery->count();
        $sumExpected = (clone $query)->whereNotNull('expected_value')->sum('expected_value');

        $logoPath = public_path('logo-removebg-preview.png');
        $logoEndCol = 'B';
        $titleStartCol = 'C';

        if ($logoPath && is_readable($logoPath)) {
            try {
                $drawing = new Drawing;
                $drawing->setName('Logo');
                $drawing->setPath($logoPath);
                $drawing->setHeight(72);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(8);
                $drawing->setOffsetY(6);
                $drawing->setWorksheet($sheet);
                $sheet->mergeCells('A1:B4');
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(4);
            } catch (\Throwable) {
                $titleStartCol = 'A';
                $logoEndCol = 'A';
            }
        } else {
            $titleStartCol = 'A';
            $logoEndCol = 'A';
        }

        if ($titleStartCol === 'A' && $logoEndCol === 'A') {
            $sheet->mergeCells("A1:{$lastCol}1");
            $titleRange = "A1:{$lastCol}1";
            $subRange = "A2:{$lastCol}2";
            $metaRange = "A3:{$lastCol}3";
            $statsRange = "A4:{$lastCol}4";
            $tableHeaderRow = 6;
        } else {
            $sheet->mergeCells("{$titleStartCol}1:{$lastCol}1");
            $sheet->mergeCells("{$titleStartCol}2:{$lastCol}2");
            $sheet->mergeCells("{$titleStartCol}3:{$lastCol}3");
            $sheet->mergeCells("{$titleStartCol}4:{$lastCol}4");
            $titleRange = "{$titleStartCol}1:{$lastCol}1";
            $subRange = "{$titleStartCol}2:{$lastCol}2";
            $metaRange = "{$titleStartCol}3:{$lastCol}3";
            $statsRange = "{$titleStartCol}4:{$lastCol}4";
            $tableHeaderRow = 6;
        }

        $sheet->setCellValue("{$titleStartCol}1", 'تقرير العملاء المحتملين');
        $sheet->getStyle($titleRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 22,
                'color' => ['rgb' => self::WHITE],
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => self::EMERALD_700],
                'endColor' => ['rgb' => self::EMERALD_500],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => self::EMERALD_950],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(44);

        $appName = config('app.name', 'Mindlytics');
        $sheet->setCellValue("{$titleStartCol}2", $appName . ' — مبيعات وتتبع المراحل');
        $sheet->getStyle($subRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => self::EMERALD_950],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::EMERALD_100],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::BORDER],
                ],
            ],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(26);

        $sheet->setCellValue("{$titleStartCol}3", $contextLabel . ' — تاريخ التصدير: ' . now()->format('Y-m-d H:i'));
        $sheet->getStyle($metaRange)->applyFromArray([
            'font' => [
                'size' => 10,
                'color' => ['rgb' => self::SLATE_600],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::WHITE],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        $sumFormatted = number_format((float) $sumExpected, 2, '.', ',');
        $sheet->setCellValue("{$titleStartCol}4", "إجمالي السجلات في التقرير: {$totalRows}     |     مجموع القيمة المتوقعة (الظاهر بالتصفية): {$sumFormatted} ج.م");
        $sheet->getStyle($statsRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => self::EMERALD_800],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::EMERALD_50],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::EMERALD_500],
                ],
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(24);

        $sheet->getRowDimension(5)->setRowHeight(8);

        $headers = $includeAssignee
            ? ['الاسم', 'مسند إلى', 'التصنيف', 'دفعة الاستيراد', 'الهاتف', 'البريد', 'الشركة', 'المصدر', 'المرحلة', 'الأولوية', 'قيمة متوقعة (ج.م)', 'متابعة تالية', 'آخر تواصل', 'تاريخ الإنشاء']
            : ['الاسم', 'التصنيف', 'دفعة الاستيراد', 'الهاتف', 'البريد', 'الشركة', 'المصدر', 'المرحلة', 'الأولوية', 'قيمة متوقعة (ج.م)', 'متابعة تالية', 'آخر تواصل', 'تاريخ الإنشاء'];

        $c = 1;
        foreach ($headers as $h) {
            $col = Coordinate::stringFromColumnIndex($c);
            $sheet->setCellValue($col . $tableHeaderRow, $h);
            $c++;
        }

        $headerRange = 'A' . $tableHeaderRow . ':' . $lastCol . $tableHeaderRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => self::WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::EMERALD_800],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::EMERALD_950],
                ],
            ],
        ]);
        $sheet->getRowDimension($tableHeaderRow)->setRowHeight(32);

        $valueCol = Coordinate::stringFromColumnIndex($includeAssignee ? 11 : 10);

        $rowNum = $tableHeaderRow + 1;
        $alternate = false;

        $dataQuery = $query->clone()->orderBy('id');
        if ($includeAssignee) {
            $dataQuery->with(['assignee', 'category']);
        } else {
            $dataQuery->with('category');
        }

        foreach ($dataQuery->cursor() as $l) {
            $expectedRaw = $l->expected_value;
            $expectedCell = $expectedRaw !== null ? (float) $expectedRaw : null;

            $values = $includeAssignee
                ? [
                    $l->name,
                    $l->assignee->name ?? '—',
                    $l->category?->name ?? '—',
                    $l->import_batch ?? '—',
                    $l->phone,
                    $l->email,
                    $l->company,
                    SalesLead::sourceLabel($l->source),
                    SalesLead::stageLabel($l->stage),
                    SalesLead::priorityLabel($l->priority),
                    $expectedCell,
                    $l->next_follow_up_at?->format('Y-m-d H:i'),
                    $l->last_contacted_at?->format('Y-m-d H:i'),
                    $l->created_at->format('Y-m-d H:i'),
                ]
                : [
                    $l->name,
                    $l->category?->name ?? '—',
                    $l->import_batch ?? '—',
                    $l->phone,
                    $l->email,
                    $l->company,
                    SalesLead::sourceLabel($l->source),
                    SalesLead::stageLabel($l->stage),
                    SalesLead::priorityLabel($l->priority),
                    $expectedCell,
                    $l->next_follow_up_at?->format('Y-m-d H:i'),
                    $l->last_contacted_at?->format('Y-m-d H:i'),
                    $l->created_at->format('Y-m-d H:i'),
                ];

            $c = 1;
            foreach ($values as $val) {
                $col = Coordinate::stringFromColumnIndex($c);
                if ($col === $valueCol && $val === null) {
                    $sheet->setCellValue($col . $rowNum, '—');
                } else {
                    $sheet->setCellValue($col . $rowNum, $val ?? '');
                }
                $c++;
            }

            $bg = $alternate ? self::EMERALD_50 : self::WHITE;
            $range = 'A' . $rowNum . ':' . $lastCol . $rowNum;
            $sheet->getStyle($range)->applyFromArray([
                'font' => [
                    'size' => 10,
                    'color' => ['rgb' => self::SLATE_700],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $bg],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => self::BORDER],
                    ],
                ],
            ]);

            if ($expectedRaw !== null) {
                $sheet->getStyle($valueCol . $rowNum)->getNumberFormat()->setFormatCode('#,##0.00');
            } else {
                $sheet->getStyle($valueCol . $rowNum)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            }

            $pr = $l->priority ?? 'normal';
            $prioCol = Coordinate::stringFromColumnIndex($includeAssignee ? 10 : 9);
            if ($pr === 'urgent') {
                $sheet->getStyle($prioCol . $rowNum)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::ROSE_SOFT],
                    ],
                    'font' => ['bold' => true, 'color' => ['rgb' => '9F1239']],
                ]);
            } elseif ($pr === 'high') {
                $sheet->getStyle($prioCol . $rowNum)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => self::AMBER_SOFT],
                    ],
                    'font' => ['bold' => true],
                ]);
            }

            if ($l->isOpen() && $l->isFollowUpOverdue()) {
                $followCol = Coordinate::stringFromColumnIndex($includeAssignee ? 12 : 11);
                $sheet->getStyle($followCol . $rowNum)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'BE123C'],
                    ],
                ]);
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
            $alternate = ! $alternate;
        }

        if ($totalRows === 0) {
            $sheet->setCellValue('A' . $rowNum, 'لا توجد بيانات مطابقة للتصفية الحالية.');
            $sheet->mergeCells('A' . $rowNum . ':' . $lastCol . $rowNum);
            $sheet->getStyle('A' . $rowNum)->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => self::SLATE_600]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $rowNum++;
        }

        $sheet->setAutoFilter('A' . $tableHeaderRow . ':' . $lastCol . $tableHeaderRow);
        $sheet->freezePane('A' . ($tableHeaderRow + 1));

        for ($i = 1; $i <= $lastColIndex; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getHeaderFooter()->setOddFooter('&L&' . $appName . ' &R& صفحة &P / &N');

        return $spreadsheet;
    }

    public function buildImportTemplate(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('قالب الاستيراد');
        $sheet->setRightToLeft(true);

        $headers = ['الاسم', 'الهاتف', 'البريد', 'الشركة', 'نوع الاهتمام', 'تفاصيل الاهتمام', 'القيمة', 'ملاحظات', 'الأولوية'];
        foreach ($headers as $i => $header) {
            $col = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue($col.'1', $header);
        }

        $sheet->setCellValue('A2', 'أحمد محمد');
        $sheet->setCellValue('B2', '01001234567');
        $sheet->setCellValue('C2', 'ahmed@example.com');
        $sheet->setCellValue('D2', 'شركة مثال');
        $sheet->setCellValue('E2', 'courses');
        $sheet->setCellValue('F2', 'دورة Laravel');
        $sheet->setCellValue('G2', 5000);
        $sheet->setCellValue('H2', 'عميل مهتم');
        $sheet->setCellValue('I2', 'عادي');

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_700]],
        ]);

        for ($i = 1; $i <= 9; $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function writeToOutput(Spreadsheet $spreadsheet): void
    {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
