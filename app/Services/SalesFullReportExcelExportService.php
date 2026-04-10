<?php

namespace App\Services;

use App\Models\SalesActivity;
use App\Models\SalesLead;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SalesFullReportExcelExportService
{
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

    /**
     * @param  array{
     *   mode: string,
     *   rep: \App\Models\User|null,
     *   start: Carbon,
     *   end: Carbon,
     *   period_report: array<string, mixed>|null,
     *   rep_summaries: list<array{user: \App\Models\User, report: array<string, mixed>}>,
     *   leads: Collection,
     *   activities: Collection,
     *   audit_logs: Collection,
     *   created_by_leads_count: int|null,
     *   context: string
     * }  $payload
     */
    public function buildSpreadsheet(array $payload): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->removeSheetByIndex(0);

        $this->addCoverSheet($spreadsheet, $payload);
        $this->addKpiSheet($spreadsheet, $payload);
        $this->addLeadsSheet($spreadsheet, $payload);
        $this->addActivitiesSheet($spreadsheet, $payload);
        $this->addAuditSheet($spreadsheet, $payload);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public function writeToOutput(Spreadsheet $spreadsheet): void
    {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addCoverSheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'الملخص');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        $lastCol = 'H';
        $this->attachLogo($sheet, $lastCol);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'تقرير المبيعات الشامل');
        $this->styleTitleBand($sheet, "A1:{$lastCol}1");

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', config('app.name', 'Mindlytics').' — لوحة الإدارة');
        $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => self::EMERALD_950]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_100]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', $payload['context']);
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE_600]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $from = $payload['start']->format('Y-m-d');
        $to = $payload['end']->format('Y-m-d');
        $scope = $payload['mode'] === 'single' && $payload['rep']
            ? 'موظف: '.$payload['rep']->name
            : 'جميع موظفي المبيعات النشطين';

        $row = 5;
        $pairs = [
            'نطاق التقرير' => $scope,
            'من تاريخ' => $from,
            'إلى تاريخ' => $to,
            'عدد أيام الفترة' => (string) (max(1, (int) $payload['start']->diffInDays($payload['end']) + 1)),
        ];

        if ($payload['mode'] === 'single' && $payload['period_report']) {
            $m = $payload['period_report']['metrics'];
            $pairs['عميل محتمل لمسهم في الفترة'] = (string) $payload['leads']->count();
            $pairs['أنشطة CRM في الفترة'] = (string) $payload['activities']->count();
            $pairs['سجلات النظام (مبيعات)'] = (string) $payload['audit_logs']->count();
            $pairs['عملاء أنشأهم الموظف في الفترة'] = (string) ($payload['created_by_leads_count'] ?? 0);
            $pairs['إيرادات مغلقة (فوز) في الفترة'] = number_format((float) ($m['revenue_closed'] ?? 0), 2, '.', ',').' ج.م';
            $pairs['صفقات فوز في الفترة'] = (string) ($m['won_closed'] ?? 0);
            $pairs['Leads جديدة (إنشاء في الفترة)'] = (string) ($m['new_leads'] ?? 0);
            $pairs['المؤشر المركّب (تقدير الفترة)'] = (string) ($payload['period_report']['composite'] ?? '—');
            $pairs['قيمة الأنبوب المفتوح (حالي)'] = number_format((float) ($m['pipeline_value'] ?? 0), 2, '.', ',').' ج.م';
            $pairs['فرص مفتوحة (حالي)'] = (string) ($m['open_opportunities'] ?? 0);
        } else {
            $pairs['عملاء محتملون (فريق) في الفترة'] = (string) $payload['leads']->count();
            $pairs['أنشطة CRM (فريق)'] = (string) $payload['activities']->count();
            $pairs['سجلات النظام'] = (string) $payload['audit_logs']->count();
            $totalRev = $payload['rep_summaries'] !== []
                ? collect($payload['rep_summaries'])->sum(fn ($s) => (float) ($s['report']['metrics']['revenue_closed'] ?? 0))
                : 0;
            $pairs['إجمالي إيرادات الفريق (فوز في الفترة)'] = number_format($totalRev, 2, '.', ',').' ج.م';
        }

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
        if ($payload['mode'] === 'single' && ! empty($payload['period_report']['alert_flags'])) {
            $sheet->setCellValue('B'.$row, 'تنبيهات ومؤشرات');
            $sheet->mergeCells('B'.$row.':'.$lastCol.$row);
            $sheet->getStyle('B'.$row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => self::WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_700]],
            ]);
            $row++;
            foreach ($payload['period_report']['alert_flags'] as $flag) {
                $sheet->setCellValue('B'.$row, '• '.$flag);
                $sheet->mergeCells('B'.$row.':'.$lastCol.$row);
                $sheet->getStyle('B'.$row)->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => self::SLATE_700]],
                    'alignment' => ['wrapText' => true, 'horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $row++;
            }
        }

        foreach (range('A', $lastCol) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addKpiSheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'مؤشرات الأداء');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        if ($payload['mode'] === 'single' && $payload['period_report']) {
            $this->writeSingleRepKpi($sheet, $payload['period_report']);
        } else {
            $this->writeTeamKpiMatrix($sheet, $payload['rep_summaries']);
        }

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    private function writeSingleRepKpi(Worksheet $sheet, array $report): void
    {
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'تفصيل KPIs للفترة (مقابل أهداف مُقيسة نسبةً لطول الفترة)');
        $this->styleTitleBand($sheet, 'A1:F1');

        $row = 3;
        $sheet->setCellValue('A'.$row, 'البعد');
        $sheet->setCellValue('B'.$row, 'الدرجة /100');
        $sheet->setCellValue('C'.$row, 'الوصف');
        $sheet->mergeCells('C'.$row.':F'.$row);
        $this->styleHeaderRow($sheet, 'A'.$row.':F'.$row);
        $row++;

        foreach ($report['pillars'] ?? [] as $key => $p) {
            $sheet->setCellValue('A'.$row, is_string($key) ? $key : '');
            $sheet->setCellValue('B'.$row, $p['score'] ?? '');
            $sheet->setCellValue('C'.$row, $p['label'] ?? '');
            $sheet->mergeCells('C'.$row.':F'.$row);
            $row++;
        }

        $row += 1;
        $sheet->setCellValue('A'.$row, 'المؤشر المركّب');
        $sheet->setCellValue('B'.$row, $report['composite'] ?? '');
        $sheet->mergeCells('C'.$row.':F'.$row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        $row += 2;

        $sheet->setCellValue('A'.$row, 'المؤشر');
        $sheet->setCellValue('B'.$row, 'الفعلي');
        $sheet->setCellValue('C'.$row, 'الهدف (فترة)');
        $sheet->setCellValue('D'.$row, 'نسبة الإنجاز %');
        $this->styleHeaderRow($sheet, 'A'.$row.':D'.$row);
        $row++;

        foreach ($report['kpi_lines'] ?? [] as $line) {
            $actual = $line['actual'] ?? null;
            $target = $line['target'] ?? null;
            $pct = null;
            if ($target !== null && is_numeric($target) && (float) $target > 0 && $actual !== null && is_numeric($actual)) {
                $pct = round((float) $actual / (float) $target * 100, 1);
            }

            $sheet->setCellValue('A'.$row, $line['label'] ?? '');
            $sheet->setCellValue('B'.$row, $this->excelScalar($actual));
            $sheet->setCellValue('C'.$row, $this->excelScalar($target));
            $sheet->setCellValue('D'.$row, $pct !== null ? $pct.'%' : '—');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D'] as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
        $sheet->freezePane('A4');
    }

    /**
     * @param  list<array{user: \App\Models\User, report: array<string, mixed>}>  $summaries
     */
    private function writeTeamKpiMatrix(Worksheet $sheet, array $summaries): void
    {
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'مقارنة موظفي المبيعات — ملخص الفترة');
        $this->styleTitleBand($sheet, 'A1:J1');

        $headers = ['الموظف', 'مركّب', 'إيراد فوز', 'صفقات فوز', 'Leads جديدة', 'مكالمات', 'اجتماعات', 'متابعات', 'أنبوب', 'متابعات متأخرة'];
        $row = 3;
        $col = 1;
        foreach ($headers as $h) {
            $c = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($c.$row, $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $this->styleHeaderRow($sheet, 'A'.$row.':'.$lastCol.$row);
        $row++;

        foreach ($summaries as $item) {
            $u = $item['user'];
            $r = $item['report'];
            $m = $r['metrics'] ?? [];
            $values = [
                $u->name,
                $r['composite'] ?? '—',
                (float) ($m['revenue_closed'] ?? 0),
                (int) ($m['won_closed'] ?? 0),
                (int) ($m['new_leads'] ?? 0),
                (int) ($m['calls'] ?? 0),
                (int) ($m['meetings'] ?? 0),
                (int) ($m['followups'] ?? 0),
                (int) ($m['open_opportunities'] ?? 0),
                (int) ($m['overdue_followups'] ?? 0),
            ];
            $col = 1;
            foreach ($values as $v) {
                $c = Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($c.$row, $v);
                $col++;
            }
            if ($row % 2 === 0) {
                $sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::EMERALD_50]],
                ]);
            }
            $row++;
        }

        $revCol = Coordinate::stringFromColumnIndex(3);
        for ($i = 4; $i <= $row - 1; $i++) {
            $sheet->getStyle($revCol.$i)->getNumberFormat()->setFormatCode('#,##0.00');
        }

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addLeadsSheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'العملاء المحتملون');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        $includeAssignee = $payload['mode'] === 'all';
        $headers = $includeAssignee
            ? ['المسند إليه', 'الاسم', 'الهاتف', 'البريد', 'الشركة', 'المصدر', 'المرحلة', 'الأولوية', 'قيمة متوقعة', 'متابعة تالية', 'آخر تواصل', 'أنشئ بواسطة', 'تاريخ الإنشاء', 'تاريخ الإغلاق']
            : ['الاسم', 'الهاتف', 'البريد', 'الشركة', 'المصدر', 'المرحلة', 'الأولوية', 'قيمة متوقعة', 'متابعة تالية', 'آخر تواصل', 'أنشئ بواسطة', 'تاريخ الإنشاء', 'تاريخ الإغلاق'];

        $sheet->mergeCells('A1:'.Coordinate::stringFromColumnIndex(count($headers)).'1');
        $sheet->setCellValue('A1', 'كل العملاء المحتملون ذوو الصلة بالفترة (إنشاء / تحديث / إغلاق / نشاط)');
        $this->styleTitleBand($sheet, 'A1:'.Coordinate::stringFromColumnIndex(count($headers)).'1');

        $row = 3;
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $this->styleHeaderRow($sheet, 'A'.$row.':'.$lastCol.$row);
        $row++;

        $alternate = false;
        /** @var \App\Models\SalesLead $lead */
        foreach ($payload['leads'] as $lead) {
            if ($includeAssignee) {
                $values = [
                    $lead->assignee->name ?? '—',
                    $lead->name,
                    $lead->phone,
                    $lead->email,
                    $lead->company,
                    SalesLead::sourceLabel($lead->source),
                    SalesLead::stageLabel($lead->stage),
                    SalesLead::priorityLabel($lead->priority),
                    $lead->expected_value !== null ? (float) $lead->expected_value : null,
                    $lead->next_follow_up_at?->format('Y-m-d H:i'),
                    $lead->last_contacted_at?->format('Y-m-d H:i'),
                    $lead->creator->name ?? '—',
                    $lead->created_at?->format('Y-m-d H:i'),
                    $lead->closed_at?->format('Y-m-d H:i'),
                ];
            } else {
                $values = [
                    $lead->name,
                    $lead->phone,
                    $lead->email,
                    $lead->company,
                    SalesLead::sourceLabel($lead->source),
                    SalesLead::stageLabel($lead->stage),
                    SalesLead::priorityLabel($lead->priority),
                    $lead->expected_value !== null ? (float) $lead->expected_value : null,
                    $lead->next_follow_up_at?->format('Y-m-d H:i'),
                    $lead->last_contacted_at?->format('Y-m-d H:i'),
                    $lead->creator->name ?? '—',
                    $lead->created_at?->format('Y-m-d H:i'),
                    $lead->closed_at?->format('Y-m-d H:i'),
                ];
            }
            $col = 1;
            foreach ($values as $v) {
                $c = Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($c.$row, $v ?? '');
                $col++;
            }
            $bg = $alternate ? self::EMERALD_50 : self::WHITE;
            $sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $valueColIdx = $includeAssignee ? 9 : 8;
            $ev = $lead->expected_value;
            if ($ev !== null && $ev !== '') {
                $sheet->getStyle(Coordinate::stringFromColumnIndex($valueColIdx).$row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            $alternate = ! $alternate;
            $row++;
        }

        if ($payload['leads']->isEmpty()) {
            $sheet->setCellValue('A'.$row, 'لا توجد سجلات في نطاق الفترة.');
            $sheet->mergeCells('A'.$row.':'.$lastCol.$row);
        }

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
        $sheet->freezePane('A4');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addActivitiesSheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'أنشطة CRM');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        $includeRep = $payload['mode'] === 'all';
        $headers = $includeRep
            ? ['الموظف', 'العميل المحتمل', 'النوع', 'العنوان', 'المحتوى', 'التاريخ']
            : ['العميل المحتمل', 'النوع', 'العنوان', 'المحتوى', 'التاريخ'];

        $sheet->mergeCells('A1:'.Coordinate::stringFromColumnIndex(count($headers)).'1');
        $sheet->setCellValue('A1', 'كل أنشطة CRM المسجّلة في الفترة');
        $this->styleTitleBand($sheet, 'A1:'.Coordinate::stringFromColumnIndex(count($headers)).'1');

        $row = 3;
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $this->styleHeaderRow($sheet, 'A'.$row.':'.$lastCol.$row);
        $row++;

        /** @var \App\Models\SalesActivity $act */
        foreach ($payload['activities'] as $act) {
            $body = $act->body !== null && $act->body !== ''
                ? mb_substr(strip_tags((string) $act->body), 0, 500)
                : '';
            if ($includeRep) {
                $values = [
                    $act->user->name ?? '—',
                    $act->lead->name ?? '—',
                    SalesActivity::typeLabel($act->type),
                    $act->title ?? '—',
                    $body,
                    $act->created_at?->format('Y-m-d H:i'),
                ];
            } else {
                $values = [
                    $act->lead->name ?? '—',
                    SalesActivity::typeLabel($act->type),
                    $act->title ?? '—',
                    $body,
                    $act->created_at?->format('Y-m-d H:i'),
                ];
            }
            $col = 1;
            foreach ($values as $v) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $v);
                $col++;
            }
            $sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]]],
                'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
            ]);
            $row++;
        }

        if ($payload['activities']->isEmpty()) {
            $sheet->setCellValue('A'.$row, 'لا توجد أنشطة في الفترة.');
            $sheet->mergeCells('A'.$row.':'.$lastCol.$row);
        }

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setWidth($i === count($headers) - ($includeRep ? 1 : 0) ? 22 : 18);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function addAuditSheet(Spreadsheet $ss, array $payload): void
    {
        $sheet = new Worksheet($ss, 'سجل النظام');
        $ss->addSheet($sheet);
        $sheet->setRightToLeft(true);

        $headers = ['التاريخ', 'المستخدم', 'الإجراء', 'الوصف', 'الرابط'];
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'سجل أنشطة المبيعات في النظام (مراقبة الإدارة)');
        $this->styleTitleBand($sheet, 'A1:E1');

        $row = 3;
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).$row, $h);
            $col++;
        }
        $this->styleHeaderRow($sheet, 'A'.$row.':E'.$row);
        $row++;

        $labels = [
            'sales_lead_created' => 'إنشاء عميل (موظف)',
            'sales_lead_viewed' => 'عرض عميل (موظف)',
            'sales_lead_updated' => 'تحديث عميل (موظف)',
            'sales_lead_deleted' => 'حذف عميل (موظف)',
            'sales_activity_created' => 'نشاط (موظف)',
            'sales_lead_created_admin' => 'إنشاء عميل (إدارة)',
            'sales_lead_viewed_admin' => 'عرض عميل (إدارة)',
            'sales_lead_updated_admin' => 'تحديث عميل (إدارة)',
            'sales_lead_deleted_admin' => 'حذف عميل (إدارة)',
            'sales_lead_reassigned' => 'إعادة إسناد',
            'sales_activity_created_admin' => 'نشاط (إدارة)',
        ];

        foreach ($payload['audit_logs'] as $log) {
            $sheet->setCellValue('A'.$row, $log->created_at?->format('Y-m-d H:i'));
            $sheet->setCellValue('B'.$row, $log->user->name ?? '—');
            $sheet->setCellValue('C'.$row, $labels[$log->action] ?? $log->action);
            $sheet->setCellValue('D'.$row, mb_substr((string) ($log->description ?? ''), 0, 800));
            $sheet->setCellValue('E'.$row, (string) ($log->url ?? ''));
            $sheet->getStyle('A'.$row.':E'.$row)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::BORDER]]],
                'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
            ]);
            $row++;
        }

        if ($payload['audit_logs']->isEmpty()) {
            $sheet->setCellValue('A'.$row, 'لا توجد سجلات في الفترة.');
            $sheet->mergeCells('A'.$row.':E'.$row);
        }

        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(55);
        $sheet->getColumnDimension('E')->setWidth(35);
    }

    private function styleTitleBand(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => self::WHITE],
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
        if (preg_match('/(\d+)/', $range, $m)) {
            $sheet->getRowDimension((int) $m[1])->setRowHeight(36);
        }
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
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => self::EMERALD_950],
                ],
            ],
        ]);
    }

    private function attachLogo(Worksheet $sheet, string $lastCol): void
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

    private function excelScalar(mixed $v): string|int|float
    {
        if ($v === null) {
            return '—';
        }

        return is_scalar($v) ? $v : '—';
    }
}
