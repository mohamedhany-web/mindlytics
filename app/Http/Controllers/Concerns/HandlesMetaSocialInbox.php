<?php

namespace App\Http\Controllers\Concerns;

use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Models\MetaSocialPage;
use App\Models\SalesLead;
use App\Services\MetaSocial\MetaSocialContactCaptureService;
use App\Services\MetaSocial\MetaSocialCrmService;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

trait HandlesMetaSocialInbox
{
    abstract protected function metaInboxService(): MetaSocialInboxService;

    abstract protected function metaCrmService(): MetaSocialCrmService;

    abstract protected function metaGraphService(): MetaSocialGraphService;

    /** admin | employee | sales_manager */
    protected function metaInboxAudience(): string
    {
        return 'admin';
    }

    protected function metaInboxLayout(): string
    {
        return 'layouts.admin';
    }

    protected function metaInboxRoute(string $action, mixed ...$params): string
    {
        $prefix = 'admin.meta-social.inbox.';

        return match ($action) {
            'index' => route($prefix.'index', $params[0] ?? []),
            'poll' => route($prefix.'poll', $params[0] ?? []),
            'reply' => route($prefix.'reply', $params[0]),
            'assign' => route($prefix.'assign', $params[0]),
            'contact' => route($prefix.'contact', $params[0]),
            'create-lead' => route($prefix.'create-lead', $params[0]),
            'link-lead' => route($prefix.'link-lead', $params[0]),
            'enrich' => route($prefix.'enrich', $params[0]),
            'request-phone' => route($prefix.'request-phone', $params[0]),
            'sync-messages' => route($prefix.'sync-messages', $params[0]),
            'messages.update' => route($prefix.'messages.update', [$params[0], $params[1]]),
            'messages.destroy' => route($prefix.'messages.destroy', [$params[0], $params[1]]),
            default => route($prefix.'index'),
        };
    }

    protected function metaInboxCanAssignOthers(): bool
    {
        return $this->metaInboxAudience() === 'admin';
    }

    protected function metaInboxAutoClaim(): bool
    {
        return in_array($this->metaInboxAudience(), ['employee', 'sales_manager'], true);
    }

    protected function metaInboxClaim(MetaSocialConversation $conversation): MetaSocialConversation
    {
        if (! $this->metaInboxAutoClaim() || ! auth()->id()) {
            return $conversation;
        }

        if (! $this->metaCrmService()->crmReady()) {
            return $conversation;
        }

        return $this->metaCrmService()->claimOnSalesAction($conversation, (int) auth()->id());
    }

    /** @return array<string, mixed> */
    protected function metaInboxExtraViewData(): array
    {
        return [];
    }

