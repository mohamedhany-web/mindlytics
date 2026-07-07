<?php



namespace App\Services;



use App\Models\EmployeeAttendanceRecord;

use Illuminate\Http\Request;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Style\Alignment;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Symfony\Component\HttpFoundation\StreamedResponse;



class EmployeeAttendanceExcelExport

{

    public function streamFromRequest(Request $request): StreamedResponse

    {

        $records = $this->queryFromRequest($request)->get();

        $spreadsheet = $this->build($records);

        $filename = 'employee_attendance_'.now()->format('Y_m_d_His').'.xlsx';



        return new StreamedResponse(function () use ($spreadsheet) {

            (new Xlsx($spreadsheet))->save('php://output');

        }, 200, [

            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

            'Content-Disposition' => 'attachment; filename="'.$filename.'"',

            'Cache-Control' => 'max-age=0',

        ]);

    }



    private function queryFromRequest(Request $request)

    {

        $query = EmployeeAttendanceRecord::query()

            ->with(['user.employeeJob', 'workSchedule', 'lateDeduction', 'absenceDeduction', 'incompleteDeduction'])

            ->orderByDesc('work_date');



        if ($request->filled('employee_id')) {

            $query->where('user_id', (int) $request->employee_id);

        }

        if ($request->filled('job_id')) {

            $query->whereHas('user', fn ($q) => $q->where('employee_job_id', (int) $request->job_id));

        }

        if ($request->filled('status')) {

            $query->where('status', $request->status);

        }

        if ($request->filled('from')) {

            $query->whereDate('work_date', '>=', $request->from);

        }

        if ($request->filled('to')) {

            $query->whereDate('work_date', '<=', $request->to);

        }

        if ($request->boolean('late_only')) {

            $query->where('is_late', true);

        }



        return $query;

    }



    private function build($records): Spreadsheet

    {

        $spreadsheet = new Spreadsheet;

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('حضور الموظفين');

        $sheet->setRightToLeft(true);



        $headers = [

            'التاريخ', 'الموظف', 'الوظيفة', 'موعد العمل', 'حضور', 'انصراف',

            'ساعات العمل', 'المطلوب', 'الحالة', 'متأخر', 'خصم تأخير', 'خصم غياب', 'خصم عدم إكمال', 'إجمالي خصومات',

        ];



        $lastCol = Coordinate::stringFromColumnIndex(count($headers));



        foreach ($headers as $index => $header) {

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1).'1', $header);

        }



        $sheet->getStyle('A1:'.$lastCol.'1')->applyFromArray([

            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],

            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F766E']],

            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],

        ]);



        $labels = EmployeeAttendanceRecord::statusLabels();

        $row = 2;



        foreach ($records as $record) {

            $values = [

                $record->work_date->format('Y-m-d'),

                $record->user->name ?? '',

                $record->user->employeeJob->name ?? '',

                $record->workSchedule?->timeRangeLabel() ?? '',

                $record->clock_in_at?->format('H:i') ?? '',

                $record->clock_out_at?->format('H:i') ?? '',

                $record->worked_minutes ? round($record->worked_minutes / 60, 2) : '',

                round($record->required_minutes / 60, 2),

                $labels[$record->status] ?? $record->status,

                $record->is_late ? 'نعم' : 'لا',

                (float) ($record->lateDeduction?->amount ?? 0),

                (float) ($record->absenceDeduction?->amount ?? 0),

                (float) ($record->incompleteDeduction?->amount ?? 0),

                $record->totalDeductionAmount(),

            ];



            foreach ($values as $index => $value) {

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($index + 1).$row, $value);

            }



            $row++;

        }



        for ($i = 1; $i <= count($headers); $i++) {

            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);

        }



        $sheet->getStyle('A1:'.$lastCol.max(1, $row - 1))->applyFromArray([

            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],

        ]);



        $sheet->setCellValue('A'.($row + 1), 'تاريخ التصدير: '.now()->format('Y-m-d H:i'));



        return $spreadsheet;

    }

}


