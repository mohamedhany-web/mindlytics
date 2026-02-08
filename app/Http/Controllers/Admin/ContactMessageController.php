<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // إحصائيات
        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::whereNull('read_at')->count(),
            'read' => ContactMessage::whereNotNull('read_at')->count(),
            'today' => ContactMessage::whereDate('created_at', today())->count(),
        ];

        return view('admin.contact-messages.index', compact('messages', 'stats'));
    }

    public function show(ContactMessage $contactMessage)
    {
        if (!$contactMessage->read_at) {
            $contactMessage->update(['read_at' => now()]);
        }

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }

    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'تم تحديد الرسالة كمقروءة');
    }

    public function markAsUnread(ContactMessage $contactMessage)
    {
        $contactMessage->update(['read_at' => null]);

        return redirect()->back()->with('success', 'تم تحديد الرسالة كغير مقروءة');
    }
}



