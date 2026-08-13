<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\ModeratorMontageRequest;
use App\Models\User;
use App\Services\EmployeeTaskAssignmentNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ModeratorMontageRequestController extends Controller
{
    public function __construct(
        protected EmployeeTaskAssignmentNotifier $taskAssignmentNotifier
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ModeratorMontageRequest::query()
            ->where('moderator_id', $user->id)
            ->with(['montageEmployee.employeeJob', 'employeeTask']);

        if ($request->filled('status') && array_key_exists($request->status, ModeratorMontageRequest::statuses())) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        return view('employee.montage-requests.index', compact('requests'));
    }

    public function create()
    {
        $editors = User::videoEditingEmployees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->orderBy('name')
            ->get();

        return view('employee.montage-requests.create', compact('editors'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'montage_employee_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'requirements' => ['required', 'string', 'max:20000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'deadline_at' => ['required', 'date', 'after:now'],
        ], [
            'montage_employee_id.required' => 'اختر موظف المونتاج',
            'requirements.required' => 'أدخل متطلبات الفيديو المطلوب',
            'deadline_at.after' => 'حد التسليم يجب أن يكون في المستقبل',
        ]);

        $editor = User::query()
            ->employees()
            ->where('id', $validated['montage_employee_id'])
            ->where('is_active', true)
            ->first();

        if (! $editor || ! $editor->isVideoEditingEmployee()) {
            return back()->withErrors(['montage_employee_id' => 'المستخدم المختار ليس موظف مونتاج نشطاً.'])->withInput();
        }

        if ((int) $editor->id === (int) $user->id) {
            return back()->withErrors(['montage_employee_id' => 'لا يمكن إسناد الطلب لنفسك.'])->withInput();
        }

        $deadlineAt = Carbon::parse($validated['deadline_at']);
        $deadlineDate = $deadlineAt->toDateString();
        $fullDescription = trim(
            ($validated['description'] ?? '')
            ."\n\n"
            .'— متطلبات الفيديو —'
            ."\n"
            .$validated['requirements']
            ."\n\n"
            .'ملاحظة: يمكن تسليم الفيديو برابط Google Drive أو برفع ملف.'
        );

        $montageTask = null;

        DB::beginTransaction();
        try {
            $montageRequest = ModeratorMontageRequest::create([
                'moderator_id' => $user->id,
                'montage_employee_id' => $editor->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'requirements' => $validated['requirements'],
                'priority' => $validated['priority'],
                'deadline_at' => $deadlineAt,
                'status' => ModeratorMontageRequest::STATUS_PENDING,
            ]);

            $montageTask = EmployeeTask::create([
                'employee_id' => $editor->id,
                'assigned_by' => $user->id,
                'title' => $validated['title'],
                'description' => $fullDescription,
                'task_type' => 'video_editing',
                'priority' => $validated['priority'],
                'status' => 'pending',
                'deadline' => $deadlineDate,
                'notes' => 'طلب مونتاج من مشرف المحتوى #'.$montageRequest->id.' — التسليم عبر Drive أو رفع ملف.',
                'montage_request_id' => $montageRequest->id,
                'flexible_video_delivery' => true,
            ]);

            $montageRequest->update(['employee_task_id' => $montageTask->id]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'تعذر إنشاء طلب المونتاج. حاول مرة أخرى.')->withInput();
        }

        $this->taskAssignmentNotifier->notifyAssigned($montageTask->fresh());

        return redirect()
            ->route('employee.montage-requests.show', $montageRequest)
            ->with('success', 'تم إنشاء طلب المونتاج وإسناده لموظف المونتاج كمهمة.');
    }

    public function show(ModeratorMontageRequest $montage_request)
    {
        $user = Auth::user();
        abort_unless((int) $montage_request->moderator_id === (int) $user->id, 403);

        $montage_request->load([
            'montageEmployee.employeeJob',
            'moderator',
            'employeeTask.deliverables',
        ]);

        return view('employee.montage-requests.show', [
            'montageRequest' => $montage_request,
        ]);
    }

    public function cancel(ModeratorMontageRequest $montage_request)
    {
        $user = Auth::user();
        abort_unless((int) $montage_request->moderator_id === (int) $user->id, 403);

        if (! $montage_request->isOpen()) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب.');
        }

        DB::beginTransaction();
        try {
            $montage_request->update([
                'status' => ModeratorMontageRequest::STATUS_CANCELLED,
            ]);

            if ($montage_request->employee_task_id) {
                $task = EmployeeTask::query()->find($montage_request->employee_task_id);
                if ($task && ! in_array($task->status, ['completed'], true)) {
                    $task->update([
                        'status' => 'on_hold',
                        'notes' => trim(($task->notes ?? '')."\nتم إلغاء طلب المونتاج من المشرف."),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'تعذر إلغاء الطلب.');
        }

        return back()->with('success', 'تم إلغاء طلب المونتاج.');
    }
}
