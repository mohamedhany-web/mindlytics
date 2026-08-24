<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\EmployeeTask;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\SalesLead;
use App\Services\SalesTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeCalendarController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService
    ) {}

    /**
     * عرض التقويم
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }
        
        // جلب جميع الأحداث للموظف
        $events = $this->getEmployeeEvents($user);
        
        // إحصائيات
        $stats = [
            'total' => $events->count(),
            'tasks' => $events->where('type', 'task')->count(),
            'leaves' => $events->where('type', 'leave')->count(),
            'meetings' => $events->where('type', 'meeting')->count(),
            'followups' => $events->where('type', 'follow_up')->count(),
            'upcoming' => $events->where('start_date', '>=', now())->count(),
            'is_sales_manager' => $user->hasSalesManagerPortalAccess(),
        ];

        return view('employee.calendar.index', compact('events', 'stats'));
    }

    /**
     * API endpoint للحصول على الأحداث بصيغة JSON (لـ FullCalendar)
     */
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            return response()->json(['error' => 'غير مصرح'], 403);
        }
        
        $start = $request->get('start');
        $end = $request->get('end');

        $events = $this->getEmployeeEvents($user, $start, $end);
        
        // تحويل الأحداث إلى صيغة FullCalendar
        $calendarEvents = $events->map(function($event) {
            return [
                'id' => $event->id ?? $event->calendar_id,
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                'allDay' => $event->is_all_day ?? false,
                'color' => $event->color ?? $this->getEventColor($event->type),
                'type' => $event->type,
                'url' => $event->url ?? null,
                'description' => $event->description ?? null,
                'extendedProps' => [
                    'type' => $event->type,
                    'priority' => $event->priority ?? 'medium',
                    'location' => $event->location ?? null,
                    'assignee' => $event->assignee_name ?? null,
                    'description' => $event->description ?? null,
                ]
            ];
        });

        return response()->json($calendarEvents);
    }

    /**
     * جلب جميع الأحداث للموظف
     */
    private function getEmployeeEvents($user, $startDate = null, $endDate = null)
    {
        $events = collect();

        // 1. المهام (Tasks)
        $tasks = EmployeeTask::where('employee_id', $user->id)
            ->where(function($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where('deadline', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('deadline', '<=', $endDate);
                }
            })
            ->with(['assigner'])
            ->get();

        foreach ($tasks as $task) {
            $events->push((object)[
                'calendar_id' => 'task_' . $task->id,
                'id' => $task->id,
                'title' => 'مهمة: ' . $task->title,
                'description' => $task->description,
                'start_date' => $task->deadline ?? $task->created_at,
                'end_date' => $task->deadline ?? $task->created_at,
                'is_all_day' => true,
                'type' => 'task',
                'color' => $this->getTaskColor($task->status, $task->priority),
                'priority' => $task->priority ?? 'medium',
                'url' => route('employee.tasks.show', $task->id),
            ]);
        }

        // 2. الإجازات (Leaves)
        $leaves = \App\Models\LeaveRequest::where('employee_id', $user->id)
            ->where(function($q) use ($startDate, $endDate) {
                if ($startDate) {
                    $q->where('start_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q->where('end_date', '<=', $endDate);
                }
            })
            ->get();

        foreach ($leaves as $leave) {
            $events->push((object)[
                'calendar_id' => 'leave_' . $leave->id,
                'id' => $leave->id,
                'title' => 'إجازة: ' . ($leave->type ?? 'إجازة'),
                'description' => $leave->reason,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'is_all_day' => true,
                'type' => 'leave',
                'color' => $this->getLeaveColor($leave->status),
                'priority' => 'medium',
                'url' => route('employee.leaves.show', $leave->id),
            ]);
        }

        // 3. أحداث التقويم المخصصة (Calendar Events)
        $calendarEvents = CalendarEvent::getEmployeeEvents(
            $user->id,
            $startDate ?? now()->subMonths(1),
            $endDate ?? now()->addMonths(3)
        );

        foreach ($calendarEvents as $event) {
            $events->push((object)[
                'calendar_id' => 'calendar_' . $event->id,
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date ?? $event->start_date,
                'is_all_day' => $event->is_all_day,
                'type' => $event->type,
                'color' => $event->color,
                'priority' => $event->priority,
                'location' => $event->location,
            ]);
        }

        $mktQuery = ModeratorMarketingCalendarEvent::query()
            ->when(! $user->isBusinessDeveloper(), function ($q) use ($user) {
                $q->where(function ($inner) use ($user) {
                    $inner->where('assigned_employee_id', $user->id)
                        ->orWhereHas('plan', fn ($p) => $p->where('moderator_id', $user->id));
                });
            })
            ->with(['plan', 'platform']);

        if ($startDate) {
            $mktQuery->where('starts_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $mktQuery->where('starts_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        foreach ($mktQuery->get() as $mkt) {
            $color = $mkt->platform?->color_hex ?: '#db2777';
            $planTitle = $mkt->plan?->title ?? '';
            $url = $user->hasModeratorPortalAccess() && $mkt->plan_id
                ? route('employee.marketing-plans.show', $mkt->plan_id)
                : route('employee.marketing-today.index');
            $events->push((object)[
                'calendar_id' => 'mkt_'.$mkt->id,
                'id' => 'mkt_'.$mkt->id,
                'title' => 'تسويق: '.$mkt->title.($planTitle ? ' — '.$planTitle : ''),
                'description' => $mkt->body,
                'start_date' => $mkt->starts_at,
                'end_date' => $mkt->ends_at ?? $mkt->starts_at,
                'is_all_day' => false,
                'type' => 'marketing',
                'color' => $color,
                'priority' => 'medium',
                'url' => $url,
            ]);
        }

        // 5. متابعات المبيعات (Next Follow) — للمانجر: فريقه بالكامل
        $this->appendSalesFollowUpEvents($user, $events, $startDate, $endDate);

        // ترتيب الأحداث حسب التاريخ
        return $events->sortBy('start_date')->values();
    }

    /**
     * إضافة مواعيد المتابعة من العملاء المحتملين إلى التقويم.
     */
    private function appendSalesFollowUpEvents($user, $events, $startDate = null, $endDate = null): void
    {
        if (! $user->isSalesStaff()) {
            return;
        }

        $assigneeIds = $this->teamService->visibleAssigneeIds($user);
        if ($assigneeIds === []) {
            return;
        }

        $query = SalesLead::query()
            ->whereIn('assigned_to', $assigneeIds)
            ->openPipeline()
            ->whereNotNull('next_follow_up_at')
            ->with(['assignee:id,name']);

        if ($startDate) {
            $query->where('next_follow_up_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('next_follow_up_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $isManager = $user->hasSalesManagerPortalAccess();

        foreach ($query->get() as $lead) {
            $at = $lead->next_follow_up_at;
            if (! $at) {
                continue;
            }

            $assigneeName = $lead->assignee?->name;
            $title = 'متابعة: '.$lead->name;
            if ($isManager && $assigneeName) {
                $title .= ' — '.$assigneeName;
            }

            $url = $isManager
                ? route('employee.sales-manager.leads.show', $lead)
                : route('employee.sales.leads.show', $lead);

            $events->push((object) [
                'calendar_id' => 'followup_'.$lead->id,
                'id' => 'followup_'.$lead->id,
                'title' => $title,
                'description' => trim(($lead->phone ?? '').($assigneeName ? ' · '.$assigneeName : '')),
                'start_date' => $at,
                'end_date' => $at->copy()->addHour(),
                'is_all_day' => false,
                'type' => 'follow_up',
                'color' => $this->getFollowUpColor($lead),
                'priority' => $lead->isFollowUpOverdue() ? 'urgent' : 'medium',
                'url' => $url,
                'location' => $assigneeName,
                'assignee_name' => $assigneeName,
            ]);
        }
    }

    private function getFollowUpColor(SalesLead $lead): string
    {
        if ($lead->isFollowUpOverdue()) {
            return '#DC2626';
        }

        if ($lead->next_follow_up_at?->isToday()) {
            return '#F59E0B';
        }

        return '#0D9488';
    }

    /**
     * الحصول على لون الحدث حسب النوع
     */
    private function getEventColor($type)
    {
        return match($type) {
            'task' => '#3B82F6',
            'leave' => '#10B981',
            'meeting' => '#8B5CF6',
            'deadline' => '#DC2626',
            'review' => '#10B981',
            'personal' => '#6366F1',
            'marketing' => '#db2777',
            'follow_up' => '#0D9488',
            default => '#6B7280',
        };
    }

    /**
     * الحصول على لون المهمة حسب الحالة والأولوية
     */
    private function getTaskColor($status, $priority)
    {
        if ($status === 'completed') {
            return '#10B981';
        }
        
        if ($status === 'overdue' || ($priority === 'urgent' && $status !== 'completed')) {
            return '#DC2626';
        }
        
        if ($priority === 'high') {
            return '#F59E0B';
        }
        
        return '#3B82F6';
    }

    /**
     * الحصول على لون الإجازة حسب الحالة
     */
    private function getLeaveColor($status)
    {
        return match($status) {
            'approved' => '#10B981',
            'pending' => '#F59E0B',
            'rejected' => '#DC2626',
            default => '#6B7280',
        };
    }
}