    protected function metaInboxIndex(Request $request): View
    {
        $inbox = $this->metaInboxService();
        $crm = $this->metaCrmService();
        $graph = $this->metaGraphService();
        $audience = $this->metaInboxAudience();

        $tablesReady = $inbox->tablesReady();
        $crmReady = $tablesReady && $crm->crmReady();
        $connectionMeta = $graph->connectionMeta();

        $pages = $tablesReady
            ? MetaSocialPage::query()->where('is_active', true)->orderBy('page_name')->get()
            : collect();

        $pageId = (int) $request->query('page');
        $conversationId = (int) $request->query('conversation');
        $assignedFilter = $request->query('assigned_to');
        $platformFilter = $request->query('platform');
        $statusFilter = $request->query('status');
        $unreadOnly = $request->boolean('unread');
        $search = trim((string) $request->query('q', ''));

        $conversations = collect();
        $activeConversation = null;
        $messages = collect();
        $crmPayload = null;
        $agents = $crmReady ? $crm->eligibleAgents() : [];
        $filterCounts = [
            'all' => 0,
            'unread' => 0,
            'messenger' => 0,
            'instagram' => 0,
            'open' => 0,
            'closed' => 0,
        ];

        if ($tablesReady) {
            $baseCounts = MetaSocialConversation::query();
            if ($pageId > 0) {
                $baseCounts->where('meta_social_page_id', $pageId);
            }
            $filterCounts['all'] = (clone $baseCounts)->count();
            $filterCounts['unread'] = (clone $baseCounts)->where('unread_count', '>', 0)->count();
            $filterCounts['messenger'] = (clone $baseCounts)->where('platform', 'messenger')->count();
            $filterCounts['instagram'] = (clone $baseCounts)->where('platform', 'instagram')->count();
            $filterCounts['open'] = (clone $baseCounts)->where('status', MetaSocialConversation::STATUS_OPEN)->count();
            $filterCounts['closed'] = (clone $baseCounts)->where('status', MetaSocialConversation::STATUS_CLOSED)->count();

            $query = MetaSocialConversation::query()
                ->with(['page', 'assignee:id,name', 'salesLead:id,name,stage,phone'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('id');

            if ($pageId > 0) {
                $query->where('meta_social_page_id', $pageId);
            }
            if ($platformFilter && in_array($platformFilter, ['messenger', 'instagram'], true)) {
                $query->where('platform', $platformFilter);
            }
            if ($statusFilter === 'open' || $statusFilter === 'closed') {
                $query->where('status', $statusFilter);
            }
            if ($unreadOnly) {
                $query->where('unread_count', '>', 0);
            }
            if ($assignedFilter === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif (is_numeric($assignedFilter) && (int) $assignedFilter > 0) {
                $query->where('assigned_to', (int) $assignedFilter);
            }
            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('participant_name', 'like', '%'.$search.'%')
                        ->orWhere('participant_username', 'like', '%'.$search.'%')
                        ->orWhere('participant_id', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('last_message_preview', 'like', '%'.$search.'%');
                });
            }

            $conversations = $query->limit(500)->get();

            if ($conversationId > 0) {
                $activeConversation = MetaSocialConversation::query()
                    ->with(['page', 'assignee:id,name', 'salesLead'])
                    ->find($conversationId);
            } elseif ($conversations->isNotEmpty()) {
                $activeConversation = MetaSocialConversation::query()
                    ->with(['page', 'assignee:id,name', 'salesLead'])
                    ->find($conversations->first()->id);
            }

            if ($activeConversation) {
                if ($request->boolean('sync_messages') || $activeConversation->messages()->count() < 5) {
                    $inbox->syncAllMessagesForConversation($activeConversation);
                }

                if ($crmReady && ! $activeConversation->participant_name) {
                    $crm->enrichParticipantProfile($activeConversation);
                    $activeConversation->refresh();
                }

                $activeConversation->load([
                    'page',
                    'assignee:id,name',
                    'salesLead',
                    'messages' => fn ($q) => $q->orderBy('sent_at')->orderBy('id'),
                    'messages.sentBy:id,name',
                ]);

                $inbox->markConversationRead($activeConversation);
                $messages = $activeConversation->messages;
                if ($crmReady) {
                    $crmPayload = $crm->serializeCrm($activeConversation, $audience);
                }
            }
        }

        $unreadTotal = $tablesReady ? (int) MetaSocialConversation::sum('unread_count') : 0;
        $connected = (bool) ($connectionMeta['can_use'] ?? false);
        $convId = $activeConversation?->id;

        $msUrls = [];
        if ($convId) {
            $replyPath = parse_url($this->metaInboxRoute('reply', $convId), PHP_URL_PATH) ?: '';
            $messagesBasePath = preg_replace('#/reply$#', '/messages', $replyPath) ?: ('/admin/meta-social/inbox/'.$convId.'/messages');
            $msUrls = [
                'assign' => $this->metaInboxRoute('assign', $convId),
                'contact' => $this->metaInboxRoute('contact', $convId),
                'createLead' => $this->metaInboxRoute('create-lead', $convId),
                'linkLead' => $this->metaInboxRoute('link-lead', $convId),
                'enrich' => $this->metaInboxRoute('enrich', $convId),
                'requestPhone' => $this->metaInboxRoute('request-phone', $convId),
                'syncMessages' => $this->metaInboxRoute('sync-messages', $convId),
                'messageUpdateBase' => url($messagesBasePath),
            ];
        }

        return view('admin.meta-social.inbox', array_merge([
            'tablesReady' => $tablesReady,
            'crmReady' => $crmReady,
            'connectionMeta' => $connectionMeta,
            'connected' => $connected,
            'pages' => $pages,
            'pageId' => $pageId,
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'unreadTotal' => $unreadTotal,
            'agents' => $agents,
            'crmPayload' => $crmPayload,
            'assignedFilter' => $assignedFilter,
            'platformFilter' => $platformFilter,
            'statusFilter' => $statusFilter,
            'unreadOnly' => $unreadOnly,
            'search' => $search,
            'filterCounts' => $filterCounts,
            'msLayout' => $this->metaInboxLayout(),
            'msAudience' => $audience,
            'msIndexUrl' => $this->metaInboxRoute('index'),
            'msPollUrl' => $this->metaInboxRoute('poll', array_filter([
                'page' => $pageId ?: null,
                'conversation' => $convId,
            ])),
            'msReplyUrl' => $convId ? $this->metaInboxRoute('reply', $convId) : '',
            'msUrls' => $msUrls,
            'msCanAssignOthers' => $this->metaInboxCanAssignOthers(),
            'msHideAdminLinks' => $audience !== 'admin',
            'msAutoClaim' => $this->metaInboxAutoClaim(),
        ], $this->metaInboxExtraViewData()));
    }

    protected function metaInboxReply(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $this->metaInboxClaim($conversation);
        $result = $this->metaInboxService()->sendReply($conversation->fresh(), $validated['body'], auth()->id());

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل'], 422);
        }

        /** @var MetaSocialMessage $message */
        $message = $result['message'];
        $payload = [
            'success' => true,
            'message' => $this->metaSerializeMessage($message, auth()->user()?->name),
        ];
        if ($this->metaCrmService()->crmReady()) {
            $payload['crm'] = $this->metaCrmService()->serializeCrm(
                $conversation->fresh(['page', 'assignee', 'salesLead']),
                $this->metaInboxAudience()
            );
        }

        return response()->json($payload);
    }

