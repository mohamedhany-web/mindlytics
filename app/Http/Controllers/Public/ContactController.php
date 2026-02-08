<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $message = ContactMessage::create($validated);

        // TODO: إرسال إيميل للمسؤول
        
        return redirect()->route('public.contact')
            ->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً!');
    }
}
