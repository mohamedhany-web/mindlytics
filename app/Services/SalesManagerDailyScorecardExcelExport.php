<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesManagerDailyScorecardExcelExport
{
    /**
     * @param  array<string, mixed>  $board
     */
    public function download(array $board): StreamedResponse
    {
        $spreadsheet = $this->build($board);
        $date = $board['date'] instanceof Carbon ? $board['date'] : Carbon::parse($board['date']);
        $filename = 'sales-manager-scorecard-'.$date->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    public function build(array $board): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $this->summarySheet($spreadsheet, $board);
        $this->employeesSheet($spreadsheet, $board);
        $this->channelsSheet($spreadsheet, $board);
        $this->attendanceSheet($spreadsheet, $board);
        $this->exceptionsSheet($spreadsheet, $board);
        $this->activitiesSheet($spreadsheet, $board);
        $this->leadsSheet($spreadsheet, $board);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function summarySheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Summary');
        $date = $board['date'] instanceof Carbon ? $board['date']->format('Y-m-d') : (string) $board['date'];
        $s = $board['summary'];

        $rows = [
            ['مركز رقابة مدير المبيعات'],
            ['الفريق', $board['team']->name ?? '—'],
            ['التاريخ', $date],
            ['عدد الأعضاء', $s['members'] ?? 0],
            ['متوسط الدرجة', $s['avg_score'] ?? 0],
            ['تحت المستوى', $s['below_target'] ?? 0],
            ['حرج', $s['critical'] ?? 0],
            ['محاولات اتصال', $s['call_attempts'] ?? 0],
            ['تم الرد', $s['calls_answered'] ?? 0],
            ['مدفوع مؤكد', $s['finance_verified_paid'] ?? 0],
            ['تقارير مسلّمة', $s['reports_submitted'] ?? 0],
            ['استثناءات', $s['exceptions_total'] ?? 0],
            ['مراجعات معتمدة', $s['approved_reviews'] ?? 0],
            ['ملاحظة', 'الدرجة تعتمد على نشاط CRM المرتبط بعميل فقط. لا خصم تلقائي.'],
        ];

        foreach ($rows as $i => $row) {
            $sheet->fromArray($row, null, 'A'.($i + 1));
        }
        $this->headerStyle($sheet, 'A1');
        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(40);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function employeesSheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Employees');
        $headers = [
            'الموظف', 'الدرجة', 'نتائج', 'نشاط', 'جودة', 'CRM', 'حضور',
            'محاولات', 'ردود', 'مؤهل', 'مدفوع CRM', 'مدفوع مؤكد',
            'تقرير', 'مراجعة', 'توصية',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $this->headerStyle($sheet, 'A1:O1');

        $r = 2;
        foreach ($board['rows'] as $row) {
            $sheet->fromArray([
                $row['name'],
                $row['verified_score'],
                $row['pillars']['results']['score'] ?? 0,
                $row['pillars']['activity']['score'] ?? 0,
                $row['pillars']['quality']['score'] ?? 0,
                $row['pillars']['crm_discipline']['score'] ?? 0,
                $row['pillars']['attendance']['score'] ?? 0,
                $row['sos']['call_attempts_daily'] ?? 0,
                $row['sos']['calls_answered_daily'] ?? 0,
                $row['sos']['qualified_conversations_daily'] ?? 0,
                $row['financial']['crm_declared_paid'] ?? 0,
                $row['financial']['finance_verified_paid'] ?? 0,
                $row['daily_report_submitted'] ? 'مسلّم' : 'ناقص',
                $row['review']?->statusLabel() ?? '—',
                $row['review']?->recommendationLabel() ?? ($row['suggested_recommendation'] ?? '—'),
            ], null, 'A'.$r);
            $r++;
        }
        $this->autoWidth($sheet, 15);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function channelsSheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Channels');
        $sheet->fromArray([
            'الموظف', 'واتساب CRM', 'واتساب مرتبط', 'واتساب غير مرتبط',
            'سوشيال مرتبط', 'سوشيال غير مرتبط', 'متابعات', 'كولد مستلم', 'كولد معمول', '% كولد',
        ], null, 'A1');
        $this->headerStyle($sheet, 'A1:J1');
        $r = 2;
        foreach ($board['rows'] as $row) {
            $sheet->fromArray([
                $row['name'],
                $row['channels']['whatsapp_crm'] ?? 0,
                $row['channels']['whatsapp_outbound_linked'] ?? 0,
                $row['channels']['whatsapp_outbound_unlinked'] ?? 0,
                $row['channels']['meta_outbound_linked'] ?? 0,
                $row['channels']['meta_outbound_unlinked'] ?? 0,
                $row['channels']['followups'] ?? 0,
                $row['cold']['assigned_today'] ?? 0,
                $row['cold']['worked_today'] ?? 0,
                $row['cold']['worked_pct'] ?? '—',
            ], null, 'A'.$r);
            $r++;
        }
        $this->autoWidth($sheet, 10);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function attendanceSheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Attendance');
        $sheet->fromArray(['الموظف', 'الحالة', 'حضور', 'تأخير', 'غياب', 'دقائق عمل', 'انقطاع د', 'تقرير'], null, 'A1');
        $this->headerStyle($sheet, 'A1:H1');
        $r = 2;
        foreach ($board['rows'] as $row) {
            $a = $row['attendance'];
            $sheet->fromArray([
                $row['name'],
                $a['status'] ?? '—',
                ($a['clocked_in'] ?? false) ? 'نعم' : 'لا',
                ($a['is_late'] ?? false) ? 'نعم' : 'لا',
                ($a['is_absent'] ?? false) ? 'نعم' : 'لا',
                $a['worked_minutes'] ?? 0,
                $a['offline_minutes'] ?? 0,
                $row['daily_report_submitted'] ? 'مسلّم' : 'ناقص',
            ], null, 'A'.$r);
            $r++;
        }
        $this->autoWidth($sheet, 8);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function exceptionsSheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Exceptions');
        $sheet->fromArray(['الموظف', 'الكود', 'الوصف', 'العدد'], null, 'A1');
        $this->headerStyle($sheet, 'A1:D1');
        $r = 2;
        foreach ($board['exceptions'] as $ex) {
            $sheet->fromArray([
                $ex['employee_name'] ?? '—',
                $ex['code'] ?? '',
                $ex['label'] ?? '',
                $ex['count'] ?? 0,
            ], null, 'A'.$r);
            $r++;
        }
        if ($r === 2) {
            $sheet->fromArray(['—', '—', 'لا استثناءات', 0], null, 'A2');
        }
        $this->autoWidth($sheet, 4);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function activitiesSheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Activities');
        $sheet->fromArray(['الموظف', 'الوقت', 'النوع', 'العميل', 'النتيجة'], null, 'A1');
        $this->headerStyle($sheet, 'A1:E1');
        $r = 2;
        foreach ($board['rows'] as $row) {
            foreach ($row['activities'] as $act) {
                $sheet->fromArray([
                    $row['name'],
                    $act->created_at?->format('Y-m-d H:i') ?? '',
                    \App\Models\SalesActivity::typeLabel($act->type),
                    $act->lead?->name ?? '—',
                    \App\Models\SalesActivity::outcomeLabel($act->outcome),
                ], null, 'A'.$r);
                $r++;
                if ($r > 5000) {
                    break 2;
                }
            }
        }
        $this->autoWidth($sheet, 5);
    }

    /**
     * @param  array<string, mixed>  $board
     */
    private function leadsSheet(Spreadsheet $spreadsheet, array $board): void
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Leads');
        $sheet->fromArray(['الموظف', 'العميل', 'الهاتف', 'المرحلة', 'المصدر', 'كولد'], null, 'A1');
        $this->headerStyle($sheet, 'A1:F1');
        $r = 2;
        foreach ($board['rows'] as $row) {
            foreach ($row['leads_touched'] as $lead) {
                $sheet->fromArray([
                    $row['name'],
                    $lead->name,
                    $lead->phone,
                    $lead->stage,
                    $lead->source,
                    $lead->import_batch ? 'نعم' : 'لا',
                ], null, 'A'.$r);
                $r++;
                if ($r > 5000) {
                    break 2;
                }
            }
        }
        $this->autoWidth($sheet, 6);
    }

    private function headerStyle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
        ]);
    }

    private function autoWidth($sheet, int $cols): void
    {
        for ($i = 1; $i <= $cols; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
        }
    }
}
