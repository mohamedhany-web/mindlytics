<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketsController extends Controller
{
    public function index(Request $request): View
    {
        $q = SupportTicket::query()->with(['user:id,name,email,role']);

        if ($status = trim((string) $request->input('status'))) {
            if (in_array($status, ['open', 'closed'], true)) {
                $q->where('status', $status);
            }
        }
        if ($search = trim((string) $request->input('q'))) {
            $q->where(function ($w) use ($search) {
                $w->where('subject', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        $tickets = $q->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.support-tickets.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['user:id,name,email,role', 'messages.sender:id,name,role']);
        return view('admin.support-tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:5000'],
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);

        $ticket->update(['last_message_at' => now()]);

        return redirect()->route('admin.support-tickets.show', $ticket)->with('success', 'تم إرسال الرد.');
    }

    public function close(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'closed']);
        return redirect()->route('admin.support-tickets.show', $ticket)->with('success', 'تم إغلاق التذكرة.');
    }
}

