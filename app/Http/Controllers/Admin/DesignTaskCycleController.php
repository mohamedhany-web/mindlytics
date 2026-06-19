<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignTaskCycle;
use App\Models\EmployeeTask;
use App\Models\User;
use App\Services\EmployeeMonthlyPerformanceExcelExport;
use App\Services\EmployeeMonthlyPerformanceInsightsService;
use App\Services\EmployeeMonthlyPerformanceReportService;
use App\Services\EmployeeDailyReportService;
use App\Services\EmployeeTaskAssignmentNotifier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DesignTaskCycleController extends Controller
{
    public function __construct(
        protected EmployeeTaskAssignmentNotifier $taskAssignmentNotifier
    ) {}
    public function index(Request $request)
    {
        $query = DesignTaskCycle::query()
            ->with(['moderator', 'designer', 'designerTask', 'moderatorDeliveryTask']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('moderator_id')) {
            $query->where('moderator_id', $request->moderator_id);
        }
        if ($request->filled('designer_employee_id')) {
            $query->where('designer_employee_id', $request->designer_employee_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%'.$s.'%')
                    ->orWhere('id', $s)
                    ->orWhereHas('moderator', fn ($q) => $q->where('name', 'like', '%'.$s.'%'))
                    ->orWhereHas('designer', fn ($q) => $q->where('name', 'like', '%'.$s.'%'));
            });
        }

        $cycles = $query->latest()->paginate(25)->withQueryString();

        $moderators = User::moderatorEmployees()->where('is_active', true)->orderBy('name')->get();
        $designers = User::designerEmployees()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => DesignTaskCycle::count(),
            'pending' => DesignTaskCycle::whereIn('status', [
                DesignTaskCycle::STATUS_PENDING_DESIGN,
                DesignTaskCycle::STATUS_DESIGN_IN_PROGRESS,
            ])->count(),
            'awaiting_moderator' => DesignTaskCycle::where('status', DesignTaskCycle::STATUS_DESIGN_SUBMITTED)->count(),
            'in_delivery' => DesignTaskCycle::where('status', DesignTaskCycle::STATUS_MODERATOR_DELIVERY_PENDING)->count(),
            'completed' => DesignTaskCycle::where('status', DesignTaskCycle::STATUS_COMPLETED)->count(),
        ];

        return view('admin.design-task-cycles.index', compact('cycles', 'moderators', 'designers', 'stats'));
    }

    public function create()
    {
        $moderators = User::moderatorEmployees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();
        $designers = User::designerEmployees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();

        return view('admin.design-task-cycles.create', compact('moderators', 'designers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'moderator_id' => ['required', 'exists:users,id'],
            'designer_employee_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'specifications' => ['required', 'string', 'max:20000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'deadline_at' => ['required', 'date', 'after:now'],
        ], [
            'moderator_id.required' => 'اختر المشرف',
            'designer_employee_id.required' => 'اختر المصمم',
            'specifications.required' => 'أدخل تفاصيل التصميم المطلوب',
            'deadline_at.after' => 'حد التسليم يجب أن يكون في المستقبل',
        ]);

        $moderator = User::query()->employees()->where('id', $validated['moderator_id'])->where('is_active', true)->first();
        if (! $moderator || ! $moderator->isModeratorEmployee()) {
            return back()->withErrors(['moderator_id' => 'المستخدم المختار ليس مشرفاً نشطاً.'])->withInput();
        }

        $designer = User::query()->employees()->where('id', $validated['designer_employee_id'])->where('is_active', true)->first();
        if (! $designer || ! $designer->isDesignerEmployee()) {
            return back()->withErrors(['designer_employee_id' => 'المستخدم المختار ليس مصمماً نشطاً.'])->withInput();
        }

        if ((int) $designer->id === (int) $moderator->id) {
            return back()->withErrors(['designer_employee_id' => 'لا يمكن إسناد الطلب لنفس الشخص كمشرف ومصمم.'])->withInput();
        }

        $cycle = $this->createCycleWithDesignerTask($validated, $moderator, $designer, Auth::id());

        if (! $cycle) {
            return back()->with('error', 'تعذر إنشاء الدورة. حاول مرة أخرى.')->withInput();
        }

        return redirect()->route('admin.design-task-cycles.show', $cycle)
            ->with('success', 'تم إنشاء دورة التصميم وإسناد المهمة للمصمم.');
    }

    public function edit(DesignTaskCycle $designTaskCycle)
    {
        $designTaskCycle->load(['moderator', 'designer']);
        $moderators = User::moderatorEmployees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();
        $designers = User::designerEmployees()->where('is_active', true)->with('employeeJob')->orderBy('name')->get();

        return view('admin.design-task-cycles.edit', compact('designTaskCycle', 'moderators', 'designers'));
    }

    public function update(Request $request, DesignTaskCycle $designTaskCycle)
    {
        $locked = in_array($designTaskCycle->status, [
            DesignTaskCycle::STATUS_COMPLETED,
            DesignTaskCycle::STATUS_CANCELLED,
        ], true);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'specifications' => ['required', 'string', 'max:20000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'deadline_at' => ['required', 'date'],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
            'status' => ['required', 'in:'.implode(',', [
                DesignTaskCycle::STATUS_PENDING_DESIGN,
                DesignTaskCycle::STATUS_DESIGN_IN_PROGRESS,
                DesignTaskCycle::STATUS_DESIGN_SUBMITTED,
                DesignTaskCycle::STATUS_MODERATOR_DELIVERY_PENDING,
                DesignTaskCycle::STATUS_COMPLETED,
                DesignTaskCycle::STATUS_CANCELLED,
            ])],
        ];

        if (! $locked) {
            $rules['moderator_id'] = ['required', 'exists:users,id'];
            $rules['designer_employee_id'] = ['required', 'exists:users,id'];
        }

        $validated = $request->validate($rules);

        if (! $locked) {
            $moderator = User::query()->employees()->where('id', $validated['moderator_id'])->where('is_active', true)->first();
            if (! $moderator || ! $moderator->isModeratorEmployee()) {
                return back()->withErrors(['moderator_id' => 'المستخدم المختار ليس مشرفاً نشطاً.'])->withInput();
            }

            $designer = User::query()->employees()->where('id', $validated['designer_employee_id'])->where('is_active', true)->first();
            if (! $designer || ! $designer->isDesignerEmployee()) {
                return back()->withErrors(['designer_employee_id' => 'المستخدم المختار ليس مصمماً نشطاً.'])->withInput();
            }

            if ((int) $designer->id === (int) $moderator->id) {
                return back()->withErrors(['designer_employee_id' => 'لا يمكن إسناد الطلب لنفس الشخص كمشرف ومصمم.'])->withInput();
            }

            $validated['moderator_id'] = $moderator->id;
            $validated['designer_employee_id'] = $designer->id;
        } else {
            unset($validated['moderator_id'], $validated['designer_employee_id']);
        }

        $deadlineAt = Carbon::parse($validated['deadline_at']);
        $fullDescription = trim(($validated['description'] ?? '')."\n\n".'— مواصفات التصميم —'."\n".$validated['specifications']);

        DB::beginTransaction();
        try {
            $previousDesignerId = (int) $designTaskCycle->designer_employee_id;

            $designTaskCycle->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'specifications' => $validated['specifications'],
                'priority' => $validated['priority'],
                'deadline_at' => $deadlineAt,
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'] ?? null,
                ...(! $locked ? [
                    'moderator_id' => $validated['moderator_id'],
                    'designer_employee_id' => $validated['designer_employee_id'],
                ] : []),
            ]);

            if ($designTaskCycle->designerTask) {
                $designTaskCycle->designerTask->update([
                    'title' => $validated['title'],
                    'description' => $fullDescription,
                    'priority' => $validated['priority'],
                    'deadline' => $deadlineAt->toDateString(),
                    ...(! $locked ? [
                        'employee_id' => $validated['designer_employee_id'],
                        'assigned_by' => $validated['moderator_id'],
                    ] : []),
                ]);

                if (! $locked && (int) $validated['designer_employee_id'] !== $previousDesignerId) {
                    $this->taskAssignmentNotifier->notifyAssigned($designTaskCycle->designerTask->fresh());
                }
            }

            if ($designTaskCycle->moderatorDeliveryTask && ! $locked) {
                $designTaskCycle->moderatorDeliveryTask->update([
                    'assigned_by' => $validated['moderator_id'],
                    'employee_id' => $validated['moderator_id'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'تعذر تحديث الدورة.')->withInput();
        }

        return redirect()->route('admin.design-task-cycles.show', $designTaskCycle)
            ->with('success', 'تم تحديث دورة التصميم.');
    }

    public function destroy(DesignTaskCycle $designTaskCycle)
    {
        DB::beginTransaction();
        try {
            $taskIds = EmployeeTask::query()
                ->where('design_cycle_id', $designTaskCycle->id)
                ->pluck('id');

            if ($designTaskCycle->designer_task_id) {
                $taskIds->push($designTaskCycle->designer_task_id);
            }
            if ($designTaskCycle->moderator_delivery_task_id) {
                $taskIds->push($designTaskCycle->moderator_delivery_task_id);
            }

            $taskIds = $taskIds->unique()->filter();

            EmployeeTask::query()->whereIn('id', $taskIds)->each(function (EmployeeTask $task) {
                $task->deliverables()->delete();
                $task->delete();
            });

            $designTaskCycle->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'تعذر حذف الدورة.');
        }

        return redirect()->route('admin.design-task-cycles.index')
            ->with('success', 'تم حذف دورة التصميم والمهام المرتبطة.');
    }

    public function show(DesignTaskCycle $designTaskCycle)
    {
        $designTaskCycle->load([
            'moderator.employeeJob',
            'designer.employeeJob',
            'designerTask.deliverables',
            'moderatorDeliveryTask.deliverables',
        ]);

        return view('admin.design-task-cycles.show', compact('designTaskCycle'));
    }

    public function updateNotes(Request $request, DesignTaskCycle $designTaskCycle)
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $designTaskCycle->update(['admin_notes' => $validated['admin_notes'] ?? null]);

        return back()->with('success', 'تم حفظ ملاحظات الإدارة.');
    }

    public function cancel(Request $request, DesignTaskCycle $designTaskCycle)
    {
        if (in_array($designTaskCycle->status, [DesignTaskCycle::STATUS_COMPLETED, DesignTaskCycle::STATUS_CANCELLED], true)) {
            return back()->withErrors(['error' => 'الدورة منتهية أو ملغاة.']);
        }

        $designTaskCycle->update(['status' => DesignTaskCycle::STATUS_CANCELLED]);

        return back()->with('success', 'تم إلغاء الدورة من قبل الإدارة.');
    }

    /**
     * تقرير أداء شهري تفصيلي للموظفين (مهام + دورات التصميم).
     */
    public function performanceReport(
        Request $request,
        EmployeeMonthlyPerformanceReportService $reportService,
        EmployeeMonthlyPerformanceInsightsService $insightsService,
        EmployeeDailyReportService $dailyReportService
    ) {
        [$start, $end, $year, $month] = $this->performanceReportPeriod($request);

        $report = $reportService->analyze($start, $end);

        $prevStart = $start->copy()->subMonth()->startOfMonth();
        $prevEnd = $prevStart->copy()->endOfMonth();
        $prevReport = $reportService->analyze($prevStart, $prevEnd);

        $activeEmployees = User::employees()->where('is_active', true)->orderBy('name')->get();
        $dailyCompliance = $dailyReportService->submissionRateForMonth($activeEmployees, $start);

        $dashboard = $insightsService->build($report, $prevReport, $dailyCompliance);

        return view('admin.design-task-cycles.performance-report', [
            'rows' => $report['rows'],
            'summary' => $report['summary'],
            'prevSummary' => $prevReport['summary'],
            'start' => $report['start'],
            'end' => $report['end'],
            'year' => $year,
            'month' => $month,
            'dashboard' => $dashboard,
            'dailyCompliance' => $dailyCompliance,
        ]);
    }

    /**
     * تصدير تقرير الأداء الشهري إلى Excel (أوراق متعددة، تنسيق جاهز للطباعة).
     */
    public function performanceReportExcel(Request $request, EmployeeMonthlyPerformanceReportService $reportService, EmployeeMonthlyPerformanceExcelExport $excelExport)
    {
        [$start, $end] = $this->performanceReportPeriod($request);

        $report = $reportService->analyze($start, $end);

        return $excelExport->streamResponse($report);
    }

    /**
     * @return array{0: \Carbon\Carbon, 1: \Carbon\Carbon, 2: int, 3: int}
     */
    private function performanceReportPeriod(Request $request): array
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $year = max(2000, min(2100, $year));
        $month = max(1, min(12, $month));

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        return [$start, $end, $year, $month];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function createCycleWithDesignerTask(array $validated, User $moderator, User $designer, int $assignedById): ?DesignTaskCycle
    {
        $deadlineAt = Carbon::parse($validated['deadline_at']);
        $deadlineDate = $deadlineAt->toDateString();
        $fullDescription = trim(($validated['description'] ?? '')."\n\n".'— مواصفات التصميم —'."\n".$validated['specifications']);

        DB::beginTransaction();
        try {
            $cycle = DesignTaskCycle::create([
                'moderator_id' => $moderator->id,
                'designer_employee_id' => $designer->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'specifications' => $validated['specifications'],
                'priority' => $validated['priority'],
                'deadline_at' => $deadlineAt,
                'status' => DesignTaskCycle::STATUS_PENDING_DESIGN,
            ]);

            $designerTask = EmployeeTask::create([
                'employee_id' => $designer->id,
                'assigned_by' => $assignedById,
                'title' => $validated['title'],
                'description' => $fullDescription,
                'task_type' => 'design',
                'priority' => $validated['priority'],
                'status' => 'pending',
                'deadline' => $deadlineDate,
                'notes' => 'مهمة تصميم — دورة #'.$cycle->id.' — يُرجى الالتزام بحد التسليم.',
                'design_cycle_id' => $cycle->id,
            ]);

            $cycle->update(['designer_task_id' => $designerTask->id]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return null;
        }

        $this->taskAssignmentNotifier->notifyAssigned($designerTask->fresh());

        return $cycle->fresh();
    }
}
