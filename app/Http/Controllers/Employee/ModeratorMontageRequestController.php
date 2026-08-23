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
            ->with(['montageEmployee.employeeJob', 'employeeTask', 'moderatorDeliveryTask']);

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
            'montage_employee_id.required' => 'اختر محرر الفيديو',
            'requirements.required' => 'أدخل متطلبات الفيديو المطلوب',
            'deadline_at.after' => 'حد التسليم يجب أن يكون في المستقبل',
        ]);

        $editor = User::query()
            ->employees()
            ->where('id', $validated['montage_employee_id'])
            ->where('is_active', true)
            ->first();

        if (! $editor || ! $editor->isVideoEditingEmployee()) {
            return back()->withErrors(['montage_employee_id' => 'المستخدم المختار ليس محرر فيديو نشطاً.'])->withInput();
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
            ->with('success', 'تم إنشاء طلب الفيديو وإسناده لمحرر الفيديو كمهمة.');
    }

    public function show(ModeratorMontageRequest $montage_request)
    {
        $user = Auth::user();
        abort_unless((int) $montage_request->moderator_id === (int) $user->id, 403);

        $montage_request->load([
            'montageEmployee.employeeJob',
            'moderator',
            'employeeTask.deliverables',
            'moderatorDeliveryTask.deliverables',
        ]);

        return view('employee.montage-requests.show', [
            'montageRequest' => $montage_request,
            'deliverablesTimeline' => $montage_request->deliverablesTimelineForModerator(),
        ]);
    }

    public function storeModeratorDelivery(Request $request, ModeratorMontageRequest $montage_request)
    {
        $user = Auth::user();
        abort_unless((int) $montage_request->moderator_id === (int) $user->id, 403);

        if ($montage_request->status !== ModeratorMontageRequest::STATUS_SUBMITTED) {
            return back()->withErrors(['error' => 'يمكن إنشاء مهمة التسليم النهائي بعد تسليم محرر الفيديو فقط.']);
        }

        if ($montage_request->moderator_delivery_task_id) {
            return back()->withErrors(['error' => 'تم إنشاء مهمة التسليم النهائي مسبقاً.']);
        }

        $validated = $request->validate([
            'delivery_notes' => ['nullable', 'string', 'max:5000'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $deadline = $validated['deadline'] ?? now()->addDays(3)->toDateString();

        $title = 'تسليم نهائي (مشرف): '.$montage_request->title;
        $description = trim(
            ($validated['delivery_notes'] ?? '')
            ."\n\n"
            .'مرتبط بطلب فيديو #'.$montage_request->id
            ."\n"
            .'بعد مراجعة تسليم محرر الفيديو، ارفع الملف النهائي للجهة المعنية من خلال تسليمات هذه المهمة.'
        );

        DB::beginTransaction();
        try {
            $modTask = EmployeeTask::create([
                'employee_id' => $user->id,
                'assigned_by' => $user->id,
                'title' => $title,
                'description' => $description,
                'task_type' => 'video_montage_moderator_delivery',
                'priority' => $montage_request->priority,
                'status' => 'pending',
                'deadline' => $deadline,
                'notes' => 'تسليم نهائي لطلب فيديو #'.$montage_request->id,
                'montage_request_id' => $montage_request->id,
            ]);

            $montage_request->update([
                'moderator_delivery_task_id' => $modTask->id,
                'status' => ModeratorMontageRequest::STATUS_MODERATOR_DELIVERY_PENDING,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors(['error' => 'تعذر إنشاء مهمة التسليم.']);
        }

        $this->taskAssignmentNotifier->notifyAssigned($modTask->fresh());

        return redirect()->route('employee.tasks.show', $modTask)
            ->with('success', 'تم إنشاء مهمة التسليم النهائي في «مهامي». أضف التسليمات ثم أكمل المهمة.');
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
