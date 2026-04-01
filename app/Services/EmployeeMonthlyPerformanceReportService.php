<?php

namespace App\Services;

use App\Models\DesignTaskCycle;
use App\Models\EmployeeTask;
use App\Models\EmployeeTaskDeliverable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeMonthlyPerformanceReportService
{
    /**
     * تحليل أداء الموظفين لشهر كامل (مهام + دورات تصميم) مع استعلامات مجمّعة.
     *
     * @return array{
     *   start: Carbon,
     *   end: Carbon,
     *   rows: list<array<string, mixed>>,
     *   summary: array<string, mixed>,
     *   design_cycles: Collection<int, DesignTaskCycle>,
     *   completed_tasks: Collection<int, EmployeeTask>
     * }
     */
    public function analyze(Carbon $start, Carbon $end): array
    {
        $employees = User::query()
            ->employees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->orderBy('name')
            ->get();

        $empIds = $employees->pluck('id')->all();

        if ($empIds === []) {
            return [
                'start' => $start,
                'end' => $end,
                'rows' => [],
                'summary' => $this->emptySummary(),
                'design_cycles' => collect(),
                'completed_tasks' => collect(),
            ];
        }

        $assignedInMonth = EmployeeTask::query()
            ->whereIn('employee_id', $empIds)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('employee_id, COUNT(*) as c')
            ->groupBy('employee_id')
            ->pluck('c', 'employee_id');

        $completedTasks = EmployeeTask::query()
            ->whereIn('employee_id', $empIds)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end])
            ->get(['id', 'employee_id', 'title', 'task_type', 'priority', 'deadline', 'completed_at', 'created_at']);

        $completedByEmp = $completedTasks->groupBy('employee_id');

        $openOverdue = EmployeeTask::query()
            ->whereIn('employee_id', $empIds)
            ->whereIn('status', ['pending', 'in_progress', 'on_hold'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', $end->toDateString())
            ->selectRaw('employee_id, COUNT(*) as c')
            ->groupBy('employee_id')
            ->pluck('c', 'employee_id');

        $deliverablesByEmp = EmployeeTaskDeliverable::query()
            ->whereBetween('employee_task_deliverables.created_at', [$start, $end])
            ->join('employee_tasks', 'employee_tasks.id', '=', 'employee_task_deliverables.task_id')
            ->whereIn('employee_tasks.employee_id', $empIds)
            ->selectRaw('employee_tasks.employee_id as employee_id, COUNT(*) as c')
            ->groupBy('employee_tasks.employee_id')
            ->pluck('c', 'employee_id');

        $designerCycles = DesignTaskCycle::query()
            ->whereIn('designer_employee_id', $empIds)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $designerCyclesByEmp = $designerCycles->groupBy('designer_employee_id');

        $moderatorCreated = DesignTaskCycle::query()
            ->whereIn('moderator_id', $empIds)
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('moderator_id, COUNT(*) as c')
            ->groupBy('moderator_id')
            ->pluck('c', 'moderator_id');

        $moderatorCompletedCycles = DesignTaskCycle::query()
            ->whereIn('moderator_id', $empIds)
            ->where('status', DesignTaskCycle::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$start, $end])
            ->whereNotNull('created_at')
            ->whereNotNull('completed_at')
            ->get(['moderator_id', 'created_at', 'completed_at']);

        $moderatorCompletedCount = $moderatorCompletedCycles->groupBy('moderator_id')->map->count();

        $moderatorAvgCycleDays = $moderatorCompletedCycles
            ->groupBy('moderator_id')
            ->map(function (Collection $group) {
                $days = [];
                foreach ($group as $c) {
                    $d = $c->created_at->diffInDays($c->completed_at);
                    if ($d >= 0 && $d < 2000) {
                        $days[] = $d;
                    }
                }

                return count($days) ? round(array_sum($days) / count($days), 2) : null;
            });

        $moderatorCancelled = DesignTaskCycle::query()
            ->whereIn('moderator_id', $empIds)
            ->where('status', DesignTaskCycle::STATUS_CANCELLED)
            ->whereBetween('updated_at', [$start, $end])
            ->selectRaw('moderator_id, COUNT(*) as c')
            ->groupBy('moderator_id')
            ->pluck('c', 'moderator_id');

        $designCyclesForExport = DesignTaskCycle::query()
            ->with(['moderator', 'designer', 'designerTask'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('created_at', [$start, $end])
                    ->orWhereBetween('completed_at', [$start, $end])
                    ->orWhereBetween('designer_submitted_at', [$start, $end]);
            })
            ->orderBy('id')
            ->get();

        $rows = [];

        foreach ($employees as $emp) {
            $id = $emp->id;
            $tasksCompleted = $completedByEmp->get($id, collect());

            $onTime = 0;
            $late = 0;
            $noDeadline = 0;
            $sumHours = 0.0;
            $countWithDuration = 0;
            $byTypeCompleted = ['design' => 0, 'video_editing' => 0, 'sales' => 0, 'other' => 0];

            foreach ($tasksCompleted as $t) {
                $ot = $this->taskOnTimeFlag($t);
                if ($ot === true) {
                    $onTime++;
                } elseif ($ot === false) {
                    $late++;
                } else {
                    $noDeadline++;
                }
                if ($t->created_at && $t->completed_at) {
                    $sumHours += $t->created_at->diffInHours($t->completed_at);
                    $countWithDuration++;
                }
                $type = (string) $t->task_type;
                if (isset($byTypeCompleted[$type])) {
                    $byTypeCompleted[$type]++;
                } else {
                    $byTypeCompleted['other']++;
                }
            }

            $avgHours = $countWithDuration > 0 ? round($sumHours / $countWithDuration, 2) : null;
            $judged = $onTime + $late;
            $taskOnTimeRate = $judged > 0 ? round(100 * $onTime / $judged, 1) : null;

            $assigned = (int) ($assignedInMonth[$id] ?? 0);
            $completedCount = $tasksCompleted->count();
            $completionRate = $assigned > 0 ? round(100 * $completedCount / $assigned, 1) : null;

            $asDesignerCycles = $designerCyclesByEmp->get($id, collect());
            $designerSubmittedInMonth = $asDesignerCycles->filter(fn ($c) => $c->designer_submitted_at
                && $c->designer_submitted_at->between($start, $end));

            $designerOnTime = 0;
            $designerLate = 0;
            foreach ($designerSubmittedInMonth as $c) {
                if ($c->designer_submitted_at && $c->deadline_at) {
                    if ($c->designer_submitted_at->lte($c->deadline_at)) {
                        $designerOnTime++;
                    } else {
                        $designerLate++;
                    }
                }
            }
            $dj = $designerOnTime + $designerLate;
            $designerOnTimeRate = $dj > 0 ? round(100 * $designerOnTime / $dj, 1) : null;

            $rows[] = [
                'user' => $emp,
                'tasks_assigned_in_month' => $assigned,
                'tasks_completed_in_month' => $completedCount,
                'tasks_completion_rate_pct' => $completionRate,
                'tasks_on_time' => $onTime,
                'tasks_late' => $late,
                'tasks_no_deadline_completed' => $noDeadline,
                'tasks_on_time_rate_pct' => $taskOnTimeRate,
                'avg_completion_hours' => $avgHours,
                'open_overdue_tasks_end_of_month' => (int) ($openOverdue[$id] ?? 0),
                'deliverables_submitted' => (int) ($deliverablesByEmp[$id] ?? 0),
                'tasks_completed_design' => $byTypeCompleted['design'],
                'tasks_completed_video' => $byTypeCompleted['video_editing'],
                'tasks_completed_sales' => $byTypeCompleted['sales'],
                'tasks_completed_other' => $byTypeCompleted['other'],
                'design_cycles_as_designer' => $asDesignerCycles->count(),
                'designer_submissions_in_month' => $designerSubmittedInMonth->count(),
                'designer_on_time' => $designerOnTime,
                'designer_late' => $designerLate,
                'designer_on_time_rate_pct' => $designerOnTimeRate,
                'design_cycles_created_as_moderator' => (int) ($moderatorCreated[$id] ?? 0),
                'design_cycles_completed_as_moderator' => (int) ($moderatorCompletedCount[$id] ?? 0),
                'moderator_avg_cycle_completion_days' => $moderatorAvgCycleDays[$id] ?? null,
                'design_cycles_cancelled_as_moderator' => (int) ($moderatorCancelled[$id] ?? 0),
            ];
        }

        $summary = array_merge($this->buildSummary($rows), [
            'design_cycles_touched_month' => $designCyclesForExport->count(),
        ]);

        return [
            'start' => $start,
            'end' => $end,
            'rows' => $rows,
            'summary' => $summary,
            'design_cycles' => $designCyclesForExport,
            'completed_tasks' => $completedTasks,
        ];
    }

    private function emptySummary(): array
    {
        return [
            'employees' => 0,
            'tasks_assigned' => 0,
            'tasks_completed' => 0,
            'tasks_on_time' => 0,
            'tasks_late' => 0,
            'tasks_on_time_rate_pct' => null,
            'deliverables' => 0,
            'design_cycles_touched_month' => 0,
            'designer_submissions_month' => 0,
            'designer_on_time' => 0,
            'designer_late' => 0,
            'designer_on_time_rate_pct' => null,
            'moderator_cycles_completed' => 0,
            'open_overdue_tasks' => 0,
            'design_cycles_touched_month' => 0,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     */
    private function buildSummary(array $rows): array
    {
        if ($rows === []) {
            return $this->emptySummary();
        }

        $s = $this->emptySummary();
        $s['employees'] = count($rows);

        foreach ($rows as $r) {
            $s['tasks_assigned'] += $r['tasks_assigned_in_month'];
            $s['tasks_completed'] += $r['tasks_completed_in_month'];
            $s['tasks_on_time'] += $r['tasks_on_time'];
            $s['tasks_late'] += $r['tasks_late'];
            $s['deliverables'] += $r['deliverables_submitted'];
            $s['designer_submissions_month'] += $r['designer_submissions_in_month'];
            $s['designer_on_time'] += $r['designer_on_time'];
            $s['designer_late'] += $r['designer_late'];
            $s['moderator_cycles_completed'] += $r['design_cycles_completed_as_moderator'];
            $s['open_overdue_tasks'] += $r['open_overdue_tasks_end_of_month'];
        }

        $tj = $s['tasks_on_time'] + $s['tasks_late'];
        $s['tasks_on_time_rate_pct'] = $tj > 0 ? round(100 * $s['tasks_on_time'] / $tj, 1) : null;

        $dj = $s['designer_on_time'] + $s['designer_late'];
        $s['designer_on_time_rate_pct'] = $dj > 0 ? round(100 * $s['designer_on_time'] / $dj, 1) : null;

        return $s;
    }

    private function taskOnTimeFlag(EmployeeTask $t): ?bool
    {
        if (! $t->deadline || ! $t->completed_at) {
            return null;
        }
        $deadlineEnd = Carbon::parse($t->deadline->format('Y-m-d'))->endOfDay();

        return $t->completed_at->lte($deadlineEnd);
    }

    public static function taskTypeLabelArabic(?string $type): string
    {
        return match ($type) {
            'design' => 'تصميم',
            'video_editing' => 'مونتاج فيديو',
            'sales' => 'مبيعات',
            'design_moderator_delivery' => 'تسليم مشرف تصميم',
            default => $type ? 'عام / أخرى' : '—',
        };
    }
}
