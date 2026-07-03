<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Concerns\HandlesWhatsAppInbox;
use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppConversationMessage;
use App\Models\WhatsAppTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesWhatsAppInboxController extends Controller
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
            'waLayout' => 'layouts.employee',
            'waPageTitle' => 'محادثات الواتساب - Mindlytics',
            'waPageHeader' => 'واتساب المبيعات',
            'waInboxTitle' => 'محادثاتي',
            'waInboxSubtitle' => 'ردّ على عملائك وتابع مراحل الـ Pipeline من نفس الشاشة.',
            'waHideWebhookBanner' => true,
            'waHideAdminFilters' => true,
        ];
    }

    protected function inboxAudience(): string
    {
        return 'employee';
    }

    protected function inboxRoute(string $action, mixed ...$params): string
    {
        return match ($action) {
            'index' => route('employee.sales.whatsapp.inbox.index', $params[0] ?? []),
            'poll' => route('employee.sales.whatsapp.inbox.poll', $params[0] ?? []),
            'templates' => route('employee.sales.whatsapp.inbox.templates'),
            'start' => route('employee.sales.whatsapp.inbox.start'),
            'conversation' => route('employee.sales.whatsapp.inbox.conversation', is_array($params[0] ?? null) ? $params[0] : ['conversation' => $params[0]]),
            'reply' => route('employee.sales.whatsapp.inbox.reply', $params[0]),
            'react' => route('employee.sales.whatsapp.inbox.react', $params[0]),
            'media-send' => route('employee.sales.whatsapp.inbox.media-send', $params[0]),
            'media' => route('employee.sales.whatsapp.inbox.media', is_array($params[0] ?? null) ? $params[0] : [
                'conversation' => $params[0],
                'message' => $params[1] ?? 0,
            ]),
            'template' => route('employee.sales.whatsapp.inbox.template', $params[0]),
            'status' => route('employee.sales.whatsapp.inbox.status', $params[0]),
            'notes' => route('employee.sales.whatsapp.inbox.notes', $params[0]),
            'tag' => route('employee.sales.whatsapp.inbox.tag', $params[0]),
            'lead-stage' => route('employee.sales.whatsapp.inbox.lead-stage', $params[0]),
            default => route('employee.sales.whatsapp.inbox.index'),
        };
    }

    /** @return array<string, mixed> */
    protected function inboxBaseFilters(Request $request): array
    {
        return array_filter([
            'status' => $request->query('status'),
            'tag_id' => $request->query('tag_id'),
            'mine' => true,
            'assigned_to' => (string) auth()->id(),
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

    public function storeNote(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxStoreNote($request, $conversation);
    }

    public function syncTag(Request $request, WhatsAppConversation $conversation, WhatsAppTag $tag): JsonResponse
    {
        return $this->inboxSyncTag($request, $conversation, $tag);
    }
}
