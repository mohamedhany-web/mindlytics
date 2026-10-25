<?php

namespace App\Domains\Messaging\Http\Controllers\Admin;

use App\Domains\Messaging\Http\Controllers\Concerns\HandlesMetaSocialInbox;
use App\Http\Controllers\Controller;
use App\Domains\Messaging\Models\MetaSocialConversation;
use App\Domains\Messaging\Models\MetaSocialMessage;
use App\Domains\Messaging\Services\MetaSocialCrmService;
use App\Domains\Messaging\Services\MetaSocialGraphService;
use App\Domains\Messaging\Services\MetaSocialInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetaSocialInboxController extends Controller
{
    use HandlesMetaSocialInbox;

    public function __construct(
        private MetaSocialInboxService $inbox,
        private MetaSocialGraphService $graph,
        private MetaSocialCrmService $crm,
    ) {}

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
