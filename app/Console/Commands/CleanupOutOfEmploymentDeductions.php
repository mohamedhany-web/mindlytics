<?php

namespace App\Console\Commands;

use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeDailyReport;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesDailyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * إلغاء الخصومات التلقائية التي أُنشئت لتواريخ خارج فترة عمل الموظف
 * (قبل تاريخ التعيين أو بعد إنهاء الخدمة).
 */
class CleanupOutOfEmploymentDeductions extends Command
{
    protected $signature = 'employees:cleanup-out-of-employment-deductions {--dry-run : عرض النتائج دون تعديل}';

    protected $description = 'Cancel auto deductions dated before hire date or after termination date';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $cancelled = 0;

        $employees = User::employees()->get(['id', 'name', 'hire_date', 'termination_date']);

        foreach ($employees as $employee) {
            $deductions = EmployeeSalaryDeduction::query()
                ->where('employee_id', $employee->id)
                ->where('status', '!=', 'cancelled')
                ->whereNull('created_by')
                ->get();

            foreach ($deductions as $deduction) {
                $date = Carbon::parse($deduction->deduction_date);
                if ($employee->isEmployedOn($date)) {
                    continue;
                }

                $this->line(sprintf(
                    '%s — %s — %s — %s ج.م',
                    $employee->name,
                    $deduction->deduction_number,
                    $date->toDateString(),
                    number_format((float) $deduction->amount, 2)
                ));

                if (! $dryRun) {
                    DB::transaction(function () use ($deduction) {
                        $deduction->update([
                            'status' => 'cancelled',
                            'notes' => trim(($deduction->notes ? $deduction->notes."\n" : '')
                                .'أُلغي تلقائياً — التاريخ خارج فترة عمل الموظف.'),
                        ]);

                        SalesDailyReport::query()
                            ->where('auto_deduction_id', $deduction->id)
                            ->update(['auto_deduction_id' => null]);

                        EmployeeDailyReport::query()
                            ->where('auto_deduction_id', $deduction->id)
                            ->update(['auto_deduction_id' => null]);

                        EmployeeAttendanceRecord::query()
                            ->where('late_deduction_id', $deduction->id)
                            ->update(['late_deduction_id' => null]);
                        EmployeeAttendanceRecord::query()
                            ->where('absence_deduction_id', $deduction->id)
                            ->update(['absence_deduction_id' => null]);
                        EmployeeAttendanceRecord::query()
                            ->where('incomplete_deduction_id', $deduction->id)
                            ->update(['incomplete_deduction_id' => null]);
                        EmployeeAttendanceRecord::query()
                            ->where('presence_deduction_id', $deduction->id)
                            ->update(['presence_deduction_id' => null]);
                    });
                }

                $cancelled++;
            }
        }

        $this->info($dryRun
            ? "خصومات ستُلغى: {$cancelled}"
            : "تم إلغاء {$cancelled} خصماً خارج فترة العمل.");

        return self::SUCCESS;
    }
}
