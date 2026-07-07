<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendanceRecord;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkSchedule::query()->withCount('employees')->orderBy('name');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $schedules = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => WorkSchedule::count(),
            'active' => WorkSchedule::where('is_active', true)->count(),
            'inactive' => WorkSchedule::where('is_active', false)->count(),
            'assigned_employees' => User::employees()->whereNotNull('work_schedule_id')->count(),
        ];

        return view('admin.work-schedules.index', compact('schedules', 'stats'));
    }

    public function create()
    {
        return view('admin.work-schedules.create', [
            'dayOptions' => User::weeklyOffDayOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSchedule($request);
        WorkSchedule::create($validated);

        return redirect()->route('admin.work-schedules.index')->with('success', 'تم إنشاء موعد العمل.');
    }

    public function edit(WorkSchedule $workSchedule)
    {
        return view('admin.work-schedules.edit', [
            'schedule' => $workSchedule,
            'dayOptions' => User::weeklyOffDayOptions(),
        ]);
    }

    public function update(Request $request, WorkSchedule $workSchedule): RedirectResponse
    {
        $workSchedule->update($this->validateSchedule($request));

        return redirect()->route('admin.work-schedules.index')->with('success', 'تم تحديث موعد العمل.');
    }

    public function destroy(WorkSchedule $workSchedule): RedirectResponse
    {
        if ($workSchedule->employees()->exists()) {
            return back()->with('error', 'لا يمكن حذف موعد مرتبط بموظفين.');
        }

        $workSchedule->delete();

        return redirect()->route('admin.work-schedules.index')->with('success', 'تم الحذف.');
    }

    private function validateSchedule(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'required_hours' => 'required|numeric|min:1|max:24',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'integer|min:0|max:6',
            'grace_minutes' => 'nullable|integer|min:0|max:120',
            'early_access_minutes' => 'nullable|integer|min:0|max:120',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['grace_minutes'] = (int) ($validated['grace_minutes'] ?? 15);
        $validated['early_access_minutes'] = (int) ($validated['early_access_minutes'] ?? 10);
        $validated['work_days'] = array_values(array_unique(array_map('intval', $validated['work_days'])));

        return $validated;
    }
}
