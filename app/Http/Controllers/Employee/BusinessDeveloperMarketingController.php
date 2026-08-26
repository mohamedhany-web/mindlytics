<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\ModeratorMarketingCalendarEvent;
use App\Models\ModeratorMarketingPlan;
use App\Models\ModeratorMarketingPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BusinessDeveloperMarketingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(Auth::user()?->isBusinessDeveloper(), 403, 'مركز التسويق مخصص لـ Business Developer.');

            return $next($request);
        });
    }

    public function index(Request $request): View
    {
        $now = now();
        $weekEnd = $now->copy()->addDays(6)->endOfDay();
        $rangeEnd = $now->copy()->addDays(14)->endOfDay();

        $statusFilter = $request->get('status');
        if ($statusFilter && ! in_array($statusFilter, ['draft', 'active', 'paused', 'completed'], true)) {
            $statusFilter = null;
        }

        $plans = ModeratorMarketingPlan::query()
            ->with(['moderator:id,name', 'platforms'])
            ->withCount([
                'platforms',
                'calendarEvents',
                'calendarEvents as published_count' => fn ($q) => $q->where('status', 'published'),
                'calendarEvents as scheduled_count' => fn ($q) => $q->where('status', 'scheduled'),
                'calendarEvents as overdue_confirm_count' => fn ($q) => $q
                    ->where('requires_confirmation', true)
                    ->whereNull('execution_confirmed_at')
                    ->where('starts_at', '<', $now)
                    ->whereNotIn('status', ['skipped', 'published']),
            ])
            ->when($statusFilter, fn ($q) => $q->where('status', $statusFilter))
            ->latest()
            ->get();

        $stats = [
            'total' => $plans->count(),
            'active' => $plans->where('status', 'active')->count(),
            'draft' => $plans->where('status', 'draft')->count(),
            'paused' => $plans->where('status', 'paused')->count(),
            'completed' => $plans->where('status', 'completed')->count(),
            'platforms' => ModeratorMarketingPlatform::query()->count(),
            'events_total' => ModeratorMarketingCalendarEvent::query()->count(),
            'events_week' => ModeratorMarketingCalendarEvent::query()
                ->whereBetween('starts_at', [$now->copy()->startOfDay(), $weekEnd])
                ->count(),
            'published_week' => ModeratorMarketingCalendarEvent::query()
                ->where('status', 'published')
                ->whereBetween('starts_at', [$now->copy()->startOfDay(), $weekEnd])
                ->count(),
            'overdue_confirm' => ModeratorMarketingCalendarEvent::query()
                ->where('requires_confirmation', true)
                ->whereNull('execution_confirmed_at')
                ->where('starts_at', '<', $now)
                ->whereNotIn('status', ['skipped', 'published'])
                ->count(),
            'moderators' => $plans->pluck('moderator_id')->filter()->unique()->count(),
        ];

        $upcoming = ModeratorMarketingCalendarEvent::query()
            ->with([
                'plan:id,title,moderator_id,status',
                'plan.moderator:id,name',
                'platform:id,platform_key,custom_label,color_hex',
                'assignee:id,name',
            ])
            ->whereBetween('starts_at', [$now->copy()->startOfDay(), $rangeEnd])
            ->orderBy('starts_at')
            ->limit(50)
            ->get();

        $overdue = ModeratorMarketingCalendarEvent::query()
            ->with([
                'plan:id,title,moderator_id',
                'plan.moderator:id,name',
                'platform:id,platform_key,custom_label,color_hex',
                'assignee:id,name',
            ])
            ->where('requires_confirmation', true)
            ->whereNull('execution_confirmed_at')
            ->where('starts_at', '<', $now)
            ->whereNotIn('status', ['skipped', 'published'])
            ->orderByDesc('starts_at')
            ->limit(25)
            ->get();

        $platformMix = ModeratorMarketingPlatform::query()
            ->selectRaw('platform_key, COUNT(*) as total')
            ->groupBy('platform_key')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'key' => $row->platform_key,
                'label' => ModeratorMarketingPlatform::platformLabels()[$row->platform_key] ?? $row->platform_key,
                'total' => (int) $row->total,
            ]);

        $eventStatusMix = ModeratorMarketingCalendarEvent::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn ($row) => [(string) $row->status => (int) $row->total]);

        $timelineDays = collect(range(0, 6))->map(function (int $offset) use ($now) {
            $day = $now->copy()->addDays($offset)->startOfDay();

            return [
                'date' => $day->toDateString(),
                'label' => $day->locale('ar')->translatedFormat('D d'),
                'count' => ModeratorMarketingCalendarEvent::query()
                    ->whereDate('starts_at', $day->toDateString())
                    ->count(),
                'published' => ModeratorMarketingCalendarEvent::query()
                    ->whereDate('starts_at', $day->toDateString())
                    ->where('status', 'published')
                    ->count(),
            ];
        });

        $maxDay = max(1, (int) $timelineDays->max('count'));

        return view('employee.business-developer.marketing.command-center', [
            'plans' => $plans,
            'stats' => $stats,
            'upcoming' => $upcoming,
            'overdue' => $overdue,
            'platformMix' => $platformMix,
            'eventStatusMix' => $eventStatusMix,
            'timelineDays' => $timelineDays,
            'maxDay' => $maxDay,
            'statusFilter' => $statusFilter,
            'weekEnd' => $weekEnd,
            'rangeEnd' => $rangeEnd,
        ]);
    }
}
