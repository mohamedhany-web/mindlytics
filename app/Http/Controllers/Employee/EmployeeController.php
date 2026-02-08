<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
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

        return view('employee.dashboard', compact('user', 'tasks', 'stats'));
    }
}
