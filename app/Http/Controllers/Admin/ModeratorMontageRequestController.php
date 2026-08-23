<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\ModeratorMontageRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModeratorMontageRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ModeratorMontageRequest::query()
            ->with(['moderator', 'montageEmployee', 'employeeTask', 'moderatorDeliveryTask']);

        if ($request->filled('status') && array_key_exists($request->status, ModeratorMontageRequest::statuses())) {
            $query->where('status', $request->status);
        }
        if ($request->filled('moderator_id')) {
            $query->where('moderator_id', $request->moderator_id);
        }
        if ($request->filled('montage_employee_id')) {
            $query->where('montage_employee_id', $request->montage_employee_id);
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
                    ->orWhereHas('montageEmployee', fn ($q) => $q->where('name', 'like', '%'.$s.'%'));
            });
        }

        $requests = $query->latest()->paginate(25)->withQueryString();

        $moderators = User::moderatorEmployees()->where('is_active', true)->orderBy('name')->get();
        $editors = User::videoEditingEmployees()->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => ModeratorMontageRequest::count(),
            'pending' => ModeratorMontageRequest::whereIn('status', [
                ModeratorMontageRequest::STATUS_PENDING,
                ModeratorMontageRequest::STATUS_IN_PROGRESS,
            ])->count(),
            'awaiting_moderator' => ModeratorMontageRequest::where('status', ModeratorMontageRequest::STATUS_SUBMITTED)->count(),
            'in_delivery' => ModeratorMontageRequest::where('status', ModeratorMontageRequest::STATUS_MODERATOR_DELIVERY_PENDING)->count(),
            'completed' => ModeratorMontageRequest::where('status', ModeratorMontageRequest::STATUS_COMPLETED)->count(),
        ];

        return view('admin.montage-requests.index', compact('requests', 'moderators', 'editors', 'stats'));
    }

    public function show(ModeratorMontageRequest $montage_request)
    {
        $montage_request->load([
            'moderator.employeeJob',
            'montageEmployee.employeeJob',
            'employeeTask.deliverables',
            'moderatorDeliveryTask.deliverables',
        ]);

        return view('admin.montage-requests.show', [
            'montageRequest' => $montage_request,
            'deliverablesTimeline' => $montage_request->deliverablesTimelineForModerator(),
        ]);
    }

    public function cancel(ModeratorMontageRequest $montage_request)
    {
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
                        'notes' => trim(($task->notes ?? '')."\nتم إلغاء طلب الفيديو من الإدارة."),
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'تعذر إلغاء الطلب.');
        }

        return back()->with('success', 'تم إلغاء طلب الفيديو.');
    }
}
