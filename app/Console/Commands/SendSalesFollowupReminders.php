<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Console\Command;

class SendSalesFollowupReminders extends Command
{
    protected $signature = 'sales:send-followup-reminders {--sla-overdue-threshold=3 : Overdue follow-ups threshold for admin escalation}';

    protected $description = 'Send daily sales follow-up reminders for reps and SLA escalation alerts for admins';

    public function handle(): int
    {
        $threshold = max(1, (int) $this->option('sla-overdue-threshold'));
        $today = today();
        $salesReps = User::salesEmployees()->where('is_active', true)->get(['id', 'name']);

        $employeeNotifs = 0;
        $adminNotifs = 0;

        foreach ($salesReps as $rep) {
            $base = SalesLead::query()->forAssignee((int) $rep->id)->openPipeline();

            $overdueCount = (clone $base)
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count();

            $todayCount = (clone $base)
                ->whereNotNull('next_follow_up_at')
                ->whereDate('next_follow_up_at', $today)
                ->count();

            $staleCount = $this->staleOpenCount((int) $rep->id);

            if (($overdueCount + $todayCount + $staleCount) > 0) {
                $title = 'تذكير المتابعات اليومية - المبيعات';
                $message = "لديك {$overdueCount} متابعة متأخرة، {$todayCount} متابعة اليوم، و {$staleCount} عميل يحتاج تواصل.";
                $actionUrl = route('employee.sales.leads.index', ['follow_up' => 'overdue', 'sort' => 'follow_up']);

                if (! $this->alreadySentToday((int) $rep->id, $title, $actionUrl)) {
                    Notification::create([
                        'user_id' => $rep->id,
                        'sender_id' => null,
                        'title' => $title,
                        'message' => $message,
                        'type' => 'reminder',
                        'priority' => $overdueCount > 0 ? 'high' : 'normal',
                        'audience' => 'employee',
                        'action_url' => $actionUrl,
                        'action_text' => 'فتح العملاء المحتملين',
                        'data' => [
                            'reminder_kind' => 'sales_followup_daily',
                            'overdue_followups' => $overdueCount,
                            'today_followups' => $todayCount,
                            'stale_open_leads' => $staleCount,
                            'date' => now()->toDateString(),
                        ],
                    ]);
                    $employeeNotifs++;
                }
            }

            if ($overdueCount >= $threshold) {
                $adminIds = User::query()
                    ->whereIn('role', ['admin', 'super_admin'])
                    ->where('is_active', true)
                    ->pluck('id');

                foreach ($adminIds as $adminId) {
                    $title = 'تصعيد SLA - متابعة متأخرة';
                    $message = "الموظف {$rep->name} لديه {$overdueCount} متابعات متأخرة (الحد: {$threshold}).";
                    $actionUrl = route('admin.sales.leads.index', [
                        'assigned_to' => $rep->id,
                        'follow_up' => 'overdue',
                        'sort' => 'follow_up',
                    ]);

                    if (! $this->alreadySentToday((int) $adminId, $title, $actionUrl)) {
                        Notification::create([
                            'user_id' => (int) $adminId,
                            'sender_id' => null,
                            'title' => $title,
                            'message' => $message,
                            'type' => 'warning',
                            'priority' => 'urgent',
                            'audience' => 'employee',
                            'action_url' => $actionUrl,
                            'action_text' => 'مراجعة الحالة',
                            'data' => [
                                'reminder_kind' => 'sales_sla_escalation',
                                'sales_rep_id' => $rep->id,
                                'sales_rep_name' => $rep->name,
                                'overdue_followups' => $overdueCount,
                                'threshold' => $threshold,
                                'date' => now()->toDateString(),
                            ],
                        ]);
                        $adminNotifs++;
                    }
                }
            }
        }

        $this->info("Sales reminders sent. employee={$employeeNotifs}, admin={$adminNotifs}");

        return self::SUCCESS;
    }

    private function alreadySentToday(int $userId, string $title, string $actionUrl): bool
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->where('title', $title)
            ->where('action_url', $actionUrl)
            ->whereDate('created_at', today())
            ->exists();
    }

    private function staleOpenCount(int $userId): int
    {
        $d = SalesLead::STALE_CONTACT_DAYS;
        $base = SalesLead::query()->forAssignee($userId)->openPipeline();

        return (clone $base)->where(function ($q) use ($d) {
            $q->where(function ($q2) use ($d) {
                $q2->whereNull('last_contacted_at')
                    ->where('created_at', '<', now()->subDays($d));
            })->orWhere('last_contacted_at', '<', now()->subDays($d));
        })->count();
    }
}
