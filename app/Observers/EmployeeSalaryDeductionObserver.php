<?php

namespace App\Observers;

use App\Models\EmployeeDailyReport;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesDailyReport;

class EmployeeSalaryDeductionObserver
{
    /**
     * عند حذف خصم مرتبط بتقرير يومي: نُعلّم اليوم كـ «معفى» حتى لا يُعاد إنشاء الخصم تلقائياً.
     */
    public function deleting(EmployeeSalaryDeduction $deduction): void
    {
        $waivedAt = now();

        SalesDailyReport::query()
            ->where('auto_deduction_id', $deduction->id)
            ->update(['penalty_waived_at' => $waivedAt]);

        EmployeeDailyReport::query()
            ->where('auto_deduction_id', $deduction->id)
            ->update(['penalty_waived_at' => $waivedAt]);

        $this->waiveDailyReportForAutoPenalty($deduction, $waivedAt);
    }

    private function waiveDailyReportForAutoPenalty(EmployeeSalaryDeduction $deduction, \DateTimeInterface $waivedAt): void
    {
        if ($deduction->type !== 'penalty' || $deduction->created_by !== null || ! $deduction->employee_id) {
            return;
        }

        $title = (string) $deduction->title;
        $description = (string) ($deduction->description ?? '');
        $notes = (string) ($deduction->notes ?? '');

        $isSales = str_contains($notes, 'تقرير يومي مبيعات')
            || str_contains($title, 'التقرير اليومي')
            || str_contains($title, 'غرامة التقرير اليومي');

        $isEmployee = str_contains($title, 'تقرير يومي لم يُرسل')
            || str_contains($description, 'لم يُسلّم التقرير اليومي');

        if (! $isSales && ! $isEmployee) {
            return;
        }

        $date = $deduction->deduction_date;
        if (! $date) {
            return;
        }

        if ($isSales) {
            SalesDailyReport::query()
                ->where('user_id', $deduction->employee_id)
                ->whereDate('report_date', $date)
                ->whereNull('penalty_waived_at')
                ->update(['penalty_waived_at' => $waivedAt]);
        }

        if ($isEmployee) {
            EmployeeDailyReport::query()
                ->where('user_id', $deduction->employee_id)
                ->whereDate('report_date', $date)
                ->whereNull('penalty_waived_at')
                ->update(['penalty_waived_at' => $waivedAt]);
        }
    }
}
