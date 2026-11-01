<?php

namespace App\Domains\Sales\Services;

use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesPlannedActivity;
use App\Domains\Sales\Support\CrmScope;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * جدولة وتنفيذ الأنشطة المخططة (docs/SALES_CRM.md — الأنشطة).
 * - الجدولة على عميل بتحدّث «المتابعة التالية» للعميل لو الموعد أقرب، عشان شاشات المتابعات القديمة تفضل صح.
 * - التنفيذ بيسجّل نشاط في sales_activities (يتحسب في KPIs والتقرير اليومي)، ونتيجة المكالمة بتحرّك المرحلة لصاحب العميل.
 */
class SalesPlannedActivityService
{
    public function __construct(
        private SalesCallOutcomeService $outcomes,
        private SalesDailyReportService $dailyReports,
    ) {}

    /**
     * @param  array{kind: string, title: string, description?: ?string, sales_lead_id?: ?int, student_user_id?: ?int, assigned_to: int, starts_at: string, duration_minutes?: ?int, priority?: ?string}  $data
     */
    public function schedule(CrmScope $scope, array $data): SalesPlannedActivity
    {
        [$lead, $student] = $this->related($scope, $data);
        $assignee = $this->assignee($scope, (int) $data['assigned_to'], $lead);

        return DB::transaction(function () use ($scope, $data, $lead, $student, $assignee) {
            $activity = SalesPlannedActivity::create([
                'kind' => $data['kind'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'sales_lead_id' => $lead?->id,
                'student_user_id' => $student?->id ?? $lead?->student_user_id,
                'assigned_to' => $assignee->id,
                'created_by' => $scope->user->id,
                'starts_at' => Carbon::parse($data['starts_at']),
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'priority' => $data['priority'] ?? 'normal',
                'status' => 'planned',
            ]);

            if ($lead) {
                $this->pullFollowUpForward($lead, $activity);
            }

            SalesAuditService::log('sales_planned_activity_created', $activity, null,
                $activity->only(['kind', 'title', 'starts_at', 'assigned_to', 'sales_lead_id', 'student_user_id']),
                'جدولة '.SalesPlannedActivity::kindLabel($activity->kind).': '.$activity->title);

            return $activity;
        });
    }

    /** @param  array{title: string, description?: ?string, assigned_to: int, starts_at: string, duration_minutes?: ?int, priority?: ?string}  $data */
    public function reschedule(CrmScope $scope, SalesPlannedActivity $activity, array $data): SalesPlannedActivity
    {
        $assignee = $this->assignee($scope, (int) $data['assigned_to'], $activity->lead);
        $old = $activity->only(['title', 'starts_at', 'assigned_to', 'duration_minutes', 'priority']);
        $oldStart = $activity->starts_at?->copy();

        DB::transaction(function () use ($activity, $data, $assignee, $oldStart) {
            $activity->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'assigned_to' => $assignee->id,
                'starts_at' => Carbon::parse($data['starts_at']),
                'duration_minutes' => $data['duration_minutes'] ?? null,
                'priority' => $data['priority'] ?? 'normal',
            ]);

            if ($activity->lead) {
                $this->releaseFollowUp($activity->lead, $oldStart, $activity->id);
                $this->pullFollowUpForward($activity->lead, $activity);
            }
        });

        SalesAuditService::log('sales_planned_activity_updated', $activity, $old,
            $activity->only(['title', 'starts_at', 'assigned_to', 'duration_minutes', 'priority']),
            'تعديل '.SalesPlannedActivity::kindLabel($activity->kind).': '.$activity->title);

