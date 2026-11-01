<?php

namespace App\Domains\Sales\Http\Controllers\Crm;

use App\Domains\Sales\Models\SalesActivity;
use App\Domains\Sales\Models\SalesPlannedActivity;
use App\Domains\Sales\Services\SalesPlannedActivityService;
use App\Domains\Sales\Support\CrmScope;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * وحدة الأنشطة (Calls / Meetings / Tasks في SuiteCRM): قائمة، جدولة، تنفيذ، تعديل، إلغاء.
 * الموظف: أنشطته. المدير: أنشطة فريقه ويقدر يكلّف أي حد في الفريق. الأدمن: الكل.
 */
class SalesCrmActivityController extends Controller
{
    use InteractsWithCrm;

    public const QUICK = [
        'open' => 'المفتوحة',
        'today' => 'النهارده',
        'overdue' => 'المتأخرة',
        'week' => 'الأسبوع ده',
        'done' => 'المنتهية',
        'all' => 'الكل',
    ];

    public function __construct(private SalesPlannedActivityService $service) {}

    public function index(Request $request): View
    {
        $scope = $this->scope($request);
        $f = [
            'quick' => array_key_exists((string) $request->query('quick'), self::QUICK) ? (string) $request->query('quick') : 'open',
            'kind' => array_key_exists((string) $request->query('kind'), SalesPlannedActivity::KINDS) ? (string) $request->query('kind') : '',
            'assigned_to' => $scope->isRep() ? null : ($request->integer('assigned_to') ?: null),
            'q' => trim((string) $request->query('q', '')),
        ];

        $query = $this->filtered($scope, $f)->with(['lead:id,name,phone', 'student:id,name', 'assignee:id,name']);
        $f['quick'] === 'done' || $f['quick'] === 'all' ? $query->orderByDesc('starts_at') : $query->orderBy('starts_at');

        $counts = [];
        foreach (array_keys(self::QUICK) as $k) {
            $counts[$k] = $this->filtered($scope, ['quick' => $k] + $f)->count();
        }

        return $this->view($scope, 'activities', 'activities.index', [
            'activities' => $query->paginate(25)->withQueryString(),
            'filters' => $f,
            'counts' => $counts,
            'members' => $scope->isRep() ? collect() : $scope->assignableUsers(),
        ]);
    }