    protected function metaInboxUpdateMessage(Request $request, MetaSocialConversation $conversation, MetaSocialMessage $message): JsonResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $this->metaInboxClaim($conversation);
        $result = $this->metaInboxService()->editMessage($conversation, $message, $validated['body'], auth()->id());
        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل التعديل'], 422);
        }

        /** @var MetaSocialMessage $updated */
        $updated = $result['message'];

        return response()->json([
            'success' => true,
            'message' => $this->metaSerializeMessage($updated),
            'note' => 'تم التعديل في النظام فقط — Meta لا تدعم تعديل الرسائل عبر API',
        ]);
    }

    protected function metaInboxDestroyMessage(MetaSocialConversation $conversation, MetaSocialMessage $message): JsonResponse
    {
        $this->metaInboxClaim($conversation);
        $result = $this->metaInboxService()->deleteMessage($conversation, $message);
        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل المسح'], 422);
        }

        return response()->json([
            'success' => true,
            'deleted_id' => $message->id,
            'note' => 'تم المسح من النظام فقط — الرسالة قد تبقى ظاهرة عند العميل على Meta',
        ]);
    }

    protected function metaInboxAssign(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->metaCrmService()->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        if (! $this->metaInboxCanAssignOthers()) {
            // موظف السيلز: استلام لنفسه فقط
            $conversation = $this->metaCrmService()->claimOnSalesAction($conversation, (int) auth()->id());

            return response()->json([
                'success' => true,
                'crm' => $this->metaCrmService()->serializeCrm($conversation, $this->metaInboxAudience()),
            ]);
        }

        $eligibleIds = $this->metaCrmService()->eligibleAgentIds();
        $validated = $request->validate([
            'assigned_to' => ['required', 'integer', Rule::in($eligibleIds)],
        ]);

        $conversation = $this->metaCrmService()->assign($conversation, (int) $validated['assigned_to'], auth()->id());

        return response()->json([
            'success' => true,
            'crm' => $this->metaCrmService()->serializeCrm($conversation, $this->metaInboxAudience()),
        ]);
    }

    protected function metaInboxUpdateContact(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->metaCrmService()->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'notes' => 'nullable|string|max:5000',
            'status' => 'nullable|in:open,closed',
        ]);

        $this->metaInboxClaim($conversation);
        $conversation = $this->metaCrmService()->updateContactDetails($conversation->fresh(), $validated);

        return response()->json([
            'success' => true,
            'crm' => $this->metaCrmService()->serializeCrm($conversation, $this->metaInboxAudience()),
        ]);
    }

    protected function metaInboxCreateLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->metaCrmService()->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $eligibleIds = $this->metaCrmService()->eligibleAgentIds();
        $rules = [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'assigned_to' => ['nullable', 'integer', Rule::in($eligibleIds)],
        ];
        $validated = $request->validate($rules);

        if (! empty($validated['phone'])) {
            $conversation->update(['phone' => $validated['phone']]);
        }
        if (! empty($validated['email'])) {
            $conversation->update(['email' => $validated['email']]);
        }

        $assigneeId = $this->metaInboxCanAssignOthers()
            ? (isset($validated['assigned_to']) ? (int) $validated['assigned_to'] : null)
            : (int) auth()->id();

        $this->metaInboxClaim($conversation);
        $lead = $this->metaCrmService()->createLeadFromConversation(
            $conversation->fresh(),
            $assigneeId,
            auth()->id(),
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['name'] ?? null,
        );

        return response()->json([
            'success' => true,
            'lead_id' => $lead->id,
            'crm' => $this->metaCrmService()->serializeCrm(
                $conversation->fresh(['page', 'assignee', 'salesLead']),
                $this->metaInboxAudience()
            ),
        ]);
    }

    protected function metaInboxLinkLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->metaCrmService()->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $validated = $request->validate([
            'sales_lead_id' => 'required|integer|exists:sales_leads,id',
        ]);

        $this->metaInboxClaim($conversation);
        $lead = SalesLead::query()->findOrFail($validated['sales_lead_id']);
        $conversation = $this->metaCrmService()->linkLead($conversation->fresh(), $lead);
        if ($this->metaInboxAutoClaim()) {
            $conversation = $this->metaInboxClaim($conversation);
        }

        return response()->json([
            'success' => true,
            'crm' => $this->metaCrmService()->serializeCrm($conversation, $this->metaInboxAudience()),
        ]);
    }

    protected function metaInboxEnrich(MetaSocialConversation $conversation): JsonResponse
    {
        if (! $this->metaCrmService()->crmReady()) {
            return response()->json(['success' => false, 'error' => 'شغّل migrate أولاً'], 503);
        }

        $conversation = $this->metaCrmService()->enrichParticipantProfile($conversation);

        return response()->json([
            'success' => true,
            'crm' => $this->metaCrmService()->serializeCrm($conversation, $this->metaInboxAudience()),
        ]);
    }

    protected function metaInboxSyncMessages(MetaSocialConversation $conversation): JsonResponse
    {
        $result = $this->metaInboxService()->syncAllMessagesForConversation($conversation);

        if (! ($result['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'فشل جلب الرسائل',
            ], 422);
        }

        $messages = $conversation->messages()
            ->with('sentBy:id,name')
            ->orderBy('sent_at')
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => $this->metaSerializeMessage($m));

        return response()->json([
            'success' => true,
            'imported' => (int) ($result['imported'] ?? 0),
            'total_fetched' => (int) ($result['total'] ?? 0),
            'message_count' => $messages->count(),
            'messages' => $messages,
        ]);
    }

    protected function metaInboxRequestPhone(MetaSocialConversation $conversation): JsonResponse
    {
        $this->metaInboxClaim($conversation);
        $result = $this->metaInboxService()->requestPhoneNumber($conversation->fresh(), auth()->id());

        if (! ($result['success'] ?? false)) {
            return response()->json(['success' => false, 'error' => $result['error'] ?? 'فشل'], 422);
        }

        /** @var MetaSocialMessage $message */
        $message = $result['message'];
        $payload = [
            'success' => true,
            'message' => $this->metaSerializeMessage($message, auth()->user()?->name),
        ];
        if ($this->metaCrmService()->crmReady()) {
            $payload['crm'] = $this->metaCrmService()->serializeCrm(
                $conversation->fresh(['page', 'assignee', 'salesLead']),
                $this->metaInboxAudience()
            );
        }

        return response()->json($payload);
    }

    protected function metaInboxPoll(Request $request): JsonResponse
    {
        if (! $this->metaInboxService()->tablesReady()) {
            return response()->json(['success' => false], 503);
        }

        $pageId = (int) $request->query('page');
        $conversationId = (int) $request->query('conversation');
        $afterId = (int) $request->query('after_id', 0);
        $clientVersion = (string) $request->query('v', '');

        $inboxVersion = MetaSocialContactCaptureService::inboxVersion();
        $unreadTotal = (int) MetaSocialConversation::sum('unread_count');

        $payload = [
            'success' => true,
            'inbox_version' => $inboxVersion,
            'changed' => $clientVersion === '' || $clientVersion !== $inboxVersion,
            'unread_total' => $unreadTotal,
            'server_time' => now()->toIso8601String(),
        ];

        if (! $payload['changed'] && $afterId > 0 && $conversationId > 0) {
            $hasNew = MetaSocialMessage::query()
                ->where('meta_social_conversation_id', $conversationId)
                ->where('id', '>', $afterId)
                ->exists();
            if (! $hasNew) {
                return response()->json($payload);
            }
            $payload['changed'] = true;
        }

        if ($conversationId > 0) {
            $conversation = MetaSocialConversation::query()
                ->with(['assignee:id,name', 'salesLead:id,name,stage,phone,email'])
                ->find($conversationId);

            if ($conversation) {
                $messagesQuery = $conversation->messages()->with('sentBy:id,name')->orderBy('sent_at')->orderBy('id');
                if ($afterId > 0) {
                    $messagesQuery->where('id', '>', $afterId);
                }
                $messages = $messagesQuery->limit(200)->get();

                $payload['message_count'] = $conversation->messages()->count();
                $payload['messages'] = $messages->map(fn ($m) => $this->metaSerializeMessage($m));
                if ($this->metaCrmService()->crmReady()) {
                    $payload['crm'] = $this->metaCrmService()->serializeCrm($conversation, $this->metaInboxAudience());
                }
            }
        }

        if ($pageId >= 0 && ($payload['changed'] || $clientVersion === '')) {
            $listQuery = MetaSocialConversation::query()
                ->with(['page:id,page_name', 'assignee:id,name'])
                ->orderByDesc('last_message_at')
                ->orderByDesc('id')
                ->limit(80);

            if ($pageId > 0) {
                $listQuery->where('meta_social_page_id', $pageId);
            }

            $payload['conversations'] = $listQuery->get()->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->displayName(),
                'platform' => $c->platform,
                'platform_label' => $c->platformLabel(),
                'page' => $c->page?->page_name,
                'preview' => $c->last_message_preview,
                'unread' => (int) $c->unread_count,
                'assignee' => $c->assignee?->name,
                'status' => $c->status,
                'has_crm' => (bool) $c->sales_lead_id,
                'phone' => $c->phone,
                'profile_pic' => $c->participant_profile_pic,
                'last_at' => $c->last_message_at?->format('H:i') ?? '',
                'last_human' => $c->last_message_at?->diffForHumans(),
            ]);
        }

        return response()->json($payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function metaSerializeMessage(MetaSocialMessage $message, ?string $authorFallback = null): array
    {
        $message->loadMissing('sentBy:id,name');
        $meta = is_array($message->meta) ? $message->meta : [];

        return [
            'id' => $message->id,
            'body' => $message->displayBody(),
            'direction' => $message->direction,
            'message_type' => $message->message_type,
            'attachment_url' => $message->attachment_url,
            'author' => $message->sentBy?->name ?: $authorFallback,
            'sent_at_human' => $message->sent_at?->format('H:i') ?? $message->created_at?->format('H:i'),
            'edited' => ! empty($meta['edited_at']),
        ];
    }
}
