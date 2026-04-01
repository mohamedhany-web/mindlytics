<?php

namespace App\Services;

use App\Models\DesignTaskCycle;
use App\Models\EmployeeTask;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeMonthlyPerformanceExcelExport
{
    private const HEADER_FILL = '7C3AED';

    private const HEADER_FONT = 'FFFFFF';

    private const ACCENT_FILL = 'F5F3FF';

    /**
     * @param  array<string, mixed>  $data  ناتج EmployeeMonthlyPerformanceReportService::analyze()
     */
    public function streamResponse(array $data): StreamedResponse
    {
        $spreadsheet = $this->build($data);
        /** @var Carbon $start */
        $start = $data['start'];
        $filenameAscii = 'employee_performance_'.$start->format('Y_m').'.xlsx';
        $filenameUtf = 'تقرير_أداء_الموظفين_'.$start->format('Y_m').'.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filenameAscii.'"; filename*=UTF-8\'\''.rawurlencode($filenameUtf),
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function build(array $data): Spreadsheet
    {
        /** @var Carbon $start */
        $start = $data['start'];
        /** @var Carbon $end */
        $end = $data['end'];
        $rows = $data['rows'];
        $summary = $data['summary'];
        /** @var \Illuminate\Support\Collection<int, DesignTaskCycle> $designCycles */
        $designCycles = $data['design_cycles'];
        /** @var \Illuminate\Support\Collection<int, EmployeeTask> $completedTasks */
        $completedTasks = $data['completed_tasks'];

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator('Mindlytics')
            ->setTitle('تقرير أداء الموظفين')
            ->setSubject($start->translatedFormat('F Y'));

        $this->fillSummarySheet($spreadsheet->getActiveSheet(), $start, $end, $summary, $rows);
        $this->fillEmployeesSheet($spreadsheet->createSheet(), $rows);
        $this->fillDesignCyclesSheet($spreadsheet->createSheet(), $designCycles);
        $this->fillTasksSheet($spreadsheet->createSheet(), $completedTasks);

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function headerStyle(): array
    {
        return [
            'font' => ['bold' => true, 'color' => ['rgb' => self::HEADER_FONT], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::HEADER_FILL]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];
    }

    private function thinBorder(): array
    {
        return [
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
            ],
        ];
    }

    private function fillSummarySheet(Worksheet $sheet, Carbon $start, Carbon $end, array $summary, array $rows): void
    {
        $sheet->setTitle('ملخص تنفيذي');
        $sheet->setRightToLeft(true);

        $sheet->setCellValue('A1', 'Mindlytics — تقرير أداء الموظفين والتصميم');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('4C1D95');
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->setCellValue('A2', 'الفترة: من '.$start->format('Y-m-d').' إلى '.$end->format('Y-m-d').'   |   شهر: '.$start->translatedFormat('F Y'));
        $sheet->mergeCells('A2:D2');
        $sheet->getStyle('A2')->getFont()->setSize(11);

        $sheet->setCellValue('A3', 'تاريخ التصدير: '.now()->format('Y-m-d H:i'));
        $sheet->mergeCells('A3:D3');

        $r = 5;
        $sheet->setCellValue('A'.$r, 'المؤشر');
        $sheet->setCellValue('B'.$r, 'القيمة');
        $sheet->setCellValue('C'.$r, 'ملاحظات');
        $sheet->getStyle('A'.$r.':C'.$r)->applyFromArray($this->headerStyle());
        $r++;

        $lines = [
            ['عدد الموظفين النشطين', (string) $summary['employees'], ''],
            ['إجمالي المهام المسندة في الشهر', (string) $summary['tasks_assigned'], ''],
            ['إجمالي المهام المكتملة في الشهر', (string) $summary['tasks_completed'], ''],
            ['مهام مكتملة في الموعد', (string) $summary['tasks_on_time'], 'حسب مقارنة completed_at بآخر يوم deadline'],
            ['مهام مكتملة متأخرة', (string) $summary['tasks_late'], ''],
            ['نسبة الالتزام بالموعد (مهام)', $summary['tasks_on_time_rate_pct'] !== null ? $summary['tasks_on_time_rate_pct'].'%' : '—', 'من المهام التي لها موعد نهائي'],
            ['إجمالي التسليمات المرفوعة', (string) $summary['deliverables'], ''],
            ['دورات تصميم لها نشاط في الشهر', (string) ($summary['design_cycles_touched_month'] ?? 0), 'إنشاء أو إكمال أو تسليم مصمم'],
            ['تسليمات مصمم ضمن الشهر', (string) $summary['designer_submissions_month'], ''],
            ['تسليم مصمم في الموعد', (string) $summary['designer_on_time'], ''],
            ['تسليم مصمم متأخر', (string) $summary['designer_late'], ''],
            ['نسبة الالتزام (مصمم)', $summary['designer_on_time_rate_pct'] !== null ? $summary['designer_on_time_rate_pct'].'%' : '—', ''],
            ['دورات أُكملت كمشرف', (string) $summary['moderator_cycles_completed'], ''],
            ['مهام مفتوحة متأخرة (نهاية الشهر)', (string) $summary['open_overdue_tasks'], 'pending / in_progress / on_hold'],
        ];

        foreach ($lines as $line) {
            $sheet->setCellValue('A'.$r, $line[0]);
            $sheet->setCellValue('B'.$r, $line[1]);
            $sheet->setCellValue('C'.$r, $line[2]);
            $sheet->getStyle('A'.$r.':C'.$r)->applyFromArray($this->thinBorder());
            if ($r % 2 === 0) {
                $sheet->getStyle('A'.$r.':C'.$r)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB(self::ACCENT_FILL);
            }
            $r++;
        }

        $r += 2;
        $sheet->setCellValue('A'.$r, 'أعلى ٨ موظفين حسب المهام المكتملة في الشهر');
        $sheet->mergeCells('A'.$r.':C'.$r);
        $sheet->getStyle('A'.$r)->getFont()->setBold(true)->setSize(12);
        $r++;

        $sheet->setCellValue('A'.$r, 'الموظف');
        $sheet->setCellValue('B'.$r, 'المهام المكتملة');
        $sheet->setCellValue('C'.$r, 'نسبة الإنجاز من المسند');
        $sheet->getStyle('A'.$r.':C'.$r)->applyFromArray($this->headerStyle());
        $r++;

        $top = collect($rows)->sortByDesc(fn ($x) => $x['tasks_completed_in_month'])->take(8);
        foreach ($top as $row) {
            $sheet->setCellValue('A'.$r, $row['user']->name);
            $sheet->setCellValue('B'.$r, $row['tasks_completed_in_month']);
            $pct = $row['tasks_completion_rate_pct'];
            $sheet->setCellValue('C'.$r, $pct !== null ? $pct.'%' : '—');
            $sheet->getStyle('A'.$r.':C'.$r)->applyFromArray($this->thinBorder());
            $r++;
        }

        $sheet->getColumnDimension('A')->setWidth(42);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(36);
        $sheet->getStyle('A1:C'.($r - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function fillEmployeesSheet(Worksheet $sheet, array $rows): void
    {
        $sheet->setTitle('أداء الموظفين');
        $sheet->setRightToLeft(true);
        $sheet->freezePane('B2');

        $headers = [
            'الموظف',
            'البريد',
            'الوظيفة',
            'مسند بالشهر',
            'مكتملة',
            'نسبة إنجاز %',
            'في الموعد',
            'متأخر',
            '%في الموعد',
            'متوسط ساعات',
            'مفتوحة متأخرة',
            'تسليمات',
            'تصميم (مكتمل)',
            'مونتاج (مكتمل)',
            'مبيعات (مكتمل)',
            'أخرى (مكتمل)',
            'دورات كمصمم',
            'تسليم مصمم بالشهر',
            'مصمم بالموعد',
            'مصمم متأخر',
            '%مصمم بالموعد',
            'دورات أنشأها مشرف',
            'أكمل كمشرف',
            'ملغاة كمشرف',
            'متوسط أيام إكمال الدورة',
        ];

        $colCount = count($headers);
        $sheet->fromArray($headers, null, 'A1');
        $lastCol = Coordinate::stringFromColumnIndex($colCount);
        $sheet->getStyle('A1:'.$lastCol.'1')->applyFromArray($this->headerStyle());

        $rowIndex = 2;
        foreach ($rows as $row) {
            $u = $row['user'];
            $line = [
                $u->name,
                $u->email ?? '',
                $u->employeeJob->name ?? '—',
                $row['tasks_assigned_in_month'],
                $row['tasks_completed_in_month'],
                $row['tasks_completion_rate_pct'] !== null ? $row['tasks_completion_rate_pct'] / 100 : null,
                $row['tasks_on_time'],
                $row['tasks_late'],
                $row['tasks_on_time_rate_pct'] !== null ? $row['tasks_on_time_rate_pct'] / 100 : null,
                $row['avg_completion_hours'],
                $row['open_overdue_tasks_end_of_month'],
                $row['deliverables_submitted'],
                $row['tasks_completed_design'],
                $row['tasks_completed_video'],
                $row['tasks_completed_sales'],
                $row['tasks_completed_other'],
                $row['design_cycles_as_designer'],
                $row['designer_submissions_in_month'],
                $row['designer_on_time'],
                $row['designer_late'],
                $row['designer_on_time_rate_pct'] !== null ? $row['designer_on_time_rate_pct'] / 100 : null,
                $row['design_cycles_created_as_moderator'],
                $row['design_cycles_completed_as_moderator'],
                $row['design_cycles_cancelled_as_moderator'],
                $row['moderator_avg_cycle_completion_days'],
            ];
            $sheet->fromArray($line, null, 'A'.$rowIndex);
            $sheet->getStyle('A'.$rowIndex.':'.$lastCol.$rowIndex)->applyFromArray($this->thinBorder());
            $rowIndex++;
        }

        $pctCols = ['F', 'I', 'U'];
        foreach ($pctCols as $col) {
            $sheet->getStyle($col.'2:'.$col.$rowIndex)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        }

        for ($c = 1; $c <= $colCount; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setWidth($c === 1 ? 24 : 12);
        }

        $sheet->setAutoFilter('A1:'.$lastCol.($rowIndex - 1));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, DesignTaskCycle>  $cycles
     */
    private function fillDesignCyclesSheet(Worksheet $sheet, $cycles): void
    {
        $sheet->setTitle('دورات التصميم');
        $sheet->setRightToLeft(true);
        $sheet->freezePane('A2');

        $headers = [
            'المعرف', 'العنوان', 'الحالة', 'الأولوية', 'المشرف', 'المصمم',
            'أنشئت', 'موعد التسليم', 'تسليم المصمم', 'اكتملت', 'أيام حتى تسليم المصمم',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastCol.'1')->applyFromArray($this->headerStyle());

        $r = 2;
        foreach ($cycles as $c) {
            $daysDesigner = null;
            if ($c->designer_submitted_at && $c->created_at) {
                $daysDesigner = $c->created_at->diffInDays($c->designer_submitted_at);
            }
            $line = [
                $c->id,
                $c->title,
                DesignTaskCycle::statusLabel($c->status),
                $c->priority,
                $c->moderator?->name ?? '—',
                $c->designer?->name ?? '—',
                $c->created_at?->format('Y-m-d H:i'),
                $c->deadline_at?->format('Y-m-d H:i'),
                $c->designer_submitted_at?->format('Y-m-d H:i'),
                $c->completed_at?->format('Y-m-d H:i'),
                $daysDesigner,
            ];
            $sheet->fromArray($line, null, 'A'.$r);
            $sheet->getStyle('A'.$r.':'.$lastCol.$r)->applyFromArray($this->thinBorder());
            $r++;
        }

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimensionByColumn($i)->setWidth($i <= 2 ? 28 : 16);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, EmployeeTask>  $tasks
     */
    private function fillTasksSheet(Worksheet $sheet, $tasks): void
    {
        $sheet->setTitle('المهام المكتملة');
        $sheet->setRightToLeft(true);
        $sheet->freezePane('A2');

        $headers = [
            'معرف المهمة', 'الموظف', 'البريد', 'العنوان', 'نوع المهمة', 'الأولوية',
            'الموعد النهائي', 'اكتملت في', 'في الموعد', 'ساعات من الإسناد للإكمال',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:'.$lastCol.'1')->applyFromArray($this->headerStyle());

        $tasks->loadMissing(['employee.employeeJob']);

        $r = 2;
        foreach ($tasks as $t) {
            $onTime = '—';
            if ($t->deadline && $t->completed_at) {
                $deadlineEnd = Carbon::parse($t->deadline->format('Y-m-d'))->endOfDay();
                $onTime = $t->completed_at->lte($deadlineEnd) ? 'نعم' : 'لا';
            }
            $hours = ($t->created_at && $t->completed_at) ? round($t->created_at->diffInHours($t->completed_at), 2) : null;

            $line = [
                $t->id,
                $t->employee?->name ?? '—',
                $t->employee?->email ?? '',
                $t->title,
                EmployeeMonthlyPerformanceReportService::taskTypeLabelArabic($t->task_type),
                $t->priority,
                $t->deadline?->format('Y-m-d'),
                $t->completed_at?->format('Y-m-d H:i'),
                $onTime,
                $hours,
            ];
            $sheet->fromArray($line, null, 'A'.$r);
            $sheet->getStyle('A'.$r.':'.$lastCol.$r)->applyFromArray($this->thinBorder());
            $r++;
        }

        foreach (range(1, count($headers)) as $i) {
            $sheet->getColumnDimensionByColumn($i)->setWidth($i === 4 ? 36 : 14);
        }
    }
}