    public function create(Request $request): View
    {
        $scope = $this->scope($request);
        abort_unless($scope->can('activities.create'), 403);
        $lead = $request->integer('lead') ? $scope->leads()->find($request->integer('lead')) : null;
        $student = $request->integer('student') ? $scope->students()->find($request->integer('student')) : null;
        $kind = array_key_exists((string) $request->query('kind'), SalesPlannedActivity::KINDS) ? (string) $request->query('kind') : 'call';
        $date = $request->query('date') && strtotime((string) $request->query('date')) ? \Illuminate\Support\Carbon::parse((string) $request->query('date')) : null;
        $start = ($date && $date->format('H:i') !== '00:00') ? $date : ($date ?? now()->addDay())->copy()->setTime(10, 0);

        return $this->view($scope, 'activities', 'activities.form', [
            'activity' => new SalesPlannedActivity([
                'kind' => $kind,
                'title' => $lead ? SalesPlannedActivity::kindLabel($kind).' — '.$lead->name : ($student ? SalesPlannedActivity::kindLabel($kind).' — '.$student->name : ''),
                'starts_at' => $start,
                'duration_minutes' => $kind === 'meeting' ? 30 : 10,
                'priority' => 'normal',
                'assigned_to' => $lead && $scope->assignableUsers()->contains('id', $lead->assigned_to) ? $lead->assigned_to : $scope->user->id,
            ]),
            'lead' => $lead,
            'student' => $student,
            'assignable' => $scope->assignableUsers(),
            'leadOptions' => $lead ? collect() : $scope->leads()->openPipeline()->orderByDesc('updated_at')->limit(200)->get(['id', 'name', 'phone']),
            'redirectTo' => (string) $request->query('redirect_to', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $scope = $this->scope($request);
        abort_unless($scope->can('activities.create'), 403);
        $data = $this->validated($request, true);
        $activity = $this->service->schedule($scope, $data);

        return $this->backTo($request, route($scope->route('activities.show'), $activity->id), 'اتجدولت '.SalesPlannedActivity::kindLabel($activity->kind).': '.$activity->title);
    }

    public function show(Request $request, int $activity): View
    {
        $scope = $this->scope($request);
        $activity = $this->find($scope, $activity);

        return $this->view($scope, 'activities', 'activities.show', [
            'activity' => $activity,
            'others' => $activity->sales_lead_id
                ? $scope->activities()->where('sales_lead_id', $activity->sales_lead_id)->whereKeyNot($activity->id)->with('assignee:id,name')->orderByDesc('starts_at')->limit(10)->get()
                : collect(),
            'history' => $activity->sales_activity_id ? SalesActivity::query()->with('user:id,name')->find($activity->sales_activity_id) : null,
        ]);
    }

    public function edit(Request $request, int $activity): View
    {
        $scope = $this->scope($request);
        $activity = $this->find($scope, $activity);
        abort_unless($scope->canEditActivity($activity), 403);

        return $this->view($scope, 'activities', 'activities.form', [
            'activity' => $activity,
            'lead' => $activity->lead,
            'student' => $activity->student,
            'assignable' => $scope->assignableUsers(),
            'leadOptions' => collect(),
            'redirectTo' => '',
        ]);
    }

    public function update(Request $request, int $activity): RedirectResponse
    {
        $scope = $this->scope($request);
        $activity = $this->find($scope, $activity);
        abort_unless($scope->canEditActivity($activity), 403);
        $this->service->reschedule($scope, $activity, $this->validated($request, false));

        return redirect()->route($scope->route('activities.show'), $activity->id)->with('success', 'اتحفظ التعديل.');
    }

    public function complete(Request $request, int $activity): RedirectResponse
    {
        $scope = $this->scope($request);
        $activity = $this->find($scope, $activity);
        $data = $request->validate([
            'status' => ['required', Rule::in(['done', 'missed'])],
            'outcome' => ['nullable', Rule::in(array_keys(SalesActivity::OUTCOMES))],
            'result_notes' => ['nullable', 'string', 'max:2000'],
            'apply_stage' => ['nullable', 'boolean'],
        ]);
        $data['apply_stage'] = $request->boolean('apply_stage', true);
        $this->service->complete($scope, $activity, $data);

        return $this->backTo($request, route($scope->route('activities.show'), $activity->id),
            SalesPlannedActivity::kindLabel($activity->kind).' — '.SalesPlannedActivity::statusLabel($activity->status));
    }

    public function cancel(Request $request, int $activity): RedirectResponse
    {
        $scope = $this->scope($request);
        $activity = $this->find($scope, $activity);
        $this->service->cancel($scope, $activity);

        return $this->backTo($request, route($scope->route('activities.index')), 'اتلغى النشاط.');
    }

    private function find(CrmScope $scope, int $id): SalesPlannedActivity
    {
        $activity = SalesPlannedActivity::query()->with(['lead.assignee:id,name', 'student:id,name,phone,email', 'assignee:id,name', 'creator:id,name', 'completer:id,name'])->find($id);
        abort_unless($activity && $scope->canSeeActivity($activity), 404);

        return $activity;
    }

    private function filtered(CrmScope $scope, array $f): Builder
    {
        $query = $scope->activities();

        match ($f['quick']) {
            'open' => $query->planned(),
            'today' => $query->planned()->whereBetween('starts_at', [today(), today()->endOfDay()]),
            'overdue' => $query->planned()->where('starts_at', '<', now()),
            'week' => $query->where('status', '!=', 'cancelled')->whereBetween('starts_at', [today(), today()->addDays(7)->endOfDay()]),
            'done' => $query->whereIn('status', ['done', 'missed']),
            default => null,
        };
        if ($f['kind'] !== '') {
            $query->where('kind', $f['kind']);
        }
        if ($f['assigned_to']) {
            $query->where('assigned_to', $f['assigned_to']);
        }
        if ($f['q'] !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $f['q']).'%';
            $query->where(fn ($q) => $q->where('title', 'like', $like)
                ->orWhereHas('lead', fn ($l) => $l->where('name', 'like', $like)->orWhere('phone', 'like', $like))
                ->orWhereHas('student', fn ($s) => $s->where('name', 'like', $like)->orWhere('phone', 'like', $like)));
        }

        return $query;
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $creating): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'assigned_to' => ['required', 'integer'],
            'starts_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:480'],
            'priority' => ['nullable', Rule::in(array_keys(SalesPlannedActivity::PRIORITIES))],
        ];
        if ($creating) {
            $rules += [
                'kind' => ['required', Rule::in(array_keys(SalesPlannedActivity::KINDS))],
                'sales_lead_id' => ['nullable', 'integer'],
                'student_user_id' => ['nullable', 'integer'],
            ];
        }

        return $request->validate($rules, [
            'title.required' => 'اكتب موضوع النشاط.',
            'starts_at.required' => 'حدد الموعد.',
        ]);
    }
}
