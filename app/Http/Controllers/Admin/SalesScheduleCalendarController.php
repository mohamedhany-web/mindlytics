<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesScheduleCalendarService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesScheduleCalendarController extends Controller
{
    public function index(Request $request, SalesScheduleCalendarService $calendar): View
    {
        $weekStart = $calendar->resolveWeekStart($request->query('week'));
        $reps = User::salesEmployees()
            ->where('is_active', true)
            ->with('workSchedule')
            ->orderBy('name')
            ->get();

        $grid = $calendar->buildWeek($reps, $weekStart);

        return view('admin.sales.schedule-calendar.index', [
            'grid' => $grid,
            'weekStart' => $grid['week_start'],
            'weekEnd' => $grid['week_end'],
            'prevWeek' => $grid['week_start']->copy()->subWeek()->toDateString(),
            'nextWeek' => $grid['week_start']->copy()->addWeek()->toDateString(),
            'scopeLabel' => 'كل موظفي المبيعات',
            'routeName' => 'admin.sales.schedule-calendar.index',
        ]);
    }
}
