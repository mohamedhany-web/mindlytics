<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\EmployeeTaskDeliverable;
use App\Models\User;
use App\Services\EmployeeTaskAssignmentNotifier;
use App\Services\EmployeeTaskDeliverableService;
use App\Services\MontageDeliverablesExcelImportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeTaskController extends Controller
{
    public function __construct(
        protected EmployeeTaskDeliverableService $deliverableService,
        protected MontageDeliverablesExcelImportService $montageExcelImport,
        protected EmployeeTaskAssignmentNotifier $taskAssignmentNotifier
    ) {}

    /**
     * عرض قائمة المهام
     */
    public function index(Request $request)
    {
        $query = EmployeeTask::with(['employee.employeeJob', 'assigner']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($q) use ($search) {
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

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->task_type);
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
            'task_type' => 'required|in:general,video_editing,sales,design,design_moderator_delivery',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'deadline' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['status'] = 'pending';

        $task = EmployeeTask::create($validated);

        $this->taskAssignmentNotifier->notifyAssigned($task->fresh());

        return redirect()->route('admin.employee-tasks.show', $task)
            ->with('success', 'تم إضافة المهمة بنجاح');
    }

    /**
     * عرض تفاصيل مهمة
     */
    public function show(Request $request, EmployeeTask $employeeTask)
    {
        $employeeTask->load(['employee.employeeJob', 'assigner']);
        $totalDeliverables = $employeeTask->deliverables()->count();

        $deliverables = $employeeTask->deliverables()
            ->with('reviewer')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->where('title', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%")
                        ->orWhere('received_from', 'like', "%{$s}%")
                        ->orWhere('link_url', 'like', "%{$s}%")
                        ->orWhere('file_name', 'like', "%{$s}%")
                        ->orWhere('duration_before', 'like', "%{$s}%")
                        ->orWhere('duration_after', 'like', "%{$s}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('admin.employee-tasks.show', compact('employeeTask', 'deliverables', 'totalDeliverables'));
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
        $previousEmployeeId = (int) $employeeTask->employee_id;

        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'task_type' => 'required|in:general,video_editing,sales,design,design_moderator_delivery',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled,on_hold',
            'deadline' => 'nullable|date',
            'progress' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        // تحديث التواريخ بناءً على الحالة
        if ($validated['status'] === 'in_progress' && ! $employeeTask->started_at) {
            $validated['started_at'] = now();
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
            $validated['progress'] = 100;
        }

        $employeeTask->update($validated);

        if ($previousEmployeeId !== (int) $validated['employee_id']) {
            $this->taskAssignmentNotifier->notifyAssigned($employeeTask->fresh());
        }

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

    /**
     * تعديل تسليم (الإدارة)
     */
    public function updateDeliverable(Request $request, EmployeeTask $employee_task, EmployeeTaskDeliverable $deliverable)
    {
        $this->deliverableService->updateDeliverable($request, $employee_task, $deliverable);

        return redirect()->back()->with('success', 'تم تحديث التسليم بنجاح');
    }

    /**
     * حذف تسليم (الإدارة)
     */
    public function destroyDeliverable(EmployeeTask $employee_task, EmployeeTaskDeliverable $deliverable)
    {
        $this->deliverableService->destroyDeliverable($employee_task, $deliverable);

        return redirect()->back()->with('success', 'تم حذف التسليم بنجاح');
    }

    public function downloadMontageExcelTemplate(EmployeeTask $employee_task)
    {
        if (! $employee_task->isVideoEditing()) {
            abort(404);
        }

        $filename = 'montage-deliverables-template.xlsx';

        return response()->streamDownload(function () {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray([
                ['رابط_الفيديو', 'عنوان', 'ممن_استلمته', 'دقائق_قبل', 'دقائق_بعد', 'مدة_قبل', 'مدة_بعد', 'ملاحظات'],
                ['https://iframe.mediadelivery.net/...', 'مثال', 'المصدر', 12, 10, '12:00', '10:00', ''],
            ]);
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importMontageExcel(Request $request, EmployeeTask $employee_task)
    {
        if (! $employee_task->isVideoEditing()) {
            abort(404);
        }

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $result = $this->montageExcelImport->import($request->file('excel_file'), $employee_task);

        return redirect()->back()
            ->with('success', $result['message'])
            ->with('import_report', $result);
    }
}
