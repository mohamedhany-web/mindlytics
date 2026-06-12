<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesEmployeeDailyLeadsExcelExportService
{
    private const EMERALD_950 = '022C22';
    private const EMERALD_800 = '065F46';
    private const EMERALD_700 = '047857';
    private const EMERALD_500 = '10B981';
    private const EMERALD_100 = 'D1FAE5';
    private const EMERALD_50 = 'ECFDF5';
    private const SLATE_700 = '334155';
    private const SLATE_600 = '475569';
    private const WHITE = 'FFFFFF';
    private const BORDER = 'A7F3D0';

    /**
     * @param  array{
     *   rep_name: string,
     *   date_from: string,
     *   date_to: string,
     *   lead_scope_label: string,
     *   context: string,
     *   daily_rows: list<array{date: string, leads: int, activities: int, leads_created_by_rep: int, leads_transferred_from_admin: int}>,
     *   leads: Collection<int, SalesLead>,
     *   activities: Collection<int, SalesActivity>
     * } $payload
     */
    public function buildSpreadsheet(array $payload): Spreadsheet
    {
        $ss = new Spreadsheet();
        $ss->removeSheetByIndex(0);

        $this->addSummarySheet($ss, $payload);
        $this->addLeadsSheet($ss, $payload);

        $ss->setActiveSheetIndex(0);

        return $ss;
    }

    public function writeToOutput(Spreadsheet $spreadsheet): void
    {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addSummarySheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'ملخص يومي');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        $lastCol = 'G';
        $this->attachLogo($sheet);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'تقرير أعمال يومي (Leads) — إدارة المبيعات');
        $this->styleTitleBand($sheet, "A1:{$lastCol}1");

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', $payload['context']);
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE_600]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 4;
        $pairs = [
            'الموظف' => (string) ($payload['rep_name'] ?? '—'),
            'من تاريخ' => (string) ($payload['date_from'] ?? ''),
            'إلى تاريخ' => (string) ($payload['date_to'] ?? ''),
            'فلتر الـ Leads' => (string) ($payload['lead_scope_label'] ?? ''),
            'إجمالي Leads في الملف' => (string) ($payload['leads']->count() ?? 0),
            'إجمالي أنشطة CRM في الفترة' => (string) ($payload['activities']->count() ?? 0),
        ];

        foreach ($pairs as $label => $value) {
            $sheet->setCellValue('B'.$row, $label);
            $sheet->setCellValue('D'.$row, $value);
            $sheet->mergeCells('D'.$row.':'.$lastCol.$row);
            $sheet->getStyle('B'.$row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => self::EMERALD_800]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_50]],
            ]);
            $sheet->getStyle('D'.$row)->applyFromArray([
                'font' => ['color' => ['rgb' => self::SLATE_700]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);
            $row++;
        }

        $row += 1;
        $sheet->mergeCells("A{$row}:{$lastCol}{$row}");
        $sheet->setCellValue("A{$row}", 'تفصيل يومي (عدد Leads + الأنشطة)');
        $this->styleHeaderRow($sheet, "A{$row}:{$lastCol}{$row}");
        $row++;

        $headers = ['اليوم', 'Leads (حسب الفلتر)', 'أنشطة CRM', 'Leads أنشأها الموظف', 'Leads محولة من الإدارة', 'ملاحظة', ''];
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $h);
            $col++;
        }
        $this->styleHeaderRow($sheet, "A{$row}:{$lastCol}{$row}");
        $row++;

        $alternate = false;
        foreach (($payload['daily_rows'] ?? []) as $r) {
            $values = [
                (string) ($r['date'] ?? ''),
                (int) ($r['leads'] ?? 0),
                (int) ($r['activities'] ?? 0),
                (int) ($r['leads_created_by_rep'] ?? 0),
                (int) ($r['leads_transferred_from_admin'] ?? 0),
                '',
                '',
            ];

            $col = 1;
            foreach ($values as $v) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $v);
                $col++;
            }

            $bg = $alternate ? self::EMERALD_50 : self::WHITE;
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            $alternate = ! $alternate;
            $row++;
        }

        foreach (range('A', $lastCol) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->freezePane('A9');
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addLeadsSheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'بيانات الـ Leads');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        $headers = [
            'ID',
            'المسند إليه',
            'أنشئ بواسطة',
            'الاسم',
            'الهاتف',
            'البريد',
            'الشركة',
            'المصدر',
            'المرحلة',
            'الأولوية',
            'الاهتمام',
            'قيمة متوقعة',
            'متابعة تالية',
            'آخر تواصل',
            'سبب الخسارة',
            'ملاحظات',
            'تاريخ الإنشاء',
            'آخر تحديث',
            'تاريخ الإغلاق',
        ];

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'كل الـ Leads حسب الفلتر المختار (بكافة البيانات)');
        $this->styleTitleBand($sheet, "A1:{$lastCol}1");

        $row = 3;
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $h);
            $col++;
        }
        $this->styleHeaderRow($sheet, "A{$row}:{$lastCol}{$row}");
        $row++;

        $alternate = false;
        /** @var SalesLead $lead */
        foreach ($payload['leads'] as $lead) {
            $values = [
                (int) $lead->id,
                $lead->assignee->name ?? '—',
                $lead->creator->name ?? '—',
                $lead->name,
                $lead->phone,
                $lead->email,
                $lead->company,
                SalesLead::sourceLabel($lead->source),
                SalesLead::stageLabel($lead->stage),
                SalesLead::priorityLabel($lead->priority),
                $lead->interest,
                $lead->expected_value !== null ? (float) $lead->expected_value : null,
                $lead->next_follow_up_at?->format('Y-m-d H:i'),
                $lead->last_contacted_at?->format('Y-m-d H:i'),
                $lead->lost_reason,
                $lead->notes,
                $lead->created_at?->format('Y-m-d H:i'),
                $lead->updated_at?->format('Y-m-d H:i'),
                $lead->closed_at?->format('Y-m-d H:i'),
            ];

            $col = 1;
            foreach ($values as $v) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $v ?? '');
                $col++;
            }

            $bg = $alternate ? self::EMERALD_50 : self::WHITE;
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]]],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE_700]],
            ]);

            $sheet->getRowDimension($row)->setRowHeight(24);
            $alternate = ! $alternate;
            $row++;
        }

        if (($payload['leads'] ?? collect())->isEmpty()) {
            $sheet->setCellValue('A'.$row, 'لا توجد بيانات مطابقة للتصفية الحالية.');
            $sheet->mergeCells('A'.$row.':'.$lastCol.$row);
            $sheet->getStyle('A'.$row)->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => self::SLATE_600]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        $sheet->setAutoFilter("A3:{$lastCol}3");
        $sheet->freezePane('A4');

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
    }

    private function styleTitleBand(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => self::WHITE]],
            'fill' => [
                'fillType' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => ['rgb' => self::EMERALD_700],
                'endColor' => ['rgb' => self::EMERALD_500],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::EMERALD_950]],
            ],
        ]);
    }

    private function styleHeaderRow(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_800]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::EMERALD_950]],
            ],
        ]);
    }

    private function attachLogo(Worksheet $sheet): void
    {
        $logoPath = public_path('logo-removebg-preview.png');
        if (! $logoPath || ! is_readable($logoPath)) {
            return;
        }

        try {
            $drawing = new Drawing;
            $drawing->setName('Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(56);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(6);
            $drawing->setOffsetY(4);
            $drawing->setWorksheet($sheet);
        } catch (\Throwable) {
            // ignore
        }
    }
}

