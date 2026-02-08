<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * عرض قائمة الموظفين
     */
    public function index(Request $request)
    {
        $query = User::employees()->with(['employeeJob']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الوظيفة
        if ($request->filled('job_id')) {
            $query->where('employee_job_id', $request->job_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->whereNull('termination_date');
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'terminated') {
                $query->whereNotNull('termination_date');
            }
        }

        $employees = $query->latest('hire_date')->paginate(20);

        $jobs = EmployeeJob::active()->orderBy('name')->get();

        $stats = [
            'total' => User::employees()->count(),
            'active' => User::employees()->where('is_active', true)->whereNull('termination_date')->count(),
            'inactive' => User::employees()->where('is_active', false)->count(),
            'terminated' => User::employees()->whereNotNull('termination_date')->count(),
        ];

        return view('admin.employees.index', compact('employees', 'jobs', 'stats'));
    }

    /**
     * عرض صفحة إضافة موظف
     */
    public function create()
    {
        $jobs = EmployeeJob::active()->orderBy('name')->get();
        return view('admin.employees.create', compact('jobs'));
    }

    /**
     * حفظ موظف جديد
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
            'employee_job_id' => 'required|exists:employee_jobs,id',
            'employee_code' => 'nullable|string|unique:users,employee_code',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // إنشاء رمز الموظف إذا لم يتم توفيره
        if (empty($validated['employee_code'])) {
            $validated['employee_code'] = 'EMP-' . strtoupper(Str::random(6));
        }

        $validated['password'] = Hash::make($validated['password']);
        // استخدام 'student' كقيمة role لأن enum لا يدعم 'employee'
        // والاعتماد على is_employee للتمييز بين الموظفين والطلاب
        $validated['role'] = 'student';
        $validated['is_employee'] = true;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $employee = User::create($validated);

        // إنشاء اتفاقية تلقائياً إذا تم تحديد راتب
        if (!empty($validated['salary']) && $validated['salary'] > 0) {
            \App\Models\EmployeeAgreement::create([
                'employee_id' => $employee->id,
                'agreement_number' => \App\Models\EmployeeAgreement::generateAgreementNumber(),
                'title' => 'اتفاقية عمل - ' . $employee->name,
                'description' => 'اتفاقية عمل تلقائية تم إنشاؤها عند تسجيل الموظف',
                'salary' => $validated['salary'],
                'start_date' => $validated['hire_date'],
                'status' => 'active',
                'contract_terms' => 'شروط العقد الأساسية',
                'agreement_terms' => 'بنود الاتفاقية الأساسية',
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.employees.show', $employee)
                        ->with('success', 'تم إضافة الموظف بنجاح' . (!empty($validated['salary']) ? ' وتم إنشاء اتفاقية العمل' : ''));
    }

    /**
     * عرض تفاصيل موظف
     */
    public function show(User $employee)
    {
        $employee->load(['employeeJob', 'employeeTasks.assigner', 'employeeTasks.deliverables']);
        
        $stats = [
            'total_tasks' => $employee->employeeTasks()->count(),
            'pending_tasks' => $employee->employeeTasks()->where('status', 'pending')->count(),
            'in_progress_tasks' => $employee->employeeTasks()->where('status', 'in_progress')->count(),
            'completed_tasks' => $employee->employeeTasks()->where('status', 'completed')->count(),
            'overdue_tasks' => $employee->employeeTasks()
                ->where('deadline', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];

        return view('admin.employees.show', compact('employee', 'stats'));
    }

    /**
     * عرض صفحة تعديل موظف
     */
    public function edit(User $employee)
    {
        $jobs = EmployeeJob::active()->orderBy('name')->get();
        return view('admin.employees.edit', compact('employee', 'jobs'));
    }

    /**
     * تحديث موظف
     */
    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'phone' => 'required|string|unique:users,phone,' . $employee->id,
            'password' => 'nullable|string|min:8',
            'employee_job_id' => 'required|exists:employee_jobs,id',
            'employee_code' => 'nullable|string|unique:users,employee_code,' . $employee->id,
            'hire_date' => 'required|date',
            'termination_date' => 'nullable|date|after:hire_date',
            'salary' => 'nullable|numeric|min:0',
            'employee_notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $employee->update($validated);

        return redirect()->route('admin.employees.show', $employee)
                        ->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * حذف موظف
     */
    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')
                        ->with('success', 'تم حذف الموظف بنجاح');
    }
}