        return $activity;
    }

    /**
     * @param  array{status: string, outcome?: ?string, result_notes?: ?string, apply_stage?: bool}  $data  status: done | missed
     */
    public function complete(CrmScope $scope, SalesPlannedActivity $activity, array $data): SalesPlannedActivity
    {
        if (! $scope->canComplete($activity)) {
            abort(403);
        }
        $done = $data['status'] === 'done';
        $outcome = $activity->kind === 'call' && $done ? ($data['outcome'] ?? null) : null;
        if ($activity->kind === 'call' && $done && ! $outcome) {
            throw ValidationException::withMessages(['outcome' => 'اختر نتيجة المكالمة.']);
        }

        $actor = $scope->user;
        $lead = $activity->lead;

        DB::transaction(function () use ($activity, $data, $done, $outcome, $actor, $lead) {
            $history = null;
            if ($done && $lead) {
                $history = SalesActivity::create([
                    'sales_lead_id' => $lead->id,
                    'user_id' => $actor->id,
                    'type' => match ($activity->kind) {
                        'call' => 'call',
                        'meeting' => 'meeting',
                        default => 'note',
                    },
                    'outcome' => $outcome,
                    'duration_seconds' => $activity->kind === 'call' && $activity->duration_minutes ? $activity->duration_minutes * 60 : null,
                    'title' => SalesPlannedActivity::kindLabel($activity->kind).': '.$activity->title,
                    'body' => $data['result_notes'] ?? null,
                    'meta' => ['planned_activity_id' => $activity->id],
                ]);
                if ($activity->kind !== 'task') {
                    $lead->touchLastContactFromActivity($history->type);
                }
            }

            $activity->update([
                'status' => $done ? 'done' : 'missed',
                'outcome' => $outcome,
                'result_notes' => $data['result_notes'] ?? null,
                'completed_at' => now(),
                'completed_by' => $actor->id,
                'sales_activity_id' => $history?->id,
            ]);

            if ($lead) {
                $this->releaseFollowUp($lead, $activity->starts_at, $activity->id);
            }
        });

        // تحريك المرحلة بعد الحفظ (زي التسجيل السريع): لصاحب العميل أو الأدمن فقط، وما يفشلش التنفيذ لو الانتقال مش مسموح.
        if ($outcome && $lead && ($data['apply_stage'] ?? true) && $scope->canWorkOn($lead->fresh())) {
            $this->outcomes->apply($lead->fresh(), $outcome, $actor);
        }

        SalesAuditService::log('sales_planned_activity_completed', $activity, null,
            $activity->only(['status', 'outcome', 'result_notes']),
            SalesPlannedActivity::kindLabel($activity->kind).' '.SalesPlannedActivity::statusLabel($activity->status).': '.$activity->title);

        if ($done && $lead && $actor->isSalesEmployee()) {
            try {
                $this->dailyReports->syncAutoDraft($actor, today());
            } catch (\Throwable $e) {
                Log::warning('sales.daily_report.sync_failed', ['user_id' => $actor->id, 'message' => $e->getMessage()]);
            }
        }

        return $activity;
    }

    public function cancel(CrmScope $scope, SalesPlannedActivity $activity): void
    {
        if (! $scope->canEditActivity($activity)) {
            abort(403);
        }

        DB::transaction(function () use ($scope, $activity) {
            $activity->update(['status' => 'cancelled', 'completed_at' => now(), 'completed_by' => $scope->user->id]);
            if ($activity->lead) {
                $this->releaseFollowUp($activity->lead, $activity->starts_at, $activity->id);
            }
        });

        SalesAuditService::log('sales_planned_activity_cancelled', $activity, null, ['status' => 'cancelled'],
            'إلغاء '.SalesPlannedActivity::kindLabel($activity->kind).': '.$activity->title);
    }

    /** @return array{0: ?SalesLead, 1: ?User} */
    private function related(CrmScope $scope, array $data): array
    {
        $lead = null;
        if (! empty($data['sales_lead_id'])) {
            $lead = SalesLead::query()->find((int) $data['sales_lead_id']);
            if (! $lead || ! $scope->canSee($lead)) {
                throw ValidationException::withMessages(['sales_lead_id' => 'العميل ده خارج نطاقك.']);
            }
        }

        $student = null;
        if (! empty($data['student_user_id'])) {
            $student = User::query()->find((int) $data['student_user_id']);
            if (! $student || ! $scope->canSeeStudent($student)) {
                throw ValidationException::withMessages(['student_user_id' => 'الطالب ده خارج نطاقك.']);
            }
        }

        if (! $lead && ! $student && $data['kind'] !== 'task') {
            throw ValidationException::withMessages(['sales_lead_id' => 'المكالمة والاجتماع لازم يكونوا على عميل أو طالب.']);
        }

        return [$lead, $student];
    }

    private function assignee(CrmScope $scope, int $userId, ?SalesLead $lead): User
    {
        $user = $scope->assignableUsers()->firstWhere('id', $userId);
        if (! $user) {
            throw ValidationException::withMessages(['assigned_to' => 'اختر موظف من نطاقك.']);
        }

        return $user instanceof User ? $user : User::query()->findOrFail($userId);
    }

    /** موعد أقرب من «المتابعة التالية» الحالية ← يبقى هو المتابعة التالية. */
    private function pullFollowUpForward(SalesLead $lead, SalesPlannedActivity $activity): void
    {
        if (! $lead->isOpen() || $activity->kind === 'task') {
            return;
        }
        if ($lead->next_follow_up_at && $lead->next_follow_up_at->lte($activity->starts_at)) {
            return;
        }

        $lead->update([
            'next_follow_up_at' => $activity->starts_at,
            'follow_up_channel' => $activity->kind === 'meeting' ? 'meeting' : 'call',
        ]);
    }

    /** لو «المتابعة التالية» كانت موعد النشاط ده ← تنتقل لأقرب نشاط مخطط تاني على العميل (أو تتشال). */
    private function releaseFollowUp(SalesLead $lead, ?Carbon $start, int $exceptId): void
    {
        $lead->refresh();
        if (! $start || ! $lead->next_follow_up_at || $lead->next_follow_up_at->format('Y-m-d H:i') !== $start->format('Y-m-d H:i')) {
            return;
        }

        $next = SalesPlannedActivity::query()->planned()
            ->where('sales_lead_id', $lead->id)->whereKeyNot($exceptId)->where('kind', '!=', 'task')
            ->orderBy('starts_at')->first();

        $lead->update([
            'next_follow_up_at' => $next?->starts_at,
            'follow_up_channel' => $next ? ($next->kind === 'meeting' ? 'meeting' : 'call') : $lead->follow_up_channel,
        ]);
    }
}
