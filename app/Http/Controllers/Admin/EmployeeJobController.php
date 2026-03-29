<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeJob;
use Illuminate\Http\Request;

class EmployeeJobController extends Controller
{
    /**
     * عرض قائمة الوظائف
     */
    public function index(Request $request)
    {
        $query = EmployeeJob::withCount('employees');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $jobs = $query->latest()->paginate(20);

        $stats = [
            'total' => EmployeeJob::count(),
            'active' => EmployeeJob::active()->count(),
            'inactive' => EmployeeJob::where('is_active', false)->count(),
            'total_employees' => \App\Models\User::employees()->count(),
        ];

        return view('admin.employee-jobs.index', compact('jobs', 'stats'));
    }

    /**
     * عرض صفحة إضافة وظيفة
     */
    public function create()
    {
        $allPresets = EmployeeJob::presetJobTemplates();
        $takenCodes = EmployeeJob::query()
            ->pluck('code')
            ->filter()
            ->map(fn (string $c) => strtolower($c))
            ->all();

        $jobPresets = [];
        $presetsSkippedBecauseCodeTaken = [];
        foreach ($allPresets as $key => $preset) {
            $codeLower = strtolower((string) ($preset['code'] ?? ''));
            if ($codeLower !== '' && in_array($codeLower, $takenCodes, true)) {
                $presetsSkippedBecauseCodeTaken[$key] = $preset;

                continue;
            }
            $jobPresets[$key] = $preset;
        }

        return view('admin.employee-jobs.create', compact('jobPresets', 'presetsSkippedBecauseCodeTaken'));
    }

    /**
     * حفظ وظيفة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:employee_jobs,code',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
        ], [
            'code.unique' => 'هذا الرمز مستخدم بالفعل لوظيفة أخرى. إن كنت تريد «مبيعات» أو قالباً جاهزاً، راجع قائمة الوظائف أو عدّل الوظيفة الموجودة بدلاً من إنشاء نسخة جديدة.',
        ]);

        if (empty($validated['code'])) {
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['name']), 0, 3));
            $validated['code'] = $prefix . '-' . (EmployeeJob::count() + 1);
        }

        $validated['is_active'] = $request->has('is_active');

        $job = EmployeeJob::create($validated);

        return redirect()->route('admin.employee-jobs.index')
                        ->with('success', 'تم إضافة الوظيفة بنجاح');
    }

    /**
     * عرض تفاصيل وظيفة
     */
    public function show(EmployeeJob $employeeJob)
    {
        $employeeJob->loadCount('employees');
        $employees = $employeeJob->employees()->latest('hire_date')->paginate(10);
        return view('admin.employee-jobs.show', compact('employeeJob', 'employees'));
    }

    /**
     * عرض صفحة تعديل وظيفة
     */
    public function edit(EmployeeJob $employeeJob)
    {
        return view('admin.employee-jobs.edit', compact('employeeJob'));
    }

    /**
     * تحديث وظيفة
     */
    public function update(Request $request, EmployeeJob $employeeJob)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:employee_jobs,code,' . $employeeJob->id,
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
        ], [
            'code.unique' => 'هذا الرمز مستخدم بالفعل لوظيفة أخرى.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $employeeJob->update($validated);

        return redirect()->route('admin.employee-jobs.index')
                        ->with('success', 'تم تحديث الوظيفة بنجاح');
    }

    /**
     * حذف وظيفة
     */
    public function destroy(EmployeeJob $employeeJob)
    {
        // التحقق من وجود موظفين مرتبطين بهذه الوظيفة
        if ($employeeJob->employees()->count() > 0) {
            return redirect()->route('admin.employee-jobs.index')
                            ->with('error', 'لا يمكن حذف الوظيفة لأنها مرتبطة بموظفين');
        }

        $employeeJob->delete();

        return redirect()->route('admin.employee-jobs.index')
                        ->with('success', 'تم حذف الوظيفة بنجاح');
    }
}
