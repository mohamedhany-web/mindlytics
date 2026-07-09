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

class SalesManagerWhatsAppInboxController extends Controller
{
    use HandlesWhatsAppInbox;

    public function __construct()
    {
        $this->middleware('sales.manager');
    }

    protected function inboxView(): string
    {
        return 'admin.whatsapp.inbox';
    }

    /** @return array<string, mixed> */
    protected function inboxExtraViewData(): array
    {
        return [
            'waLayout' => 'layouts.employee',
            'waPageTitle' => 'محادثات فريق المبيعات - Mindlytics',
            'waPageHeader' => 'واتساب الفريق',
            'waInboxTitle' => 'محادثات الفريق',
            'waInboxSubtitle' => 'راقب وتابع محادثات جميع أعضاء فريقك.',
            'waHideWebhookBanner' => true,
            'waHideAdminFilters' => false,
            'waTeamInbox' => true,
            'waImmersiveInbox' => true,
            'waEmployeeLeadsUrl' => route('employee.sales-manager.leads.index'),
            'waEmployeeSalesUrl' => route('employee.sales-manager.dashboard'),
            'waBtnPrimary' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition-colors shadow-sm',
            'waBtnSecondary' => 'inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors',
        ];
    }

    protected function inboxAudience(): string
    {
        return 'employee';
    }

    protected function inboxRoute(string $action, mixed ...$params): string
    {
        return match ($action) {
            'index' => route('employee.sales-manager.whatsapp.inbox.index', $params[0] ?? []),
            'poll' => route('employee.sales-manager.whatsapp.inbox.poll', $params[0] ?? []),
            'templates' => route('employee.sales-manager.whatsapp.inbox.templates'),
            'suggested-templates' => route('employee.sales-manager.whatsapp.inbox.suggested-templates'),
            'start' => route('employee.sales-manager.whatsapp.inbox.start'),
            'conversation' => route('employee.sales-manager.whatsapp.inbox.conversation', is_array($params[0] ?? null) ? $params[0] : ['conversation' => $params[0]]),
            'reply' => route('employee.sales-manager.whatsapp.inbox.reply', $params[0]),
            'react' => route('employee.sales-manager.whatsapp.inbox.react', $params[0]),
            'media-send' => route('employee.sales-manager.whatsapp.inbox.media-send', $params[0]),
            'media' => route('employee.sales-manager.whatsapp.inbox.media', is_array($params[0] ?? null) ? $params[0] : [
                'conversation' => $params[0],
                'message' => $params[1] ?? 0,
            ]),
            'template' => route('employee.sales-manager.whatsapp.inbox.template', $params[0]),
            'status' => route('employee.sales-manager.whatsapp.inbox.status', $params[0]),
            'notes' => route('employee.sales-manager.whatsapp.inbox.notes', $params[0]),
            'tag' => route('employee.sales-manager.whatsapp.inbox.tag', $params[0]),
            'lead-stage' => route('employee.sales-manager.whatsapp.inbox.lead-stage', $params[0]),
            default => route('employee.sales-manager.whatsapp.inbox.index'),
        };
    }

    /** @return array<string, mixed> */
    protected function inboxBaseFilters(Request $request): array
    {
        return array_filter([
            'status' => $request->query('status'),
            'tag_id' => $request->query('tag_id'),
            'assigned_to' => $request->query('assigned_to'),
            'mine' => $request->boolean('mine') ? '1' : null,
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

    public function storeNote(Request $request, WhatsAppConversation $conversation): JsonResponse
    {
        return $this->inboxStoreNote($request, $conversation);
    }

    public function syncTag(Request $request, WhatsAppConversation $conversation, WhatsAppTag $tag): JsonResponse
    {
        return $this->inboxSyncTag($request, $conversation, $tag);
    }
}
