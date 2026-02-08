<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\EmployeeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeCalendarController extends Controller
{
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
            'upcoming' => $events->where('start_date', '>=', now())->count(),
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
                    'priority' => $event->priority ?? 'medium',
                    'location' => $event->location ?? null,
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

        // ترتيب الأحداث حسب التاريخ
        return $events->sortBy('start_date')->values();
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
