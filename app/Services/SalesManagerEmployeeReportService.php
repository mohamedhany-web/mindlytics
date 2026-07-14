<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\EmployeeAttendanceRecord;
use App\Models\EmployeeSalaryDeduction;
use App\Models\SalesDailyReport;
use App\Models\SalesLead;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class SalesManagerEmployeeReportService
{
    public function __construct(
        private SalesEmployeePeriodReportService $periodReports,
        private SalesInsightsAnalyticsService $insights,
    ) {}

    /**
     * تقرير قوي لمدير المبيعات عن عضو فريقه.
     *
     * @return array<string, mixed>
     */
    public function build(User $rep, Carbon $start, Carbon $end, string $leadScope = 'touched', ?int $groupId = null): array
    {
        $base = $this->periodReports->build($rep, $start, $end, $leadScope, $groupId);
        $start = $base['start'];
        $end = $base['end'];

        $deductions = $this->deductionsForPeriod((int) $rep->id, $start, $end);
        $attendanceIssues = $this->attendanceIssues((int) $rep->id, $start, $end);
        $whatsapp = $this->whatsappStats((int) $rep->id, $start, $end);
        $auditTrail = $this->auditTrail((int) $rep->id, $start, $end);
        $charts = $this->insights->buildRepCharts((int) $rep->id, $start, $end, $base['period_report'] ?? []);
        $insights = $this->buildNarrativeInsights($base, $deductions, $attendanceIssues, $whatsapp);

        $base['deductions'] = $deductions;
        $base['attendance_issues'] = $attendanceIssues;
        $base['whatsapp'] = $whatsapp;
        $base['audit_trail'] = $auditTrail;
        $base['charts'] = $charts;
        $base['insights'] = $insights;
        $base['manager_mode'] = true;

        return $base;
    }

    /**
     * @return array{items: Collection, total_amount: float, by_type: array<string, float>, count: int, daily_report_penalties: int, attendance_penalties: int}
     */
    private function deductionsForPeriod(int $userId, Carbon $start, Carbon $end): array
    {
        if (! Schema::hasTable('employee_salary_deductions')) {
            return [
                'items' => collect(),
                'total_amount' => 0.0,
                'by_type' => [],
                'count' => 0,
                'daily_report_penalties' => 0,
                'attendance_penalties' => 0,
            ];
        }

        $items = EmployeeSalaryDeduction::query()
            ->where('employee_id', $userId)
            ->whereBetween('deduction_date', [$start->toDateString(), $end->toDateString()])
            ->where('status', '!=', 'cancelled')
            ->orderByDesc('deduction_date')
            ->get();

        $byType = [];
        foreach ($items as $row) {
            $type = (string) ($row->type ?: 'other');
            $byType[$type] = ($byType[$type] ?? 0) + (float) $row->amount;
        }

        $dailyReportPenalties = 0;
        if (Schema::hasColumn('sales_daily_reports', 'auto_deduction_id')) {
            $dailyReportPenalties = (int) SalesDailyReport::query()
                ->where('user_id', $userId)
                ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
                ->whereNotNull('auto_deduction_id')
                ->count();
        }

        $attendancePenalties = 0;
        if (Schema::hasTable('employee_attendance_records')) {
            $q = EmployeeAttendanceRecord::query()
                ->where('user_id', $userId)
                ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()]);

            $cols = array_filter([
                Schema::hasColumn('employee_attendance_records', 'late_deduction_id') ? 'late_deduction_id' : null,
                Schema::hasColumn('employee_attendance_records', 'absence_deduction_id') ? 'absence_deduction_id' : null,
                Schema::hasColumn('employee_attendance_records', 'incomplete_deduction_id') ? 'incomplete_deduction_id' : null,
                Schema::hasColumn('employee_attendance_records', 'presence_deduction_id') ? 'presence_deduction_id' : null,
            ]);

            if ($cols !== []) {
                $q->where(function ($inner) use ($cols) {
                    foreach ($cols as $col) {
                        $inner->orWhereNotNull($col);
                    }
                });
                $attendancePenalties = (int) $q->count();
            }
        }

        return [
            'items' => $items,
            'total_amount' => round((float) $items->sum('amount'), 2),
            'by_type' => $byType,
            'count' => $items->count(),
            'daily_report_penalties' => $dailyReportPenalties,
            'attendance_penalties' => $attendancePenalties,
        ];
    }

    /**
     * @return array{late_days: int, absent_days: int, incomplete_days: int, records: Collection}
     */
    private function attendanceIssues(int $userId, Carbon $start, Carbon $end): array
    {
        if (! Schema::hasTable('employee_attendance_records')) {
            return [
                'late_days' => 0,
                'absent_days' => 0,
                'incomplete_days' => 0,
                'records' => collect(),
            ];
        }

        $records = EmployeeAttendanceRecord::query()
            ->where('user_id', $userId)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('work_date')
            ->get();

        $late = 0;
        $absent = 0;
        $incomplete = 0;
        foreach ($records as $r) {
            if (! empty($r->is_late) || ! empty($r->late_deduction_id)) {
                $late++;
            }
            if (($r->status ?? null) === 'absent' || ! empty($r->absence_deduction_id)) {
                $absent++;
            }
            if (! empty($r->incomplete_deduction_id) || (($r->status ?? null) === 'incomplete')) {
                $incomplete++;
            }
        }

        return [
            'late_days' => $late,
            'absent_days' => $absent,
            'incomplete_days' => $incomplete,
            'records' => $records->take(30),
        ];
    }

    /**
     * @return array{conversations: int, inbound: int, outbound: int, unread_open: int}
     */
    private function whatsappStats(int $userId, Carbon $start, Carbon $end): array
    {
        $empty = ['conversations' => 0, 'inbound' => 0, 'outbound' => 0, 'unread_open' => 0];

        if (! Schema::hasTable('whatsapp_conversations')) {
            return $empty;
        }

        $owned = function ($q) use ($userId) {
            $q->where('assigned_to', $userId)
                ->orWhereHas('salesLead', fn ($lq) => $lq->where('assigned_to', $userId));
        };

        $conversations = WhatsAppConversation::query()
            ->where($owned)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('last_message_at', [$start, $end])
                    ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->count();

        $unreadOpen = WhatsAppConversation::query()
            ->where($owned)
            ->where('unread_count', '>', 0)
            ->count();

        $inbound = 0;
        $outbound = 0;
        if (Schema::hasTable('whatsapp_conversation_messages')) {
            $msgBase = WhatsAppConversationMessage::query()
                ->whereBetween('created_at', [$start, $end])
                ->where(function ($q) use ($userId) {
                    $q->where('sent_by_user_id', $userId)
                        ->orWhereHas('conversation', function ($cq) use ($userId) {
                            $cq->where('assigned_to', $userId)
                                ->orWhereHas('salesLead', fn ($lq) => $lq->where('assigned_to', $userId));
                        });
                });

            $inbound = (int) (clone $msgBase)->where('direction', 'inbound')->count();
            $outbound = (int) (clone $msgBase)->where('direction', 'outbound')->count();
        }

        return [
            'conversations' => $conversations,
            'inbound' => $inbound,
            'outbound' => $outbound,
            'unread_open' => $unreadOpen,
        ];
    }

    /**
     * @return Collection<int, ActivityLog>
     */
    private function auditTrail(int $userId, Carbon $start, Carbon $end): Collection
    {
        if (! Schema::hasTable('activity_logs')) {
            return collect();
        }

        return ActivityLog::query()
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$start, $end])
            ->where(function ($q) {
                $q->where('action', 'like', 'sales_%')
                    ->orWhereIn('action', ['login', 'logout']);
            })
            ->orderByDesc('created_at')
            ->limit(80)
            ->get(['id', 'action', 'description', 'created_at', 'ip_address']);
    }

    /**
     * @param  array<string, mixed>  $base
     * @param  array<string, mixed>  $deductions
     * @param  array<string, mixed>  $attendanceIssues
     * @param  array<string, mixed>  $whatsapp
     * @return list<array{tone: string, title: string, body: string}>
     */
    private function buildNarrativeInsights(array $base, array $deductions, array $attendanceIssues, array $whatsapp): array
    {
        $summary = $base['summary'] ?? [];
        $period = $base['period_report'] ?? [];
        $alerts = $period['alert_flags'] ?? [];
        $composite = $period['composite'] ?? ($summary['composite_score'] ?? null);

        $insights = [];

        $leads = (int) ($summary['leads_in_scope'] ?? 0);
        $contacted = (int) ($summary['leads_contacted_in_period'] ?? 0);
        $never = (int) ($summary['leads_never_contacted'] ?? 0);
        $contactRate = $leads > 0 ? round($contacted / $leads * 100, 1) : 0;

        $insights[] = [
            'tone' => $contactRate >= 70 ? 'emerald' : ($contactRate >= 40 ? 'amber' : 'rose'),
            'title' => 'معدل التواصل مع العملاء',
            'body' => "تم التواصل مع {$contacted} من أصل {$leads} عميل في نطاق التقرير ({$contactRate}%)."
                .($never > 0 ? " يوجد {$never} عميل لم يُتواصل معهم نهائياً." : ' لا يوجد عملاء بدون تواصل.'),
        ];

        $activities = (int) ($summary['total_activities'] ?? 0);
        $calls = (int) ($summary['calls'] ?? 0);
        $followups = (int) ($summary['followups'] ?? 0);
        $meetings = (int) ($summary['meetings'] ?? 0);
        $insights[] = [
            'tone' => $activities > 0 ? 'sky' : 'rose',
            'title' => 'نشاط CRM',
            'body' => "إجمالي الأنشطة: {$activities} (مكالمات {$calls} · متابعات {$followups} · اجتماعات {$meetings})."
                .(empty($whatsapp['outbound']) ? '' : " واتساب صادر: {$whatsapp['outbound']} · وارد: {$whatsapp['inbound']}."),
        ];

        $loginDays = (int) ($summary['days_with_login'] ?? 0);
        $workDays = (int) ($summary['work_days'] ?? 0);
        $absent = (int) ($summary['days_without_login'] ?? 0);
        $missingReports = (int) ($summary['daily_reports_missing'] ?? 0);
        $insights[] = [
            'tone' => ($absent > 0 || $missingReports > 0) ? 'amber' : 'emerald',
            'title' => 'الانضباط والحضور',
            'body' => "دخول النظام في {$loginDays}/{$workDays} يوم عمل"
                .($absent > 0 ? " — غياب دخول: {$absent} يوم." : '.')
                ." تقارير يومية ناقصة: {$missingReports}."
                .(($attendanceIssues['late_days'] ?? 0) > 0 ? " تأخير حضور: {$attendanceIssues['late_days']} يوم." : ''),
        ];

        $won = (int) ($summary['won_deals'] ?? 0);
        $revenue = (float) ($summary['revenue'] ?? 0);
        $insights[] = [
            'tone' => $won > 0 ? 'emerald' : 'slate',
            'title' => 'النتائج والإقفال',
            'body' => "صفقات Won في الفترة: {$won} · إيراد مقفول تقريبي: ".number_format($revenue, 0).' ج.م.',
        ];

        if ($composite !== null) {
            $score = is_numeric($composite) ? round((float) $composite, 1) : $composite;
            $insights[] = [
                'tone' => ((float) $score >= 70 ? 'emerald' : ((float) $score >= 50 ? 'amber' : 'rose')),
                'title' => 'درجة KPI المركّبة',
                'body' => "الدرجة المركّبة للفترة: {$score}% — تجمع نتائج الإقفال + النشاط + الجودة + انضباط التقارير.",
            ];
        }

        if (($deductions['count'] ?? 0) > 0) {
            $insights[] = [
                'tone' => 'rose',
                'title' => 'الخصومات في الفترة',
                'body' => "عدد الخصومات: {$deductions['count']} · الإجمالي: ".number_format((float) $deductions['total_amount'], 2).' ج.م'
                    ." (تقارير يومية: {$deductions['daily_report_penalties']} · حضور: {$deductions['attendance_penalties']}).",
            ];
        } else {
            $insights[] = [
                'tone' => 'emerald',
                'title' => 'الخصومات في الفترة',
                'body' => 'لا توجد خصومات مسجّلة على الموظف خلال الفترة المحددة.',
            ];
        }

        foreach ($alerts as $alert) {
            if (is_string($alert) && $alert !== '') {
                $insights[] = [
                    'tone' => str_contains($alert, 'حرج') ? 'rose' : 'amber',
                    'title' => 'تنبيه KPI',
                    'body' => $alert,
                ];

                continue;
            }
            if (! is_array($alert)) {
                continue;
            }
            $insights[] = [
                'tone' => 'amber',
                'title' => (string) ($alert['title'] ?? $alert['label'] ?? 'تنبيه KPI'),
                'body' => (string) ($alert['message'] ?? $alert['body'] ?? json_encode($alert, JSON_UNESCAPED_UNICODE)),
            ];
        }

        if (($whatsapp['unread_open'] ?? 0) > 0) {
            $insights[] = [
                'tone' => 'violet',
                'title' => 'محادثات واتساب معلّقة',
                'body' => "يوجد حالياً {$whatsapp['unread_open']} محادثة معيّنة للموظف وبها رسائل غير مقروءة.",
            ];
        }

        return $insights;
    }
}
