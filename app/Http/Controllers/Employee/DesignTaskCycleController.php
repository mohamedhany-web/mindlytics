<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\DesignCycleModeratorPlannerItem;
use App\Models\DesignTaskCycle;
use App\Models\EmployeeTask;
use App\Models\User;
use App\Services\EmployeeTaskAssignmentNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DesignTaskCycleController extends Controller
{
    public function __construct(
        protected EmployeeTaskAssignmentNotifier $taskAssignmentNotifier
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DesignTaskCycle::query()
            ->where('moderator_id', $user->id)
            ->with(['designer.employeeJob', 'designerTask', 'moderatorDeliveryTask']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $cycles = $query->latest()->paginate(20)->withQueryString();

        return view('employee.design-cycles.index', compact('cycles'));
    }

    public function create()
    {
        $designers = User::designerEmployees()
            ->where('is_active', true)
            ->with('employeeJob')
            ->orderBy('name')
            ->get();

        return view('employee.design-cycles.create', compact('designers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'designer_employee_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'specifications' => ['required', 'string', 'max:20000'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'deadline_at' => ['required', 'date', 'after:now'],
        ], [
            'designer_employee_id.required' => 'اختر المصمم',
            'specifications.required' => 'أدخل تفاصيل التصميم المطلوب',
            'deadline_at.after' => 'حد التسليم يجب أن يكون في المستقبل',
        ]);

        $designer = User::query()
            ->employees()
            ->where('id', $validated['designer_employee_id'])
            ->where('is_active', true)
            ->first();

        if (! $designer || ! $designer->isDesignerEmployee()) {
            return back()->withErrors(['designer_employee_id' => 'المستخدم المختار ليس مصمماً نشطاً.'])->withInput();
        }

        if ((int) $designer->id === (int) $user->id) {
            return back()->withErrors(['designer_employee_id' => 'لا يمكن إسناد الطلب لنفسك كمصمم.'])->withInput();
        }

        $deadlineAt = Carbon::parse($validated['deadline_at']);
        $deadlineDate = $deadlineAt->toDateString();

        $fullDescription = trim(($validated['description'] ?? '')."\n\n".'— مواصفات التصميم —'."\n".$validated['specifications']);

        DB::beginTransaction();
        try {
            $cycle = DesignTaskCycle::create([
                'moderator_id' => $user->id,
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
                'assigned_by' => $user->id,
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

            return back()->with('error', 'تعذر إنشاء الطلب. حاول مرة أخرى.')->withInput();
        }

        $this->taskAssignmentNotifier->notifyAssigned($designerTask->fresh());

        return redirect()->route('employee.design-cycles.show', $cycle)
            ->with('success', 'تم إنشاء طلب التصميم وإسناد المهمة للمصمم.');
    }

    public function show(DesignTaskCycle $designTaskCycle)
    {
        $user = Auth::user();
        abort_unless((int) $designTaskCycle->moderator_id === (int) $user->id, 403);

        $designTaskCycle->load([
            'designer.employeeJob',
            'moderator',
            'designerTask.deliverables',
            'moderatorDeliveryTask.deliverables',
            'moderatorPlannerItems',
        ]);

        $deliverablesTimeline = $designTaskCycle->deliverablesTimelineForModerator();

        return view('employee.design-cycles.show', [
            'designCycle' => $designTaskCycle,
            'deliverablesTimeline' => $deliverablesTimeline,
        ]);
    }

    public function storePlannerItem(Request $request, DesignTaskCycle $designTaskCycle)
    {
        $user = Auth::user();
        abort_unless((int) $designTaskCycle->moderator_id === (int) $user->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'department' => ['nullable', 'string', 'max:120'],
            'time_slot' => ['nullable', 'string', 'max:80'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'title.required' => 'عنوان المهمة مطلوب',
        ]);

        $department = $validated['department'] ?? null;
        $timeSlot = $validated['time_slot'] ?? null;
        if ($department === '') {
            $department = null;
        }
        if ($timeSlot === '') {
            $timeSlot = null;
        }

        $maxSort = (int) $designTaskCycle->moderatorPlannerItems()->max('sort_order');

        DesignCycleModeratorPlannerItem::create([
            'design_task_cycle_id' => $designTaskCycle->id,
            'title' => $validated['title'],
            'department' => $department,
            'time_slot' => $timeSlot,
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => DesignCycleModeratorPlannerItem::STATUS_PENDING,
            'sort_order' => $maxSort + 1,
        ]);

        return back()->with('success', 'تمت إضافة بند لجدول تنظيمك.');
    }

    public function updatePlannerItem(
        Request $request,
        DesignTaskCycle $designTaskCycle,
        DesignCycleModeratorPlannerItem $planner_item
    ) {
        $user = Auth::user();
        abort_unless((int) $designTaskCycle->moderator_id === (int) $user->id, 403);
        abort_unless((int) $planner_item->design_task_cycle_id === (int) $designTaskCycle->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'department' => ['nullable', 'string', 'max:120'],
            'time_slot' => ['nullable', 'string', 'max:80'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:pending,in_progress,done,skipped'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $data = $validated;
        foreach (['department', 'time_slot'] as $k) {
            if (array_key_exists($k, $data) && $data[$k] === '') {
                $data[$k] = null;
            }
        }

        $planner_item->update($data);

        return back()->with('success', 'تم تحديث البند.');
    }

    public function destroyPlannerItem(
        DesignTaskCycle $designTaskCycle,
        DesignCycleModeratorPlannerItem $planner_item
    ) {
        $user = Auth::user();
        abort_unless((int) $designTaskCycle->moderator_id === (int) $user->id, 403);
        abort_unless((int) $planner_item->design_task_cycle_id === (int) $designTaskCycle->id, 404);

        $planner_item->delete();

        return back()->with('success', 'تم حذف البند.');
    }

    public function storeModeratorDelivery(Request $request, DesignTaskCycle $designTaskCycle)
    {
        $user = Auth::user();
        abort_unless((int) $designTaskCycle->moderator_id === (int) $user->id, 403);

        if ($designTaskCycle->status !== DesignTaskCycle::STATUS_DESIGN_SUBMITTED) {
            return back()->withErrors(['error' => 'يمكن إنشاء مهمة التسليم النهائي بعد تسليم المصمم فقط.']);
        }

        if ($designTaskCycle->moderator_delivery_task_id) {
            return back()->withErrors(['error' => 'تم إنشاء مهمة التسليم النهائي مسبقاً.']);
        }

        $validated = $request->validate([
            'delivery_notes' => ['nullable', 'string', 'max:5000'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $deadline = $validated['deadline'] ?? null;
        if ($deadline === null) {
            $deadline = now()->addDays(3)->toDateString();
        }

        $title = 'تسليم نهائي (مشرف): '.$designTaskCycle->title;
        $description = trim(
            ($validated['delivery_notes'] ?? '')
            ."\n\n"
            .'مرتبط بدورة تصميم #'.$designTaskCycle->id
            ."\n"
            .'بعد مراجعة تسليم المصمم، ارفع الملف النهائي للجهة المعنية من خلال تسليمات هذه المهمة.'
        );

        DB::beginTransaction();
        try {
            $modTask = EmployeeTask::create([
                'employee_id' => $user->id,
                'assigned_by' => $user->id,
                'title' => $title,
                'description' => $description,
                'task_type' => 'design_moderator_delivery',
                'priority' => $designTaskCycle->priority,
                'status' => 'pending',
                'deadline' => $deadline,
                'notes' => 'تسليم نهائي لدورة تصميم #'.$designTaskCycle->id,
                'design_cycle_id' => $designTaskCycle->id,
            ]);

            $designTaskCycle->update([
                'moderator_delivery_task_id' => $modTask->id,
                'status' => DesignTaskCycle::STATUS_MODERATOR_DELIVERY_PENDING,
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

    public function cancel(Request $request, DesignTaskCycle $designTaskCycle)
    {
        $user = Auth::user();
        abort_unless((int) $designTaskCycle->moderator_id === (int) $user->id, 403);

        if (in_array($designTaskCycle->status, [DesignTaskCycle::STATUS_COMPLETED, DesignTaskCycle::STATUS_CANCELLED], true)) {
            return back()->withErrors(['error' => 'لا يمكن إلغاء هذه الدورة.']);
        }

        $designTaskCycle->update(['status' => DesignTaskCycle::STATUS_CANCELLED]);

        return back()->with('success', 'تم إلغاء طلب التصميم.');
    }
}
