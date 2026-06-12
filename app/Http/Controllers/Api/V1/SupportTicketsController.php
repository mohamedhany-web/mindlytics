<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportTicketsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = min(max((int) $request->input('per_page', 20), 1), 50);

        $q = SupportTicket::query()
            ->where('user_id', $user->id)
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        $p = $q->paginate($perPage)->withQueryString();

        $items = $p->getCollection()->map(fn (SupportTicket $t) => [
            'id' => $t->id,
            'subject' => $t->subject,
            'status' => $t->status,
            'last_message_at' => $t->last_message_at?->toIso8601String(),
            'created_at' => $t->created_at?->toIso8601String(),
        ])->values();

        return response()->json([
            'tickets' => $items,
            'pagination' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'subject' => ['required', 'string', 'min:3', 'max:255'],
            'body' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'role' => (string) ($user->role ?? ''),
            'subject' => trim($data['subject']),
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'body' => trim($data['body']),
        ]);

        return response()->json(['ticket_id' => $ticket->id], 201);
    }

    public function show(Request $request, SupportTicket $ticket): JsonResponse
    {
        $user = $request->user();
        if ($ticket->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $ticket->load(['messages.sender:id,name,role,profile_image']);

        return response()->json([
            'ticket' => [
                'id' => $ticket->id,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at?->toIso8601String(),
            ],
            'messages' => $ticket->messages->map(fn (SupportTicketMessage $m) => [
                'id' => $m->id,
                'body' => $m->body,
                'created_at' => $m->created_at?->toIso8601String(),
                'sender' => [
                    'id' => $m->sender?->id,
                    'name' => $m->sender?->name,
                    'role' => $m->sender?->role,
                    'profile_image_url' => $m->sender?->profile_image_url,
                ],
            ])->values(),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket): JsonResponse
    {
        $user = $request->user();
        if ($ticket->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }
        if ($ticket->status !== 'open') {
            return response()->json(['message' => 'التذكرة مغلقة'], 422);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        $msg = SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'body' => trim($data['body']),
        ]);

        $ticket->update(['last_message_at' => now()]);

        return response()->json(['message_id' => $msg->id], 201);
    }
}

