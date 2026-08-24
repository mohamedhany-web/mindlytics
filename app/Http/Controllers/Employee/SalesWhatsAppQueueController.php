<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Services\SalesTeamService;
use App\Services\WhatsAppQueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesWhatsAppQueueController extends Controller
{
    public function __construct(
        private WhatsAppQueueService $queue,
        private SalesTeamService $teams,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $manager = $request->user();
        $team = $this->teams->teamFor($manager);
        $members = $this->teams->memberRecords($manager, $team);

        $conversations = $this->queue->pendingQuery()
            ->paginate(48)
            ->withQueryString();

        return view('employee.sales.whatsapp.queue', [
            'conversations' => $conversations,
            'queueEnabled' => $this->queue->queueEnabled(),
            'inboxUrl' => $this->queue->inboxIndexUrlFor($manager),
            'teamMembers' => $members,
            'hasTeam' => $members->isNotEmpty() || (bool) $team,
        ]);
    }

    public function count(): JsonResponse
    {
        return response()->json([
            'count' => $this->queue->pendingCount(),
        ]);
    }

    public function assign(Request $request, WhatsAppConversation $conversation): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
        ]);

        $manager = Auth::user();
        $assignee = User::query()->findOrFail((int) $data['assigned_to']);
        $lead = $this->queue->assignToSalesRep($conversation, $manager, $assignee);
        $conversation = $conversation->fresh();
        $inboxUrl = $this->queue->inboxUrlFor($manager, $conversation);

        $message = 'تم توزيع الطلب على «'.$assignee->name.'».';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'lead_id' => $lead->id,
                'assignee_id' => $assignee->id,
                'inbox_url' => $inboxUrl,
            ]);
        }

        return redirect()->route('employee.sales-manager.whatsapp.queue.index')
            ->with('success', $message.' يمكنك متابعة المحادثة من صندوق الفريق.');
    }
}
