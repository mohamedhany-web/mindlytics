<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeReportController extends Controller
{
    /**
     * عرض التقارير والإحصائيات
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // تحديد الفترة الزمنية
        $period = $request->get('period', 'month'); // week, month, quarter, year
        $startDate = $this->getStartDate($period);
        $endDate = now();

        // إحصائيات المهام
        $taskStats = $this->getTaskStats($user, $startDate, $endDate);
        
        // إحصائيات الإجازات
        $leaveStats = $this->getLeaveStats($user, $startDate, $endDate);
        
        // أداء المهام حسب الشهر
        $monthlyPerformance = $this->getMonthlyPerformance($user);
        
        // المهام حسب الأولوية
        $tasksByPriority = $this->getTasksByPriority($user);
        
        // المهام حسب الحالة
        $tasksByStatus = $this->getTasksByStatus($user);

        return view('employee.reports.index', compact(
            'taskStats',
            'leaveStats',
            'monthlyPerformance',
            'tasksByPriority',
            'tasksByStatus',
            'period'
        ));
    }

    /**
     * الحصول على تاريخ البداية حسب الفترة
     */
    private function getStartDate($period)
    {
        return match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subQuarter(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }

    /**
     * إحصائيات المهام
     */
    private function getTaskStats($user, $startDate, $endDate)
    {
        $tasks = $user->employeeTasks()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'overdue' => $tasks->where('deadline', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'completion_rate' => $tasks->count() > 0 
                ? round(($tasks->where('status', 'completed')->count() / $tasks->count()) * 100, 1) 
                : 0,
            'average_completion_time' => $this->getAverageCompletionTime($tasks),
        ];
    }

    /**
     * إحصائيات الإجازات
     */
    private function getLeaveStats($user, $startDate, $endDate)
    {
        $leaves = \App\Models\LeaveRequest::where('employee_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total' => $leaves->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
            'total_days' => $leaves->where('status', 'approved')
                ->sum(function($leave) {
                    return Carbon::parse($leave->start_date)
                        ->diffInDays(Carbon::parse($leave->end_date)) + 1;
                }),
        ];
    }

    /**
     * الأداء الشهري
     */
    private function getMonthlyPerformance($user)
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthTasks = $user->employeeTasks()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->get();
            
            $completed = $monthTasks->where('status', 'completed')->count();
            $total = $monthTasks->count();
            
            $months[] = [
                'month' => $monthStart->format('Y-m'),
                'month_name' => $monthStart->format('M Y'),
                'total' => $total,
                'completed' => $completed,
                'rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            ];
        }
        
        return $months;
    }

    /**
     * المهام حسب الأولوية
     */
    private function getTasksByPriority($user)
    {
        $tasks = $user->employeeTasks()->get();
        
        return [
            'urgent' => $tasks->where('priority', 'urgent')->count(),
            'high' => $tasks->where('priority', 'high')->count(),
            'medium' => $tasks->where('priority', 'medium')->count(),
            'low' => $tasks->where('priority', 'low')->count(),
        ];
    }

    /**
     * المهام حسب الحالة
     */
    private function getTasksByStatus($user)
    {
        $tasks = $user->employeeTasks()->get();
        
        return [
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'cancelled' => $tasks->where('status', 'cancelled')->count(),
        ];
    }

    /**
     * متوسط وقت الإنجاز
     */
    private function getAverageCompletionTime($tasks)
    {
        $completedTasks = $tasks->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->filter(function($task) {
                return $task->created_at && $task->completed_at;
            });

        if ($completedTasks->isEmpty()) {
            return 0;
        }

        $totalDays = $completedTasks->sum(function($task) {
            return $task->created_at->diffInDays($task->completed_at);
        });

        return round($totalDays / $completedTasks->count(), 1);
    }
}
