<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesWhatsAppInbox;
use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\WhatsAppTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppInboxController extends Controller
{
    use HandlesWhatsAppInbox;

    protected function inboxView(): string
    {
        return 'admin.whatsapp.inbox';
    }

    /** @return array<string, mixed> */
    protected function inboxExtraViewData(): array
    {
        return [
            'waImmersiveInbox' => true,
            'waInboxTitle' => 'محادثات الواتساب',
            'waInboxSubtitle' => 'ردّ على العملاء وتابع الـ Pipeline والـ CRM',
            'waAdminSettingsUrl' => route('admin.whatsapp.settings'),
            'waAdminReportsUrl' => route('admin.whatsapp.reports'),
            'waAdminWhatsAppUrl' => route('admin.whatsapp.index'),
        ];
    }

    protected function inboxAudience(): string
    {
        return 'admin';
    }

    protected function inboxRoute(string $action, mixed ...$params): string
    {
        return match ($action) {
            'index' => route('admin.whatsapp.inbox', $params[0] ?? []),
            'poll' => route('admin.whatsapp.inbox.poll', $params[0] ?? []),
            'templates' => route('admin.whatsapp.inbox.templates'),
            'suggested-templates' => route('admin.whatsapp.inbox.suggested-templates'),
            'start' => route('admin.whatsapp.inbox.start'),
            'conversation' => route('admin.whatsapp.inbox.conversation', is_array($params[0] ?? null) ? $params[0] : ['conversation' => $params[0]]),
            'reply' => route('admin.whatsapp.inbox.reply', $params[0]),
            'react' => route('admin.whatsapp.inbox.react', $params[0]),
            'media-send' => route('admin.whatsapp.inbox.media-send', $params[0]),
            'media' => route('admin.whatsapp.inbox.media', is_array($params[0] ?? null) ? $params[0] : [
                'conversation' => $params[0],
                'message' => $params[1] ?? 0,
            ]),
            'template' => route('admin.whatsapp.inbox.template', $params[0]),
            'status' => route('admin.whatsapp.inbox.status', $params[0]),
            'transfer' => route('admin.whatsapp.inbox.transfer', $params[0]),
            'assign' => route('admin.whatsapp.inbox.assign', $params[0]),
            'notes' => route('admin.whatsapp.inbox.notes', $params[0]),
            'tag' => route('admin.whatsapp.inbox.tag', $params[0]),
            'lead-stage' => route('admin.whatsapp.inbox.lead-stage', $params[0]),
            default => route('admin.whatsapp.inbox'),
        };
    }

    /** @return array<string, mixed> */
    protected function inboxBaseFilters(Request $request): array
    {
        return array_filter([
            'status' => $request->query('status'),
            'department' => $request->query('department'),
            'assigned_to' => $request->query('assigned_to'),
            'mine' => $request->boolean('mine'),
            'tag_id' => $request->query('tag_id'),
        ], fn ($v) => $v !== null && $v !== '');
    }

    public function index(Request $request): View
    {
        return $this->inboxIndex($request);
    }

    public function templates(): JsonResponse
    {
        return $this->inboxTemplates();
    }

    public function suggestedTemplates(Request $request): JsonResponse
    {
        return $this->inboxSuggestedTemplates($request);
    }

    public function showConversation(WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxShowConversation($conversation);
    }

    public function poll(Request $request): JsonResponse
    {
        return $this->inboxPoll($request);
    }

    public function reply(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxReply($request, $conversation);
    }

    public function react(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxReact($request, $conversation);
    }

    public function sendMedia(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxSendMedia($request, $conversation);
    }

    public function messageMedia(WhatsAppConversation $conversation, WhatsAppConversationMessage $message): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->inboxMessageMedia($conversation, $message);
    }

    public function sendTemplate(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxSendTemplate($request, $conversation);
    }

    public function start(Request $request): JsonResponse
    {
        return $this->inboxStart($request);
    }

    public function markRead(WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxMarkRead($conversation);
    }

    public function updateStatus(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxUpdateStatus($request, $conversation);
    }

    public function updateLeadStage(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxUpdateLeadStage($request, $conversation);
    }

    public function transfer(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxTransfer($request, $conversation);
    }

    public function assign(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxAssign($request, $conversation);
    }

    public function storeNote(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxStoreNote($request, $conversation);
    }

    public function syncTag(Request $request, WhatsAppConversation $conversation, WhatsAppTag $tag): JsonResponse
    {
        return $this->inboxSyncTag($request, $conversation, $tag);
    }
}
