<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Concerns\HandlesMetaSocialInbox;
use App\Http\Controllers\Controller;
use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Services\MetaSocial\MetaSocialCrmService;
use App\Services\MetaSocial\MetaSocialGraphService;
use App\Services\MetaSocial\MetaSocialInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Inbox ماسنجر/إنستجرام لموظفي المبيعات — نفس واجهة الأدمن.
 * يشوف كل الرسايل؛ أول رد/أكشن يربط المحادثة بحسابه للتقارير.
 */
class SalesMetaSocialInboxController extends Controller
{
    use HandlesMetaSocialInbox;

    public function __construct(
        private MetaSocialInboxService $inbox,
        private MetaSocialGraphService $graph,
        private MetaSocialCrmService $crm,
    ) {
        $this->middleware('sales.staff');
    }

    protected function metaInboxService(): MetaSocialInboxService
    {
        return $this->inbox;
    }

    protected function metaCrmService(): MetaSocialCrmService
    {
        return $this->crm;
    }

    protected function metaGraphService(): MetaSocialGraphService
    {
        return $this->graph;
    }

    protected function metaInboxAudience(): string
    {
        $user = auth()->user();
        if ($user?->isSalesManager()) {
            return 'sales_manager';
        }

        return 'employee';
    }

    protected function metaInboxLayout(): string
    {
        return 'layouts.employee';
    }

    protected function metaInboxRoute(string $action, mixed ...$params): string
    {
        $prefix = 'employee.sales.meta-social.inbox.';

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
        // مدير المبيعات يعيّن لأي موظف سيلز مؤهل؛ الموظف يستلم لنفسه فقط
        return $this->metaInboxAudience() === 'sales_manager';
    }

    /** @return array<string, mixed> */
    protected function metaInboxExtraViewData(): array
    {
        return [
            'waImmersiveInbox' => true,
            'waInboxTitle' => 'Messenger & Instagram',
            'waInboxSubtitle' => 'كل الرسايل · أول رد أو أكشن يربط المحادثة بك للتقارير',
        ];
    }

    public function index(Request $request): View
    {
        return $this->metaInboxIndex($request);
    }

    public function reply(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxReply($request, $conversation);
    }

    public function updateMessage(Request $request, MetaSocialConversation $conversation, MetaSocialMessage $message): JsonResponse
    {
        return $this->metaInboxUpdateMessage($request, $conversation, $message);
    }

    public function destroyMessage(MetaSocialConversation $conversation, MetaSocialMessage $message): JsonResponse
    {
        return $this->metaInboxDestroyMessage($conversation, $message);
    }

    public function assign(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxAssign($request, $conversation);
    }

    public function updateContact(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxUpdateContact($request, $conversation);
    }

    public function createLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxCreateLead($request, $conversation);
    }

    public function linkLead(Request $request, MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxLinkLead($request, $conversation);
    }

    public function enrich(MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxEnrich($conversation);
    }

    public function syncMessages(MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxSyncMessages($conversation);
    }

    public function requestPhone(MetaSocialConversation $conversation): JsonResponse
    {
        return $this->metaInboxRequestPhone($conversation);
    }

    public function poll(Request $request): JsonResponse
    {
        return $this->metaInboxPoll($request);
    }
}
