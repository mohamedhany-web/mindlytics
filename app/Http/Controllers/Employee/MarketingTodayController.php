<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Services\MarketingPlanEventAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingTodayController extends Controller
{
    public function index(MarketingPlanEventAutomationService $service)
    {
        $user = Auth::user();
        $events = $service->todayEventsForUser($user);

        $stats = [
            'total' => $events->count(),
            'pending' => $events->filter(fn ($e) => $e->requires_confirmation && ! $e->isConfirmed())->count(),
            'confirmed' => $events->filter(fn ($e) => $e->isConfirmed())->count(),
        ];

        return view('employee.marketing-today.index', compact('events', 'stats'));
    }

    public function confirm(ModeratorMarketingCalendarEvent $event, MarketingPlanEventAutomationService $service)
    {
        $user = Auth::user();

        if (! $service->userCanConfirm($event, $user)) {
            abort(403);
        }

        if ($event->isConfirmed()) {
            return back()->with('success', 'تم التأكيد مسبقاً.');
        }

        $service->confirmExecution($event, $user);

        return back()->with('success', 'تم تأكيد تنفيذ المحتوى — شكراً للالتزام.');
    }
}
