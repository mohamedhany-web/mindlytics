<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Models\SalesLead;
use App\Domains\Sales\Models\SalesPlannedActivity;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * كاليندر CRM (Calendar في SuiteCRM): يوم / أسبوع / شهر، و«مشترك» للمدير والأدمن (صف لكل موظف).
 * بيعرض الأنشطة المخططة + متابعات العملاء اللي ملهاش نشاط مخطط (من «المتابعة التالية»).
 */
class SalesCrmCalendarController extends Controller
{
    use InteractsWithCrm;

    public const VIEWS = ['day' => 'يوم', 'week' => 'أسبوع', 'month' => 'شهر', 'shared' => 'مشترك'];

    public const COLORS = ['call' => '#B8492F', 'meeting' => '#534D64', 'task' => '#627587', 'follow_up' => '#C2410C'];

    public function index(Request $request): View
    {
        $scope = $this->scope($request);
        $mode = array_key_exists((string) $request->query('view'), self::VIEWS) ? (string) $request->query('view') : 'week';
        if ($mode === 'shared' && $scope->isRep()) {
            $mode = 'week';
        }
        $date = rescue(fn () => Carbon::parse((string) $request->query('date', today()->toDateString()))->startOfDay(), today(), false);
        $userId = $scope->isRep() ? null : ($request->integer('user') ?: null);

        [$from, $to] = match ($mode) {
            'day' => [$date->copy(), $date->copy()->endOfDay()],
            'month' => [$date->copy()->startOfMonth()->startOfWeek(Carbon::SATURDAY), $date->copy()->endOfMonth()->endOfWeek(Carbon::FRIDAY)],
            default => [$date->copy()->startOfWeek(Carbon::SATURDAY), $date->copy()->endOfWeek(Carbon::FRIDAY)],
        };

        $events = $this->events($scope, $from, $to, $userId);
        $step = match ($mode) {
            'day' => fn (Carbon $d, int $n) => $d->copy()->addDays($n),
            'month' => fn (Carbon $d, int $n) => $d->copy()->addMonthsNoOverflow($n),
            default => fn (Carbon $d, int $n) => $d->copy()->addWeeks($n),
        };

        $days = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $days[] = $d->copy();
        }

        return $this->view($scope, 'calendar', 'calendar', [
            'mode' => $mode,
            'date' => $date,
            'from' => $from,
            'to' => $to,
            'days' => $days,
            'events' => $events,
            'byDay' => $events->groupBy(fn ($e) => $e['at']->toDateString()),
            'prev' => $step($date, -1)->toDateString(),
            'next' => $step($date, 1)->toDateString(),
            'userId' => $userId,
            'members' => $scope->isRep() ? collect() : $scope->assignableUsers(),
            'sharedRows' => $mode === 'shared' ? $this->sharedRows($scope, $events, $userId) : collect(),
        ]);
    }

    /** @return Collection<int, array<string, mixed>> */
    private function events(CrmScope $scope, Carbon $from, Carbon $to, ?int $userId): Collection
    {
        $planned = $scope->activities()->where('status', '!=', 'cancelled')
            ->whereBetween('starts_at', [$from, $to])
            ->when($userId, fn ($q) => $q->where('assigned_to', $userId))
            ->with(['lead:id,name', 'student:id,name', 'assignee:id,name'])
            ->orderBy('starts_at')->get();

        $covered = $planned->filter(fn ($a) => $a->sales_lead_id && $a->isPlanned())
            ->map(fn ($a) => $a->sales_lead_id.'@'.$a->starts_at->format('Y-m-d H:i'))->flip();

        $events = $planned->map(fn (SalesPlannedActivity $a) => [
            'kind' => $a->kind,
            'label' => SalesPlannedActivity::kindLabel($a->kind),
            'title' => $a->title,
            'who' => $a->lead?->name ?? $a->student?->name,
            'at' => $a->starts_at,
            'end' => $a->endsAt(),
            'user_id' => (int) $a->assigned_to,
            'user' => $a->assignee?->name,
            'status' => $a->status,
            'late' => $a->isOverdue(),
            'url' => route($scope->route('activities.show'), $a->id),
        ]);

        $followUps = $scope->leads()->openPipeline()->whereNotIn('stage', SalesLead::WON_LIKE_STAGES)
            ->whereBetween('next_follow_up_at', [$from, $to])
            ->when($userId, fn ($q) => $q->where('assigned_to', $userId))
            ->with('assignee:id,name')->limit(500)->get(['id', 'name', 'assigned_to', 'next_follow_up_at', 'follow_up_channel'])
            ->reject(fn (SalesLead $l) => $covered->has($l->id.'@'.$l->next_follow_up_at->format('Y-m-d H:i')))
            ->map(fn (SalesLead $l) => [
                'kind' => 'follow_up',
                'label' => 'متابعة'.($l->follow_up_channel ? ' · '.(SalesLead::FOLLOW_UP_CHANNELS[$l->follow_up_channel] ?? $l->follow_up_channel) : ''),
                'title' => $l->name,
                'who' => $l->name,
                'at' => $l->next_follow_up_at,
                'end' => $l->next_follow_up_at->copy()->addMinutes(15),
                'user_id' => (int) $l->assigned_to,
                'user' => $l->assignee?->name,
                'status' => 'planned',
                'late' => $l->next_follow_up_at->isPast(),
                'url' => route($scope->route('leads.show'), $l->id),
            ]);

        return $events->concat($followUps)->sortBy(fn ($e) => $e['at']->timestamp)->values();
    }

    /** صف لكل موظف في النطاق، وأحداثه مجمّعة باليوم. */
    private function sharedRows(CrmScope $scope, Collection $events, ?int $userId): Collection
    {
        $members = $scope->assignableUsers();
        if ($userId) {
            $members = $members->where('id', $userId)->values();
        }
        $byUser = $events->groupBy('user_id');

        return $members->map(fn ($m) => [
            'user' => $m,
            'byDay' => ($byUser->get($m->id) ?? collect())->groupBy(fn ($e) => $e['at']->toDateString()),
        ]);
    }
}
