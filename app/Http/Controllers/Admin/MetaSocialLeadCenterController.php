<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaSocialConversation;
use App\Models\MetaSocialPage;
use App\Models\SalesLead;
use App\Services\MetaSocial\MetaSocialContactCaptureService;
use App\Services\MetaSocial\MetaSocialCrmService;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialInboxService;
use App\Services\MetaSocial\MetaSocialLeadCenterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MetaSocialLeadCenterController extends Controller
{
    public function __construct(
        private MetaSocialLeadCenterService $leads,
        private MetaSocialCrmService $crm,
        private MetaSocialInboxService $inbox,
        private MetaSocialGraphService $graph,
    ) {}

    public function index(Request $request)
    {
        $tablesReady = $this->leads->ready();
        $crmReady = $tablesReady && $this->crm->crmReady();
        $connectionMeta = $this->graph->connectionMeta();

        $pages = $tablesReady
            ? MetaSocialPage::query()->where('is_active', true)->orderBy('page_name')->get()
            : collect();

        $filters = [
            'page' => (int) $request->query('page'),
            'tab' => (string) $request->query('tab', 'all'),
            'q' => trim((string) $request->query('q', '')),
            'assigned_to' => $request->query('assigned_to'),
            'stage' => $request->query('stage'),
        ];

        $stats = $tablesReady ? $this->leads->stats($filters['page'] ?: null) : [];
        $rows = $tablesReady
            ? $this->leads->listLeads($filters)->map(fn ($c) => $this->leads->serializeRow($c))
            : collect();

        $selectedId = (int) $request->query('lead', $request->query('conversation', 0));
        $selected = null;
        $detail = null;
        if ($tablesReady && $selectedId > 0) {
            $selected = MetaSocialConversation::query()
                ->with(['page', 'assignee:id,name', 'salesLead'])
                ->find($selectedId);
            if ($selected) {
                $detail = $this->leads->serializeDetail($selected);
            }
        } elseif ($rows->isNotEmpty()) {
            $selectedId = (int) $rows->first()['id'];
            $selected = MetaSocialConversation::query()
                ->with(['page', 'assignee:id,name', 'salesLead'])
                ->find($selectedId);
            if ($selected) {
                $detail = $this->leads->serializeDetail($selected);
            }
        }

        $agents = $crmReady ? $this->crm->eligibleAgents() : [];
        $stages = SalesLead::STAGES;

        return view('admin.meta-social.leads', compact(
            'tablesReady',
            'crmReady',
            'connectionMeta',
            'pages',
            'filters',
            'stats',
            'rows',
            'selected',
            'selectedId',
            'detail',
            'agents',
            'stages',
        ));
    }

    public function poll(Request $request): JsonResponse
    {
        if (! $this->leads->ready()) {
            return response()->json(['success' => false], 503);
        }

        $filters = [
            'page' => (int) $request->query('page'),
            'tab' => (string) $request->query('tab', 'all'),
            'q' => trim((string) $request->query('q', '')),
            'assigned_to' => $request->query('assigned_to'),
            'stage' => $request->query('stage'),
        ];
        $selectedId = (int) $request->query('lead', 0);
        $clientVersion = (string) $request->query('v', '');
        $inboxVersion = MetaSocialContactCaptureService::inboxVersion();

        $payload = [
            'success' => true,
            'inbox_version' => $inboxVersion,
            'changed' => $clientVersion === '' || $clientVersion !== $inboxVersion,
            'stats' => $this->leads->stats($filters['page'] ?: null),
        ];

        if ($payload['changed'] || $clientVersion === '') {
            $payload['rows'] = $this->leads->listLeads($filters)
                ->map(fn ($c) => $this->leads->serializeRow($c))
                ->values();
        }

        if ($selectedId > 0) {
            $selected = MetaSocialConversation::query()
                ->with(['page', 'assignee:id,name', 'salesLead'])
                ->find($selectedId);
            if ($selected) {
                $payload['detail'] = $this->leads->serializeDetail($selected);
            }
        }

        return response()->json($payload);
    }

    public function createLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'assigned_to' => 'nullable|integer|exists:users,id',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
        ]);

        try {
            $lead = $this->crm->createLeadFromConversation(
                $conversation,
                $validated['assigned_to'] ?? null,
                auth()->id(),
                $validated['phone'] ?? null,
                $validated['email'] ?? null,
                $validated['name'] ?? null,
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => collect($e->errors())->flatten()->first() ?: 'تعذّر إنشاء Lead',
            ], 422);
        }

        MetaSocialContactCaptureService::bumpInboxVersion();
        $conversation = $conversation->fresh(['page', 'assignee', 'salesLead']);

        return response()->json([
            'success' => true,
            'lead_id' => $lead->id,
            'detail' => $this->leads->serializeDetail($conversation),
            'row' => $this->leads->serializeRow($conversation),
        ]);
    }

    public function assign(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        $conversation = $this->crm->assign($conversation, (int) $validated['assigned_to'], auth()->id());
        MetaSocialContactCaptureService::bumpInboxVersion();

        return response()->json([
            'success' => true,
            'detail' => $this->leads->serializeDetail($conversation),
            'row' => $this->leads->serializeRow($conversation),
        ]);
    }

    public function updateContact(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->crm->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|in:open,closed',
        ]);

        $conversation = $this->crm->updateContactDetails($conversation, $validated);

        if ($conversation->sales_lead_id) {
            $leadUpdates = array_filter([
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
            ], fn ($v) => $v !== null && $v !== '');
            if ($leadUpdates !== []) {
                SalesLead::query()->where('id', $conversation->sales_lead_id)->update($leadUpdates);
            }
        }

        MetaSocialContactCaptureService::bumpInboxVersion();
        $conversation = $conversation->fresh(['page', 'assignee', 'salesLead']);

        return response()->json([
            'success' => true,
            'detail' => $this->leads->serializeDetail($conversation),
            'row' => $this->leads->serializeRow($conversation),
        ]);
    }

    public function updateStage(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $conversation->sales_lead_id) {
            return response()->json(['success' => false, 'error' => 'أنشئ Lead في CRM أولاً'], 422);
        }

        $validated = $request->validate([
            'stage' => ['required', 'string', Rule::in(array_keys(SalesLead::STAGES))],
        ]);

        SalesLead::query()->where('id', $conversation->sales_lead_id)->update([
            'stage' => $validated['stage'],
            'stage_entered_at' => now(),
        ]);

        MetaSocialContactCaptureService::bumpInboxVersion();
        $conversation = $conversation->fresh(['page', 'assignee', 'salesLead']);

        return response()->json([
            'success' => true,
            'detail' => $this->leads->serializeDetail($conversation),
            'row' => $this->leads->serializeRow($conversation),
        ]);
    }
}
