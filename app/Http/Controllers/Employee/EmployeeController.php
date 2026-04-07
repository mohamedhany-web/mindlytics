<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\SalesLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * لوحة تحكم الموظف
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        $tasks = $user->employeeTasks()
            ->with(['assigner', 'deliverables'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_tasks' => $user->employeeTasks()->count(),
            'pending_tasks' => $user->employeeTasks()->where('status', 'pending')->count(),
            'in_progress_tasks' => $user->employeeTasks()->where('status', 'in_progress')->count(),
            'completed_tasks' => $user->employeeTasks()->where('status', 'completed')->count(),
            'overdue_tasks' => $user->employeeTasks()
                ->where('deadline', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        $salesSnapshot = null;
        if ($user->isSalesEmployee()) {
            $sq = SalesLead::query()->forAssignee($user->id);
            $open = fn () => (clone $sq)->openPipeline();
            $salesSnapshot = [
                'total' => (clone $sq)->count(),
                'active' => $open()->count(),
                'followups_today' => $open()
                    ->whereNotNull('next_follow_up_at')
                    ->whereDate('next_follow_up_at', today())
                    ->count(),
                'followups_overdue' => $open()
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<', now())
                    ->count(),
            ];
        }

        return view('employee.dashboard', compact('user', 'tasks', 'stats', 'salesSnapshot'));
    }

    /**
     * صفحة Documentation للموظف (شرح النظام + المتطلبات + roadmap).
     */
    public function documentation()
    {
        $user = Auth::user();

        if (! $user || ! $user->isEmployee()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        return view('employee.documentation.index');
    }
}
