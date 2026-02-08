<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeTaskController extends Controller
{
    /**
     * عرض قائمة المهام
     */
    public function index(Request $request)
    {
        $query = EmployeeTask::with(['employee.employeeJob', 'assigner']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب الموظف
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب الأولوية
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->latest()->paginate(20);

        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => EmployeeTask::count(),
            'pending' => EmployeeTask::pending()->count(),
            'in_progress' => EmployeeTask::inProgress()->count(),
            'completed' => EmployeeTask::completed()->count(),
            'overdue' => EmployeeTask::overdue()->count(),
        ];

        return view('admin.employee-tasks.index', compact('tasks', 'employees', 'stats'));
    }

    /**
     * عرض صفحة إضافة مهمة
     */
    public function create()
    {
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();
        return view('admin.employee-tasks.create', compact('employees'));
    }

    /**
     * حفظ مهمة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['status'] = 'pending';

        $task = EmployeeTask::create($validated);

        return redirect()->route('admin.employee-tasks.show', $task)
                        ->with('success', 'تم إضافة المهمة بنجاح');
    }

    /**
     * عرض تفاصيل مهمة
     */
    public function show(EmployeeTask $employeeTask)
    {
        $employeeTask->load(['employee.employeeJob', 'assigner', 'deliverables.reviewer']);
        return view('admin.employee-tasks.show', compact('employeeTask'));
    }

    /**
     * عرض صفحة تعديل مهمة
     */
    public function edit(EmployeeTask $employeeTask)
    {
        $employees = User::employees()->where('is_active', true)->orderBy('name')->get();
        return view('admin.employee-tasks.edit', compact('employeeTask', 'employees'));
    }

    /**
     * تحديث مهمة
     */
    public function update(Request $request, EmployeeTask $employeeTask)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
            'deadline' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // تحديث التواريخ بناءً على الحالة
        if ($validated['status'] === 'in_progress' && !$employeeTask->started_at) {
            $validated['started_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        $employeeTask->update($validated);

        return redirect()->route('admin.employee-tasks.show', $employeeTask)
                        ->with('success', 'تم تحديث المهمة بنجاح');
    }

    /**
     * حذف مهمة
     */
    public function destroy(EmployeeTask $employeeTask)
    {
        $employeeTask->delete();
        return redirect()->route('admin.employee-tasks.index')
                        ->with('success', 'تم حذف المهمة بنجاح');
    }
}
