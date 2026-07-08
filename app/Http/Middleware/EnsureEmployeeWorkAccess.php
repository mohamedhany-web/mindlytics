<?php

namespace App\Http\Middleware;

use App\Services\EmployeeAttendanceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeWorkAccess
{
    public function __construct(
        private EmployeeAttendanceService $attendance,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isEmployee()) {
            return $next($request);
        }

        if (! $user->isSubjectToWorkSchedule()) {
            return $next($request);
        }

        if ($this->isExemptRoute($request)) {
            return $next($request);
        }

        $state = $this->attendance->getState($user);

        if ($state['can_access'] ?? false) {
            view()->share('employeeAttendance', $state);

            return $next($request);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $state['message'] ?? 'النظام مغلق خارج وقت العمل.',
                'attendance' => $this->publicState($state),
            ], 423);
        }

        view()->share('employeeAttendance', $state);

        return response()->view('employee.attendance.locked', [
            'state' => $state,
        ]);
    }

    private function isExemptRoute(Request $request): bool
    {
        $name = $request->route()?->getName() ?? '';

        return str_starts_with($name, 'employee.attendance.')
            || str_starts_with($name, 'employee.presence.')
            || $name === 'logout';
    }

    private function publicState(array $state): array
    {
        return [
            'mode' => $state['mode'] ?? null,
            'can_clock_in' => $state['can_clock_in'] ?? false,
            'can_clock_out' => $state['can_clock_out'] ?? false,
            'worked_seconds' => $state['worked_seconds'] ?? 0,
            'required_seconds' => $state['required_seconds'] ?? 0,
            'seconds_until_open' => $state['seconds_until_open'] ?? 0,
            'shift_starts_at' => $state['shift_starts_at'] ?? null,
            'shift_ends_at' => $state['shift_ends_at'] ?? null,
            'message' => $state['message'] ?? '',
        ];
    }
}
