<?php

namespace App\Console\Commands;

use App\Models\SalesDailyReport;
use App\Models\User;
use App\Services\SalesKpiService;
use App\Services\SalesNotificationService;
use Illuminate\Console\Command;

class EvaluateSalesEnforcement extends Command
{
    protected $signature = 'sales:evaluate-enforcement';

    protected $description = 'Evaluate sales KPIs and send enforcement notifications to reps and admins';

    public function handle(SalesKpiService $kpi, SalesNotificationService $notifications): int
    {
        $critical = (float) config('sales_kpi.alerts.composite_critical', 45);
        $warning = (float) config('sales_kpi.alerts.composite_warning', 65);
        $sent = 0;

        $reps = User::salesEmployees()->where('is_active', true)->get();

        foreach ($reps as $rep) {
            $report = $kpi->buildReport($rep);
            $composite = (float) ($report['composite_month'] ?? 0);
            $flags = $report['alert_flags'] ?? [];

            if ($composite < $critical) {
                $notifications->notifyKpiAlert($rep, $composite, $flags, true);
                $sent++;
                $this->notifyAdminsKpi($rep, $composite, true);
            } elseif ($composite < $warning) {
                $notifications->notifyKpiAlert($rep, $composite, $flags, false);
                $sent++;
            }
        }

        $this->info("Sales KPI enforcement notifications sent: {$sent}");

        return self::SUCCESS;
    }

    private function notifyAdminsKpi(User $rep, float $composite, bool $critical): void
    {
        $admins = User::query()->whereIn('role', ['admin', 'super_admin'])->where('is_active', true)->get();

        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'sender_id' => null,
                'title' => $critical ? 'تصعيد KPI — أداء حرج' : 'تنبيه KPI — أداء منخفض',
                'message' => "الموظف {$rep->name} — المؤشر المركّب: ".number_format($composite, 1).'%',
                'type' => 'warning',
                'priority' => $critical ? 'urgent' : 'high',
                'audience' => 'employee',
                'action_url' => route('admin.sales.kpi.index', ['user_id' => $rep->id]),
                'action_text' => 'مراجعة KPIs',
                'data' => ['kind' => 'sales_kpi_admin_alert', 'rep_id' => $rep->id, 'composite' => $composite],
            ]);
        }
    }
}
