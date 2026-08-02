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
use Symfony\Component\HttpFoundation\StreamedResponse;

class MetaSocialLeadCenterController extends Controller
{
    public function __construct(
        private MetaSocialLeadCenterService $leads,
        private MetaSocialCrmService $crm,
        private MetaSocialInboxService $inbox,
        private MetaSocialGraphService $graph,
    ) {}

    /**
     * @return array<string, mixed>
     */
    private function filtersFrom(Request $request): array
    {
        return [
            'page' => (int) $request->query('page'),
            'tab' => (string) $request->query('tab', 'all'),
            'q' => trim((string) $request->query('q', '')),
            'assigned_to' => $request->query('assigned_to'),
            'stage' => $request->query('stage'),
            'label' => $request->query('label'),
            'priority' => $request->query('priority'),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
            'sort' => (string) $request->query('sort', 'recent'),
            'view' => (string) $request->query('view', 'list'),
        ];
    }

    public function index(Request $request)
    {
        try {
            $tablesReady = $this->leads->ready();
            $crmReady = $tablesReady && $this->crm->crmReady();
            $columnsReady = $tablesReady && $this->leads->leadCenterColumnsReady();
            $connectionMeta = $this->graph->connectionMeta();

            $pages = $tablesReady
                ? MetaSocialPage::query()->where('is_active', true)->orderBy('page_name')->get()
                : collect();

            $filters = $this->filtersFrom($request);
            $stats = $tablesReady ? $this->leads->stats($filters['page'] ?: null) : [];
            $rows = $tablesReady
                ? $this->leads->listLeads($filters)->map(fn ($c) => $this->leads->serializeRow($c))
                : collect();
            $pipeline = ($tablesReady && $filters['view'] === 'pipeline')
                ? $this->leads->pipelineGroups($filters)
                : [];

            $selectedId = (int) $request->query('lead', $request->query('conversation', 0));
            $selected = null;
            $detail = null;
            $with = ['page', 'assignee:id,name'];
            if ($crmReady) {
                $with[] = 'salesLead';
            }
            if ($tablesReady && $selectedId > 0) {
                $selected = MetaSocialConversation::query()->with($with)->find($selectedId);
                if ($selected) {
                    $detail = $this->leads->serializeDetail($selected);
                }
            } elseif ($rows->isNotEmpty() && $filters['view'] !== 'pipeline') {
                $selectedId = (int) $rows->first()['id'];
                $selected = MetaSocialConversation::query()->with($with)->find($selectedId);
                if ($selected) {
                    $detail = $this->leads->serializeDetail($selected);
                }
            }

            $agents = [];
            try {
                $agents = $crmReady ? $this->crm->eligibleAgents() : [];
            } catch (\Throwable) {
                $agents = [];
            }
            $stages = MetaSocialConversation::LEAD_STAGES;
            $crmStages = SalesLead::STAGES;
            $priorities = MetaSocialConversation::PRIORITIES;
            $suggestedLabels = MetaSocialConversation::SUGGESTED_LABELS;
            $pageError = null;

            return view('admin.meta-social.leads', compact(
                'tablesReady',
                'crmReady',
                'columnsReady',
                'connectionMeta',
                'pages',
                'filters',
                'stats',
                'rows',
                'pipeline',
                'selected',
                'selectedId',
                'detail',
                'agents',
                'stages',
                'crmStages',
                'priorities',
                'suggestedLabels',
                'pageError',
            ));
        } catch (\Throwable $e) {
            report($e);

            return view('admin.meta-social.leads', [
                'tablesReady' => false,
                'crmReady' => false,
                'columnsReady' => false,
                'connectionMeta' => ['can_use' => false, 'label' => 'خطأ'],
                'pages' => collect(),
                'filters' => $this->filtersFrom($request),
                'stats' => [],
                'rows' => collect(),
                'pipeline' => [],
                'selected' => null,
                'selectedId' => 0,
                'detail' => null,
                'agents' => [],
                'stages' => MetaSocialConversation::LEAD_STAGES,
                'crmStages' => SalesLead::STAGES,
                'priorities' => MetaSocialConversation::PRIORITIES,
                'suggestedLabels' => MetaSocialConversation::SUGGESTED_LABELS,
                'pageError' => $e->getMessage(),
            ]);
        }
    }

