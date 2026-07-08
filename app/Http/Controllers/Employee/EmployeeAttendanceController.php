<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\EmployeeAttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmployeeAttendanceController extends Controller
{
    public function __construct(
        private EmployeeAttendanceService $attendance,
    ) {}

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->isEmployee(), 403);
        abort_unless($user->isSubjectToWorkSchedule(), 404);

        $state = $this->attendance->getState($user);

        return response()->json([
            'success' => true,
            'attendance' => [
                'mode' => $state['mode'],
                'can_access' => $state['can_access'],
                'can_clock_in' => $state['can_clock_in'],
                'can_clock_out' => $state['can_clock_out'],
                'worked_seconds' => $state['worked_seconds'],
                'required_seconds' => $state['required_seconds'],
                'seconds_until_open' => $state['seconds_until_open'],
                'shift_starts_at' => $state['shift_starts_at'],
                'shift_ends_at' => $state['shift_ends_at'],
                'clock_in_at' => $state['clock_in_at'],
                'message' => $state['message'],
                'schedule_name' => $state['schedule']?->name,
            ],
        ]);
    }

    public function clockIn(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->isEmployee(), 403);
        abort_unless($user->isSubjectToWorkSchedule(), 403, 'نظام الدوام متاح لموظفي المبيعات فقط.');

        try {
            $this->attendance->clockIn($user, $request->ip());
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }

            return back()->withErrors($e->errors());
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تسجيل الحضور بنجاح.']);
        }

        return redirect()->route('employee.dashboard')->with('success', 'تم تسجيل الحضور — يوم عمل موفق!');
    }

    public function clockOut(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->isEmployee(), 403);
        abort_unless($user->isSubjectToWorkSchedule(), 403, 'نظام الدوام متاح لموظفي المبيعات فقط.');

        try {
            $record = $this->attendance->clockOut($user, $request->ip());
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }

            return back()->withErrors($e->errors());
        }

        $msg = 'تم إنهاء يوم العمل. ساعات العمل: ' . number_format(($record->worked_minutes ?? 0) / 60, 2) . ' ساعة.';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('employee.dashboard')->with('success', $msg);
    }
}
