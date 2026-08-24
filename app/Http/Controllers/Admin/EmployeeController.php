<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeJob;
use App\Services\EmployeeAttendanceService;
use App\Services\UserDeletionService;
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
        EmployeeJob::ensureMediaJobs();
        EmployeeJob::ensurePresetJob('business_developer');

        $jobs = EmployeeJob::active()->orderBy('name')->get();
        $workSchedules = \App\Models\WorkSchedule::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.employees.create', compact('jobs', 'workSchedules'));
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
            'weekly_off_day' => 'nullable|integer|min:0|max:6',
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'work_mode' => 'required|in:online,offline,hybrid',
            'offline_attendance_type' => 'nullable|required_if:work_mode,offline|in:full_time,selected_days',
            'onsite_days' => 'nullable|array',
            'onsite_days.*' => 'integer|min:0|max:6',
            'use_custom_week' => 'nullable|boolean',
            'work_week_plan' => 'nullable|array',
            'work_week_plan.*.active' => 'nullable',
            'work_week_plan.*.attendance_mode' => 'nullable|in:online,offline',
            'work_week_plan.*.start_time' => 'nullable|date_format:H:i',
            'work_week_plan.*.end_time' => 'nullable|date_format:H:i',
            'work_week_plan.*.required_hours' => 'nullable|numeric|min:0|max:24',
            'salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if (! isset($validated['weekly_off_day']) || $validated['weekly_off_day'] === '') {
            $validated['weekly_off_day'] = null;
        } else {
            $validated['weekly_off_day'] = (int) $validated['weekly_off_day'];
        }

        if (empty($validated['work_schedule_id'])) {
            $validated['work_schedule_id'] = null;
        }

        $validated = $this->normalizeWorkModeFields($validated, $request);

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
        EmployeeJob::ensureMediaJobs();
        EmployeeJob::ensurePresetJob('business_developer');

        $jobs = EmployeeJob::active()->orderBy('name')->get();
        $workSchedules = \App\Models\WorkSchedule::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.employees.edit', compact('employee', 'jobs', 'workSchedules'));
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
            'weekly_off_day' => 'nullable|integer|min:0|max:6',
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'work_mode' => 'required|in:online,offline,hybrid',
            'offline_attendance_type' => 'nullable|required_if:work_mode,offline|in:full_time,selected_days',
            'onsite_days' => 'nullable|array',
            'onsite_days.*' => 'integer|min:0|max:6',
            'use_custom_week' => 'nullable|boolean',
            'work_week_plan' => 'nullable|array',
            'work_week_plan.*.active' => 'nullable',
            'work_week_plan.*.attendance_mode' => 'nullable|in:online,offline',
            'work_week_plan.*.start_time' => 'nullable|date_format:H:i',
            'work_week_plan.*.end_time' => 'nullable|date_format:H:i',
            'work_week_plan.*.required_hours' => 'nullable|numeric|min:0|max:24',
            'termination_date' => 'nullable|date|after:hire_date',
            'salary' => 'nullable|numeric|min:0',
            'employee_notes' => 'nullable|string',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:100',
            'bank_account_holder_name' => 'nullable|string|max:255',
            'bank_iban' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (! array_key_exists('weekly_off_day', $validated) || $validated['weekly_off_day'] === '' || $validated['weekly_off_day'] === null) {
            $validated['weekly_off_day'] = null;
        } else {
            $validated['weekly_off_day'] = (int) $validated['weekly_off_day'];
        }

        if (empty($validated['work_schedule_id'])) {
            $validated['work_schedule_id'] = null;
        }

        $validated = $this->normalizeWorkModeFields($validated, $request);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $employee->update($validated);
        $employee->refresh();

        // أعد حساب يوم الراحة/الحضور لليوم الحالي حسب ملف الموظف
        app(EmployeeAttendanceService::class)->resyncTodayAfterEmployeeUpdate($employee);

        return redirect()->route('admin.employees.show', $employee)
                        ->with('success', 'تم تحديث بيانات الموظف بنجاح — نوع العمل وأيام النزول تُطبَّق فوراً على قفل النظام والحضور.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizeWorkModeFields(array $validated, Request $request): array
    {
        $mode = $validated['work_mode'] ?? User::WORK_MODE_ONLINE;
        $validated['work_mode'] = $mode;
        unset($validated['use_custom_week']);

        $useCustomWeek = $request->boolean('use_custom_week') || $mode === User::WORK_MODE_HYBRID;
        $planInput = $request->input('work_week_plan', []);

        if ($useCustomWeek && is_array($planInput) && $planInput !== []) {
            $plan = $this->normalizeWorkWeekPlanInput($planInput, $mode);
            $activeDays = collect($plan)->filter(fn ($row) => $row['active'])->keys()->map(fn ($d) => (int) $d)->values()->all();
            if ($activeDays === []) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'work_week_plan' => 'فعّل يوماً واحداً على الأقل في الجدول الأسبوعي.',
                ]);
            }

            $validated['work_week_plan'] = $plan;
            $validated['onsite_days'] = collect($plan)
                ->filter(fn ($row) => $row['active'] && ($row['attendance_mode'] ?? '') === User::DAY_MODE_OFFLINE)
                ->keys()
                ->map(fn ($d) => (int) $d)
                ->values()
                ->all();

            if ($mode === User::WORK_MODE_OFFLINE) {
                $validated['offline_attendance_type'] = User::OFFLINE_SELECTED_DAYS;
            } elseif ($mode === User::WORK_MODE_HYBRID) {
                $validated['offline_attendance_type'] = null;
                if ($validated['onsite_days'] === []) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'work_week_plan' => 'في وضع Hybrid اختر يوماً أوفلاين واحداً على الأقل (أو غيّر الوضع لأونلاين كامل).',
                    ]);
                }
            } else {
                // online + custom week times only
                $validated['offline_attendance_type'] = null;
                $validated['onsite_days'] = null;
            }

            return $validated;
        }

        $validated['work_week_plan'] = null;

        if ($mode === User::WORK_MODE_ONLINE) {
            $validated['offline_attendance_type'] = null;
            $validated['onsite_days'] = null;

            return $validated;
        }

        if ($mode === User::WORK_MODE_HYBRID) {
            $days = array_values(array_unique(array_map('intval', $request->input('onsite_days', []))));
            if ($days === []) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'work_week_plan' => 'Hybrid يحتاج جدولاً أسبوعياً أو أيام أوفلاين محددة.',
                ]);
            }
            $validated['offline_attendance_type'] = null;
            $validated['onsite_days'] = $days;

            // ابنِ خطة بسيطة من الأيام المحددة + يوم الراحة
            $plan = [];
            foreach (range(0, 6) as $day) {
                $isOff = $validated['weekly_off_day'] !== null
                    ? (int) $validated['weekly_off_day'] === $day
                    : in_array($day, [5, 6], true); // default weekend if null — approximate; attendance still uses weekly_off
                $isOnsite = in_array($day, $days, true);
                $plan[$day] = [
                    'active' => ! $isOff,
                    'attendance_mode' => $isOnsite ? User::DAY_MODE_OFFLINE : User::DAY_MODE_ONLINE,
                    'start_time' => null,
                    'end_time' => null,
                    'required_hours' => null,
                ];
            }
            // Prefer explicit: active if not weekly off via employee weekly_off when set
            if ($validated['weekly_off_day'] !== null) {
                foreach (range(0, 6) as $day) {
                    $plan[$day]['active'] = (int) $validated['weekly_off_day'] !== $day;
                    if (! $plan[$day]['active']) {
                        $plan[$day]['attendance_mode'] = User::DAY_MODE_ONLINE;
                    } else {
                        $plan[$day]['attendance_mode'] = in_array($day, $days, true)
                            ? User::DAY_MODE_OFFLINE
                            : User::DAY_MODE_ONLINE;
                    }
                }
            }
            $validated['work_week_plan'] = $plan;

            return $validated;
        }

        // offline
        $type = $validated['offline_attendance_type'] ?? User::OFFLINE_FULL_TIME;
        $validated['offline_attendance_type'] = $type;

        if ($type === User::OFFLINE_SELECTED_DAYS) {
            $days = array_values(array_unique(array_map('intval', $request->input('onsite_days', []))));
            if ($days === []) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'onsite_days' => 'اختر يوماً واحداً على الأقل لنزول الموظف للمكتب.',
                ]);
            }
            $validated['onsite_days'] = $days;
        } else {
            $validated['onsite_days'] = null;
        }

        return $validated;
    }

    /**
     * @param  array<string|int, mixed>  $planInput
     * @return array<int, array{active:bool,attendance_mode:string,start_time:?string,end_time:?string,required_hours:?float}>
     */
    private function normalizeWorkWeekPlanInput(array $planInput, string $mode): array
    {
        $plan = [];
        foreach (range(0, 6) as $day) {
            $row = $planInput[$day] ?? $planInput[(string) $day] ?? [];
            if (! is_array($row)) {
                $row = [];
            }
            $activeRaw = $row['active'] ?? '0';
            $active = $activeRaw === true || $activeRaw === 1 || $activeRaw === '1';
            $attMode = (string) ($row['attendance_mode'] ?? User::DAY_MODE_ONLINE);
            if ($mode === User::WORK_MODE_OFFLINE && $active) {
                $attMode = User::DAY_MODE_OFFLINE;
            }
            if ($mode === User::WORK_MODE_ONLINE && $active) {
                $attMode = User::DAY_MODE_ONLINE;
            }
            if (! in_array($attMode, [User::DAY_MODE_ONLINE, User::DAY_MODE_OFFLINE], true)) {
                $attMode = User::DAY_MODE_ONLINE;
            }

            $start = $row['start_time'] ?? null;
            $end = $row['end_time'] ?? null;
            $hours = $row['required_hours'] ?? null;

            $plan[$day] = [
                'active' => $active,
                'attendance_mode' => $attMode,
                'start_time' => $start !== null && $start !== '' ? substr((string) $start, 0, 5) : null,
                'end_time' => $end !== null && $end !== '' ? substr((string) $end, 0, 5) : null,
                'required_hours' => $hours === null || $hours === '' ? null : (float) $hours,
            ];
        }

        return $plan;
    }

    /**
     * حذف موظف
     */
    public function destroy(User $employee, UserDeletionService $deletionService)
    {
        if (! $employee->is_employee) {
            abort(404);
        }

        try {
            $result = $deletionService->deleteEmployee($employee);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $message = 'تم حذف الموظف بنجاح';
        if (($result['unassigned_leads'] ?? 0) > 0) {
            $message .= ' — تم إلغاء تعيين ' . $result['unassigned_leads'] . ' عميل محتمل (يمكن إعادة تعيينهم من قسم المبيعات).';
        }

        return redirect()->route('admin.employees.index')->with('success', $message);
    }
}
