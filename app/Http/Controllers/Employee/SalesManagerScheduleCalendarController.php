<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesScheduleCalendarService;
use App\Services\SalesTeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesManagerScheduleCalendarController extends Controller
{
    public function index(Request $request, SalesTeamService $teamService, SalesScheduleCalendarService $calendar): View
    {
        $manager = Auth::user();
        $team = $teamService->managedTeamOrFail($manager);
        $memberIds = $teamService->memberUserIds($team);

        $weekStart = $calendar->resolveWeekStart($request->query('week'));
        $reps = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->with('workSchedule')
            ->orderBy('name')
            ->get();

        $grid = $calendar->buildWeek($reps, $weekStart);

        return view('employee.sales-manager.schedule-calendar.index', [
            'grid' => $grid,
            'weekStart' => $grid['week_start'],
            'weekEnd' => $grid['week_end'],
            'prevWeek' => $grid['week_start']->copy()->subWeek()->toDateString(),
            'nextWeek' => $grid['week_start']->copy()->addWeek()->toDateString(),
            'team' => $team,
            'scopeLabel' => 'فريق: '.$team->name,
            'routeName' => 'employee.sales-manager.schedule-calendar.index',
        ]);
    }
}
