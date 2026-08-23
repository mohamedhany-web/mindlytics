<?php

namespace App\Services;

use App\Models\EmployeeSalaryDeduction;
use App\Models\EmployeeTask;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\ModeratorMontageRequest;
use App\Models\Notification;
use App\Models\User;
use App\Support\MarketingPlanSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketingPlanEventAutomationService
{
    public function __construct(
        protected EmployeeTaskAssignmentNotifier $taskNotifier
    ) {}

    public static function contentTypeLabels(): array
    {
        return [
            'post' => 'منشور / بوست',
            'story' => 'ستوري',
            'reel' => 'ريلز / فيديو قصير',
            'graphic_design' => 'تصميم جرافيك',
            'video_montage' => 'محرر فيديو / مونتاج',
            'other' => 'أخرى',
        ];
    }

    public function afterEventSaved(ModeratorMarketingCalendarEvent $event): void
    {
        if (! MarketingPlanSettings::automationEnabled()) {
            return;
        }

        $event->loadMissing(['plan.moderator', 'platform.employeeJobs']);

        if ($event->assigned_employee_id === null) {
            $assignee = $this->resolveAssignee($event);
            if ($assignee) {
                $event->update(['assigned_employee_id' => $assignee->id]);
                $event->refresh();
            }
        }

        if (MarketingPlanSettings::autoCreateTasks() && in_array($event->content_type, ['graphic_design', 'video_montage'], true)) {
            if ($event->content_type === 'video_montage') {
                $this->syncMontageRequest($event);
            } else {
                $this->syncProductionTask($event);
            }
        }
    }

    public function resolveAssignee(ModeratorMarketingCalendarEvent $event): ?User
    {
        $event->loadMissing(['plan', 'platform.employeeJobs']);

        if ($event->assigned_employee_id) {
            return User::query()->find($event->assigned_employee_id);
        }

        $jobCode = match ($event->content_type) {
            'graphic_design' => 'designer',
            'video_montage' => 'video_editing',
            default => null,
        };

        if ($event->platform && $event->platform->employeeJobs->isNotEmpty()) {
            foreach ($event->platform->employeeJobs as $job) {
                $emp = $this->pickEmployeeForJob($job->id);
                if ($emp) {
                    return $emp;
                }
            }
        }

        if ($jobCode) {
            return $this->pickEmployeeByJobCode($jobCode);
        }

        return $event->plan?->moderator;
    }

    public function syncProductionTask(ModeratorMarketingCalendarEvent $event): ?EmployeeTask
    {
        $event->loadMissing(['plan', 'platform']);

        $assigneeId = $event->assigned_employee_id ?? $this->resolveAssignee($event)?->id;
        if (! $assigneeId) {
            return null;
        }

        $taskType = $event->content_type === 'video_montage' ? 'video_editing' : 'design';
        $moderatorId = $event->plan?->moderator_id;
        $deadline = $event->starts_at?->copy()->subDay() ?? now()->addDays(2);

        if ($event->employee_task_id) {
            $task = EmployeeTask::query()->find($event->employee_task_id);
            if ($task) {
                $task->update([
                    'employee_id' => $assigneeId,
                    'title' => $this->taskTitle($event),
                    'description' => $this->taskDescription($event),
                    'task_type' => $taskType,
                    'deadline' => $deadline->toDateString(),
                    'assigned_by' => $moderatorId,
                ]);

                return $task;
            }
        }

        $task = EmployeeTask::create([
            'employee_id' => $assigneeId,
            'assigned_by' => $moderatorId,
            'title' => $this->taskTitle($event),
            'description' => $this->taskDescription($event),
            'task_type' => $taskType,
            'priority' => 'high',
            'status' => 'pending',
            'deadline' => $deadline->toDateString(),
            'notes' => 'مهمة تلقائية من خطة تسويق #'.$event->plan_id.' — حدث #'.$event->id,
            'marketing_event_id' => $event->id,
        ]);

        $event->update(['employee_task_id' => $task->id]);
        $this->taskNotifier->notifyAssigned($task->fresh());

        return $task;
    }

    public function syncMontageRequest(ModeratorMarketingCalendarEvent $event): ?EmployeeTask
    {
        $event->loadMissing(['plan', 'platform']);

        $assigneeId = $event->assigned_employee_id ?? $this->resolveAssignee($event)?->id;
        if (! $assigneeId) {
            return null;
        }

        $moderatorId = $event->plan?->moderator_id;
        $deadline = $event->starts_at?->copy()->subDay() ?? now()->addDays(2);
        $requirements = trim(($event->body ?? '')."\n\n".'موعد النشر المخطط: '.($event->starts_at?->format('Y-m-d H:i') ?? '—'));
        if ($event->platform) {
            $requirements = 'المنصة: '.$event->platform->displayName()."\n\n".$requirements;
        }

        if ($event->employee_task_id) {
            $task = EmployeeTask::query()->find($event->employee_task_id);
            if ($task && $task->montage_request_id) {
                $montageRequest = ModeratorMontageRequest::query()->find($task->montage_request_id);
                if ($montageRequest) {
                    $montageRequest->update([
                        'montage_employee_id' => $assigneeId,
                        'title' => $this->taskTitle($event),
                        'requirements' => $requirements,
                        'deadline_at' => $deadline,
                        'priority' => 'high',
                    ]);
                    $task->update([
                        'employee_id' => $assigneeId,
                        'title' => $this->taskTitle($event),
                        'description' => $requirements,
                        'deadline' => $deadline->toDateString(),
                        'assigned_by' => $moderatorId,
                    ]);

                    return $task;
                }
            }
        }

        $montageRequest = ModeratorMontageRequest::create([
            'moderator_id' => $moderatorId,
            'montage_employee_id' => $assigneeId,
            'title' => $this->taskTitle($event),
            'description' => $event->body,
            'requirements' => $requirements,
            'priority' => 'high',
            'deadline_at' => $deadline,
            'status' => ModeratorMontageRequest::STATUS_PENDING,
        ]);

        $task = EmployeeTask::create([
            'employee_id' => $assigneeId,
            'assigned_by' => $moderatorId,
            'title' => $this->taskTitle($event),
            'description' => $requirements,
            'task_type' => 'video_editing',
            'priority' => 'high',
            'status' => 'pending',
            'deadline' => $deadline->toDateString(),
            'notes' => 'مهمة تلقائية من خطة تسويق #'.$event->plan_id.' — حدث #'.$event->id,
            'marketing_event_id' => $event->id,
            'montage_request_id' => $montageRequest->id,
            'flexible_video_delivery' => true,
        ]);

        $montageRequest->update(['employee_task_id' => $task->id]);
        $event->update(['employee_task_id' => $task->id]);
        $this->taskNotifier->notifyAssigned($task->fresh());

        return $task;
    }

    public function confirmExecution(ModeratorMarketingCalendarEvent $event, User $user): bool
    {
        if (! $this->userCanConfirm($event, $user)) {
            return false;
        }

        $event->update([
            'execution_confirmed_at' => now(),
            'execution_confirmed_by' => $user->id,
            'status' => 'published',
        ]);

        return true;
    }

    public function userCanConfirm(ModeratorMarketingCalendarEvent $event, User $user): bool
    {
        $event->loadMissing('plan');

        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $event->assigned_employee_id === (int) $user->id) {
            return true;
        }

        if ($event->plan && (int) $event->plan->moderator_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    public function sendTodayReminders(Carbon $date): int
    {
        if (! MarketingPlanSettings::automationEnabled()) {
            return 0;
        }

        $count = 0;
        $events = ModeratorMarketingCalendarEvent::query()
            ->whereDate('starts_at', $date)
            ->whereIn('status', ['scheduled', 'draft'])
            ->where('requires_confirmation', true)
            ->whereNull('execution_confirmed_at')
            ->whereNull('reminder_sent_at')
            ->with(['plan.moderator', 'platform', 'assignee'])
            ->get();

        foreach ($events as $event) {
            $recipients = collect();
            if ($event->assignee) {
                $recipients->push($event->assignee);
            }
            if ($event->plan?->moderator) {
                $recipients->push($event->plan->moderator);
            }

            foreach ($recipients->unique('id') as $emp) {
                Notification::create([
                    'user_id' => $emp->id,
                    'sender_id' => null,
                    'title' => 'تذكير: محتوى تسويق اليوم',
                    'message' => 'اليوم مطلوب: «'.$event->title.'»'.($event->platform ? ' — '.$event->platform->displayName() : '').'. أكّد التنفيذ بعد الرفع.',
                    'type' => 'reminder',
                    'priority' => 'high',
                    'audience' => 'employee',
                    'action_url' => route('employee.marketing-today.index'),
                    'action_text' => 'تأكيد التنفيذ',
                    'data' => ['kind' => 'marketing_event_reminder', 'event_id' => $event->id],
                ]);
                $count++;
            }

            $event->update(['reminder_sent_at' => now()]);
        }

        return $count;
    }

    public function applyExecutionPenalties(Carbon $date): int
    {
        if (! MarketingPlanSettings::penaltyEnabled()) {
            return 0;
        }

        $applied = 0;
        $events = ModeratorMarketingCalendarEvent::query()
            ->whereDate('starts_at', $date)
            ->where('requires_confirmation', true)
            ->whereNull('execution_confirmed_at')
            ->whereNull('execution_penalty_deduction_id')
            ->whereIn('status', ['scheduled', 'draft', 'idea'])
            ->with(['plan', 'assignee'])
            ->get();

        foreach ($events as $event) {
            $employee = $event->assignee ?? $event->plan?->moderator;
            if (! $employee) {
                continue;
            }

            $deduction = EmployeeSalaryDeduction::createWithAutoDeductionNumber([
                'employee_id' => $employee->id,
                'title' => 'غرامة — عدم تأكيد تنفيذ خطة التسويق',
                'description' => 'لم يُؤكَّد تنفيذ «'.$event->title.'» لتاريخ '.$date->format('Y-m-d'),
                'amount' => MarketingPlanSettings::penaltyAmount(),
                'type' => 'penalty',
                'deduction_date' => $date,
                'status' => 'applied',
                'created_by' => null,
            ]);

            $event->update(['execution_penalty_deduction_id' => $deduction->id]);

            Notification::create([
                'user_id' => $employee->id,
                'sender_id' => null,
                'title' => 'غرامة — خطة تسويق',
                'message' => 'لم تُؤكَّد مهمة «'.$event->title.'» اليوم. خصم '.number_format((float) $deduction->amount, 2).' ج.م.',
                'type' => 'warning',
                'priority' => 'high',
                'audience' => 'employee',
                'action_url' => route('employee.marketing-today.index'),
                'action_text' => 'عرض المهام',
                'data' => ['kind' => 'marketing_event_penalty', 'event_id' => $event->id],
            ]);

            $applied++;
        }

        return $applied;
    }

    /**
     * @return \Illuminate\Support\Collection<int, ModeratorMarketingCalendarEvent>
     */
    public function todayEventsForUser(User $user, ?Carbon $date = null)
    {
        $date = ($date ?? today())->toDateString();

        return ModeratorMarketingCalendarEvent::query()
            ->whereDate('starts_at', $date)
            ->where(function ($q) use ($user) {
                $q->where('assigned_employee_id', $user->id)
                    ->orWhereHas('plan', fn ($p) => $p->where('moderator_id', $user->id));
            })
            ->with(['plan', 'platform', 'employeeTask', 'assignee'])
            ->orderBy('starts_at')
            ->get();
    }

    private function taskTitle(ModeratorMarketingCalendarEvent $event): string
    {
        $type = self::contentTypeLabels()[$event->content_type] ?? $event->content_type;

        return $type.' — '.$event->title;
    }

    private function taskDescription(ModeratorMarketingCalendarEvent $event): string
    {
        $parts = [];
        if ($event->platform) {
            $parts[] = 'المنصة: '.$event->platform->displayName();
        }
        if ($event->body) {
            $parts[] = $event->body;
        }
        $parts[] = 'موعد النشر المخطط: '.($event->starts_at?->format('Y-m-d H:i') ?? '—');

        return implode("\n\n", $parts);
    }

    private function pickEmployeeByJobCode(string $code): ?User
    {
        return User::query()
            ->employees()
            ->where('is_active', true)
            ->whereHas('employeeJob', fn ($q) => $q->whereRaw('LOWER(code) = ?', [strtolower($code)]))
            ->orderBy('id')
            ->first();
    }

    private function pickEmployeeForJob(int $jobId): ?User
    {
        return User::query()
            ->employees()
            ->where('is_active', true)
            ->where('employee_job_id', $jobId)
            ->orderBy('id')
            ->first();
    }
}
