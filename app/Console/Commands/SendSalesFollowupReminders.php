<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\SalesLead;
use App\Models\SalesTeam;
use App\Models\User;
use App\Services\SalesTeamService;
use Illuminate\Console\Command;

class SendSalesFollowupReminders extends Command
{
    protected $signature = 'sales:send-followup-reminders {--sla-overdue-threshold=3 : Overdue follow-ups threshold for admin escalation}';

    protected $description = 'Send daily sales follow-up reminders for reps, managers, and SLA escalation alerts for admins';

    public function handle(): int
    {
        $threshold = max(1, (int) $this->option('sla-overdue-threshold'));
        $today = today();
        $salesReps = User::salesEmployees()->where('is_active', true)->get(['id', 'name']);

        $employeeNotifs = 0;
        $managerNotifs = 0;
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
                $actionUrl = $overdueCount > 0
                    ? route('employee.sales.follow-ups.index', ['filter' => 'overdue'])
                    : ($todayCount > 0
                        ? route('employee.sales.follow-ups.index', ['filter' => 'today'])
                        : route('employee.sales.follow-ups.index', ['filter' => 'stale']));

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
                        'action_text' => 'فتح متابعاتي',
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

        $managerNotifs = $this->notifySalesManagers();

        $this->info("Sales reminders sent. employee={$employeeNotifs}, manager={$managerNotifs}, admin={$adminNotifs}");

        return self::SUCCESS;
    }

    private function notifySalesManagers(): int
    {
        $sent = 0;
        $teamService = app(SalesTeamService::class);
        $staleDays = SalesLead::STALE_CONTACT_DAYS;

        $managers = User::salesManagers()->where('is_active', true)->get(['id', 'name']);

        foreach ($managers as $manager) {
            $team = $teamService->teamFor($manager);
            if (! $team instanceof SalesTeam) {
                continue;
            }

            $memberIds = $teamService->memberUserIds($team);
            if ($memberIds === []) {
                continue;
            }

            $base = SalesLead::query()->whereIn('assigned_to', $memberIds)->openPipeline();

            $overdueCount = (clone $base)
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count();

            $todayCount = (clone $base)
                ->whereNotNull('next_follow_up_at')
                ->whereDate('next_follow_up_at', today())
                ->count();

            $staleCount = (clone $base)->where(function ($q) use ($staleDays) {
                $q->where(function ($q2) use ($staleDays) {
                    $q2->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($staleDays));
                })->orWhere('last_contacted_at', '<', now()->subDays($staleDays));
            })->count();

            if (($overdueCount + $todayCount + $staleCount) === 0) {
                continue;
            }

            $title = 'رقابة متابعات الفريق';
            $message = "فريقك: {$overdueCount} متأخرة، {$todayCount} اليوم، و {$staleCount} بلا تواصل.";
            $actionUrl = $overdueCount > 0
                ? route('employee.sales-manager.follow-ups.index', ['filter' => 'overdue'])
                : ($staleCount > 0
                    ? route('employee.sales-manager.follow-ups.index', ['filter' => 'stale'])
                    : route('employee.sales-manager.follow-ups.index', ['filter' => 'today']));

            if ($this->alreadySentToday((int) $manager->id, $title, $actionUrl)) {
                continue;
            }

            Notification::create([
                'user_id' => $manager->id,
                'sender_id' => null,
                'title' => $title,
                'message' => $message,
                'type' => 'reminder',
                'priority' => $overdueCount > 0 ? 'high' : 'normal',
                'audience' => 'employee',
                'action_url' => $actionUrl,
                'action_text' => 'فتح رقابة المتابعات',
                'data' => [
                    'reminder_kind' => 'sales_manager_followup_daily',
                    'team_id' => $team->id,
                    'overdue_followups' => $overdueCount,
                    'today_followups' => $todayCount,
                    'stale_open_leads' => $staleCount,
                    'date' => now()->toDateString(),
                ],
            ]);
            $sent++;
        }

        return $sent;
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
