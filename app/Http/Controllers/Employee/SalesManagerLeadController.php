<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\WhatsAppConversation;
use App\Services\SalesTeamService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalesManagerLeadController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        $members = $team->members()->with('user:id,name')->get();

        $query = SalesLead::query()
            ->whereIn('assigned_to', $memberIds)
            ->with(['assignee:id,name', 'category']);

        if ($request->filled('assignee')) {
            $query->where('assigned_to', (int) $request->assignee);
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('stale')) {
            $d = SalesLead::STALE_CONTACT_DAYS;
            $query->openPipeline()->where(function ($q) use ($d) {
                $q->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($d))
                    ->orWhere('last_contacted_at', '<', now()->subDays($d));
            });
        }

        if ($request->get('follow_up') === 'today') {
            $query->openPipeline()
                ->whereNotNull('next_follow_up_at')
                ->whereDate('next_follow_up_at', today());
        } elseif ($request->get('follow_up') === 'overdue') {
            $query->openPipeline()
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now());
        }

        $sort = $request->get('sort', 'updated');
        match ($sort) {
            'follow_up' => $query->orderByRaw('next_follow_up_at IS NULL')->orderBy('next_follow_up_at'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('updated_at'),
        };

        $leads = $query->paginate(20)->withQueryString();
        $categories = SalesLeadCategory::active()->ordered()->get();

        $quickCounts = $this->indexQuickCounts($memberIds);

        return view('employee.sales-manager.leads.index', compact(
            'leads',
            'categories',
            'quickCounts',
            'members',
            'team'
        ));
    }

    public function show(SalesLead $lead)
    {
        $this->authorizeTeamLead($lead);
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $members = $team->members()->with('user:id,name')->get();

        $lead->load([
            'assignee:id,name,email,phone',
            'creator:id,name',
            'category',
            'group',
            'advancedCourse:id,title,price',
            'offlineCourse:id,title,price',
            'legacyCourse:id,title,price',
            'activities.user:id,name',
            'transfers.fromUser:id,name',
            'transfers.toUser:id,name',
            'transfers.transferredBy:id,name',
        ]);

        $whatsappConversation = WhatsAppConversation::query()
            ->where(function ($q) use ($lead) {
                $q->where('sales_lead_id', $lead->id);
                if ($lead->phone) {
                    $digits = preg_replace('/[^0-9]/', '', (string) $lead->phone);
                    if (strlen($digits) >= 8) {
                        $q->orWhere('phone_number', 'like', '%'.substr($digits, -9).'%');
                    }
                }
            })
            ->latest('last_message_at')
            ->first();

        $whatsappInboxUrl = $whatsappConversation
            ? route('employee.sales-manager.whatsapp.inbox.index', ['conversation' => $whatsappConversation->id])
            : ($lead->phone
                ? route('employee.sales-manager.whatsapp.inbox.index', ['start_lead' => $lead->id])
                : route('employee.sales-manager.whatsapp.inbox.index'));

        $pipelineStages = array_keys(SalesLead::STAGES);
        $currentStageIndex = array_search($lead->stage, $pipelineStages, true);
        if ($currentStageIndex === false) {
            $currentStageIndex = 0;
        }

        return view('employee.sales-manager.leads.show', compact(
            'lead',
            'members',
            'team',
            'whatsappConversation',
            'whatsappInboxUrl',
            'pipelineStages',
            'currentStageIndex'
        ));
    }

    public function transfer(Request $request, SalesLead $lead): RedirectResponse
    {
        $this->authorizeTeamLead($lead);
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $validated = $request->validate([
            'to_user_id' => ['required', 'integer', Rule::in($memberIds)],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->teamService->transferLead(
            Auth::user(),
            $lead,
            (int) $validated['to_user_id'],
            $validated['reason'] ?? null
        );

        return back()->with('success', 'تم تحويل العميل المحتمل بنجاح.');
    }

    /** @param list<int> $memberIds */
    private function indexQuickCounts(array $memberIds): array
    {
        $base = SalesLead::query()->whereIn('assigned_to', $memberIds);
        $open = fn (): Builder => (clone $base)->openPipeline();

        return [
            'today' => $open()->whereNotNull('next_follow_up_at')->whereDate('next_follow_up_at', today())->count(),
            'overdue' => $open()->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())->count(),
            'stale' => $this->staleCount($base),
            'new' => (clone $base)->where('stage', 'new')->count(),
        ];
    }

    private function staleCount(Builder $base): int
    {
        $d = SalesLead::STALE_CONTACT_DAYS;

        return (clone $base)->openPipeline()->where(function ($q) use ($d) {
            $q->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($d))
                ->orWhere('last_contacted_at', '<', now()->subDays($d));
        })->count();
    }

    private function authorizeTeamLead(SalesLead $lead): void
    {
        if (! $this->teamService->canAccessLead(Auth::user(), $lead)) {
            abort(403);
        }
    }
}