    public function poll(Request $request): JsonResponse
    {
        if (! $this->leads->ready()) {
            return response()->json(['success' => false], 503);
        }

        $filters = $this->filtersFrom($request);
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
            if (($filters['view'] ?? '') === 'pipeline') {
                $payload['pipeline'] = $this->leads->pipelineGroups($filters);
            }
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

    public function export(Request $request): StreamedResponse
    {
        abort_unless($this->leads->ready(), 503);

        return $this->leads->exportCsv($this->filtersFrom($request));
    }

    private function ok(MetaSocialConversation $conversation): JsonResponse
    {
        $conversation = $conversation->fresh(['page', 'assignee', 'salesLead']);

        return response()->json([
            'success' => true,
            'detail' => $this->leads->serializeDetail($conversation),
            'row' => $this->leads->serializeRow($conversation),
        ]);
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
            if (! empty($validated['name']) || ! empty($validated['phone']) || ! empty($validated['email'])) {
                $this->crm->updateContactDetails($conversation, $validated);
                $conversation->refresh();
            }
            $lead = $this->leads->ensureCrmLead(
                $conversation,
                $validated['assigned_to'] ?? null,
                auth()->id(),
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => collect($e->errors())->flatten()->first() ?: 'تعذّر إنشاء Lead',
            ], 422);
        }

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
            'assigned_to' => 'nullable|integer|exists:users,id',
        ]);

        if (empty($validated['assigned_to'])) {
            $conversation = $this->leads->unassign($conversation);
        } else {
            $conversation = $this->crm->assign($conversation, (int) $validated['assigned_to'], auth()->id());
        }

        return $this->ok($conversation);
    }

    public function updateContact(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|in:open,closed',
        ]);

        if ($this->crm->crmReady()) {
            $conversation = $this->crm->updateContactDetails($conversation, $validated);
            if ($conversation->sales_lead_id) {
                $leadUpdates = array_filter([
                    'name' => $validated['name'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ], fn ($v) => $v !== null && $v !== '');
                if ($leadUpdates !== []) {
                    SalesLead::query()->where('id', $conversation->sales_lead_id)->update($leadUpdates);
                }
            }
        } else {
            $conversation->update(array_filter([
                'participant_name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'] ?? null,
            ], fn ($v) => $v !== null));
            MetaSocialContactCaptureService::bumpInboxVersion();
        }

        return $this->ok($conversation);
    }

    public function updateStage(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'stage' => ['required', 'string', Rule::in(array_keys(MetaSocialConversation::LEAD_STAGES))],
        ]);

        try {
            $conversation = $this->leads->updateStage($conversation, $validated['stage'], auth()->id());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => collect($e->errors())->flatten()->first() ?: 'فشل تحديث المرحلة',
            ], 422);
        }

        return $this->ok($conversation);
    }

    public function updatePriority(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'priority' => ['required', 'string', Rule::in(array_keys(MetaSocialConversation::PRIORITIES))],
        ]);

        return $this->ok($this->leads->updatePriority($conversation, $validated['priority']));
    }

    public function updateReminder(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'reminder_at' => 'nullable|date',
        ]);

        return $this->ok($this->leads->updateReminder($conversation, $validated['reminder_at'] ?? null));
    }

    public function updateLabels(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'labels' => 'nullable|array|max:12',
            'labels.*' => 'string|max:40',
        ]);

        return $this->ok($this->leads->updateLabels($conversation, $validated['labels'] ?? []));
    }

    public function markDone(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'done' => 'nullable|boolean',
        ]);
        $done = array_key_exists('done', $validated) ? (bool) $validated['done'] : true;

        return $this->ok($this->leads->markDone($conversation, $done));
    }

    public function requestPhone(MetaSocialConversation $conversation): JsonResponse
    {
        $result = $this->inbox->requestPhoneNumber($conversation, auth()->id());
        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل'], 422);
        }

        return $this->ok($conversation);
    }

    public function reply(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $result = $this->inbox->sendReply($conversation, $validated['body'], auth()->id());
        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل الإرسال'], 422);
        }

        // إعادة فتح لو كانت Done
        if ($conversation->status === MetaSocialConversation::STATUS_CLOSED) {
            $this->leads->markDone($conversation, false);
        }

        return $this->ok($conversation->fresh());
    }

    public function bulk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|max:100',
            'ids.*' => 'integer',
            'action' => 'required|in:done,reopen,assign,unassign,stage,priority,create_crm',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'stage' => ['nullable', 'string', Rule::in(array_keys(MetaSocialConversation::LEAD_STAGES))],
            'priority' => ['nullable', 'string', Rule::in(array_keys(MetaSocialConversation::PRIORITIES))],
        ]);

        $result = $this->leads->bulkAction(
            $validated['ids'],
            $validated['action'],
            [
                'assigned_to' => $validated['assigned_to'] ?? null,
                'stage' => $validated['stage'] ?? null,
                'priority' => $validated['priority'] ?? null,
            ],
            auth()->id(),
        );

        return response()->json(['success' => true, 'updated' => $result['updated']]);
    }
}
