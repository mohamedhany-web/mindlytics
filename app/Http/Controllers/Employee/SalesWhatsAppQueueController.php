<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppConversation;
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
    ) {
        $this->middleware('sales.staff');
    }

    public function index(Request $request): View
    {
        $conversations = $this->queue->pendingQuery()
            ->paginate(20)
            ->withQueryString();

        return view('employee.sales.whatsapp.queue', [
            'conversations' => $conversations,
            'queueEnabled' => $this->queue->queueEnabled(),
        ]);
    }

    public function count(): JsonResponse
    {
        return response()->json([
            'count' => $this->queue->pendingCount(),
        ]);
    }

    public function accept(WhatsAppConversation $conversation): RedirectResponse|JsonResponse
    {
        $user = Auth::user();
        $lead = $this->queue->claim($conversation, $user);
        $conversation = $conversation->fresh();
        $inboxUrl = $this->queue->inboxUrlFor($user, $conversation);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم قبول الطلب بنجاح.',
                'lead_id' => $lead->id,
                'inbox_url' => $inboxUrl,
            ]);
        }

        return redirect()->to($inboxUrl)
            ->with('success', 'تم قبول الطلب — يمكنك التواصل مع «'.$lead->name.'» الآن.');
    }
}
