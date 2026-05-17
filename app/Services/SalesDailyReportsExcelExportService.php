<?php

namespace App\Services;

use App\Models\SalesDailyReport;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesDailyReportsExcelExportService
{
    /**
     * @param  Collection<int, SalesDailyReport>  $reports
     */
    public function buildSpreadsheet(Collection $reports, string $context): Spreadsheet
    {
        $ss = new Spreadsheet();
        $ss->removeSheetByIndex(0);

        $summary = new Worksheet($ss, 'ملخص التقارير');
        $ss->addSheet($summary);
        $summary->setRightToLeft(true);
        $this->fillSummarySheet($summary, $reports, $context);

        $activity = new Worksheet($ss, 'النشاط اليومي');
        $ss->addSheet($activity);
        $activity->setRightToLeft(true);
        $this->fillReportsSheet($activity, $reports, 'activity');

        $productivity = new Worksheet($ss, 'الإنتاجية');
        $ss->addSheet($productivity);
        $productivity->setRightToLeft(true);
        $this->fillReportsSheet($productivity, $reports, 'productivity');

        $contacts = new Worksheet($ss, 'المكالمات والاجتماعات');
        $ss->addSheet($contacts);
        $contacts->setRightToLeft(true);
        $this->fillContactsSheet($contacts, $reports);

        $problems = new Worksheet($ss, 'مشاكل العملاء');
        $ss->addSheet($problems);
        $problems->setRightToLeft(true);
        $this->fillProblemsPatternsSheet($problems, $reports);

        $ss->setActiveSheetIndex(0);

        return $ss;
    }

    public function writeToOutput(Spreadsheet $spreadsheet): void
    {
        (new Xlsx($spreadsheet))->save('php://output');
    }

    /**
     * @param  Collection<int, SalesDailyReport>  $reports
     */
    private function fillSummarySheet(Worksheet $sheet, Collection $reports, string $context): void
    {
        $sheet->setCellValue('A1', 'تقارير يومية — موظفو المبيعات');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->setCellValue('A2', $context);
        $sheet->mergeCells('A2:F2');

        $headers = ['التاريخ', 'الموظف', 'الحالة', 'رسائل', 'مؤهلون', 'حجوزات', 'مكالمات', 'اجتماعات', 'خصم تلقائي'];
        $col = 'A';
        $row = 4;
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        $this->styleHeader($sheet, 'A4:I4');

        $row = 5;
        foreach ($reports as $r) {
            $sheet->setCellValue('A'.$row, $r->report_date->format('Y-m-d'));
            $sheet->setCellValue('B'.$row, $r->user->name ?? '—');
            $sheet->setCellValue('C'.$row, $r->isSubmitted() ? 'مسلّم' : 'مسودة/ناقص');
            $sheet->setCellValue('D'.$row, $r->messages_replied ?? '—');
            $sheet->setCellValue('E'.$row, $r->leads_qualified ?? '—');
            $sheet->setCellValue('F'.$row, $r->bookings_from_leads ?? '—');
            $sheet->setCellValue('G'.$row, $r->calls_made ?? '—');
            $sheet->setCellValue('H'.$row, $r->meetings_held ?? '—');
            $sheet->setCellValue('I'.$row, $r->auto_deduction_id ? 'نعم' : 'لا');
            $row++;
        }
        foreach (range('A', 'I') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    /**
     * @param  Collection<int, SalesDailyReport>  $reports
     */
    private function fillReportsSheet(Worksheet $sheet, Collection $reports, string $mode): void
    {
        if ($mode === 'activity') {
            $headers = ['التاريخ', 'الموظف', 'ردود رسائل', 'مؤهلون', 'حجوزات', 'ملاحظات النشاط'];
        } else {
            $headers = ['التاريخ', 'الموظف', 'أرقام', 'متابعات', 'مكالمات', 'اجتماعات', 'ردود مكالمات', 'ملاحظات الإنتاجية'];
        }

        $col = 'A';
        $row = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$row, $h);
            $col++;
        }
        $lastCol = chr(ord('A') + count($headers) - 1);
        $this->styleHeader($sheet, "A1:{$lastCol}1");

        $row = 2;
        foreach ($reports as $r) {
            if ($mode === 'activity') {
                $sheet->fromArray([
                    $r->report_date->format('Y-m-d'),
                    $r->user->name ?? '',
                    $r->messages_replied,
                    $r->leads_qualified,
                    $r->bookings_from_leads,
                    $r->activity_notes,
                ], null, 'A'.$row);
            } else {
                $sheet->fromArray([
                    $r->report_date->format('Y-m-d'),
                    $r->user->name ?? '',
                    $r->numbers_worked,
                    $r->followups_done,
                    $r->calls_made,
                    $r->meetings_held,
                    $r->calls_answered,
                    $r->productivity_notes,
                ], null, 'A'.$row);
            }
            $row++;
        }
    }

    /**
     * @param  Collection<int, SalesDailyReport>  $reports
     */
    private function fillContactsSheet(Worksheet $sheet, Collection $reports): void
    {
        $headers = ['التاريخ', 'الموظف', 'النوع', 'العميل', 'الهاتف', 'Lead', 'حالة العميل', 'المشاكل'];
        $col = 'A';
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65 + $i).'1', $h);
        }
        $this->styleHeader($sheet, 'A1:H1');

        $row = 2;
        foreach ($reports as $r) {
            foreach ($r->contacts as $c) {
                $sheet->fromArray([
                    $r->report_date->format('Y-m-d'),
                    $r->user->name ?? '',
                    $c->interactionTypeLabel(),
                    $c->contact_name,
                    $c->contact_phone,
                    $c->lead?->name,
                    $c->client_status,
                    $c->client_problems,
                ], null, 'A'.$row);
                $row++;
            }
        }
    }

    /**
     * تجميع تكرار كلمات/عبارات المشاكل لمساعدة الإدارة على اكتشاف الأنماط.
     *
     * @param  Collection<int, SalesDailyReport>  $reports
     */
    private function fillProblemsPatternsSheet(Worksheet $sheet, Collection $reports): void
    {
        $sheet->setCellValue('A1', 'نص المشكلة / الاحتياج (مجمّع)');
        $sheet->setCellValue('B1', 'عدد التكرار');
        $sheet->setCellValue('C1', 'أمثلة موظفين');
        $this->styleHeader($sheet, 'A1:C1');

        $freq = [];
        foreach ($reports as $r) {
            foreach ($r->contacts as $c) {
                $text = trim(mb_strtolower((string) $c->client_problems));
                if ($text === '') {
                    continue;
                }
                $key = mb_substr($text, 0, 120);
                if (! isset($freq[$key])) {
                    $freq[$key] = ['count' => 0, 'reps' => []];
                }
                $freq[$key]['count']++;
                $freq[$key]['reps'][$r->user->name ?? ''] = true;
            }
        }

        uasort($freq, fn ($a, $b) => $b['count'] <=> $a['count']);

        $row = 2;
        foreach ($freq as $text => $meta) {
            $sheet->setCellValue('A'.$row, $text);
            $sheet->setCellValue('B'.$row, $meta['count']);
            $sheet->setCellValue('C'.$row, implode('، ', array_keys($meta['reps'])));
            $row++;
        }
    }

    private function styleHeader(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}
