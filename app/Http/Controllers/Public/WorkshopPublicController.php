<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkshopPublicController extends Controller
{
    public function show(string $slug)
    {
        $workshop = Workshop::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $remaining = $workshop->remaining_seats;

        return view('public.workshop-register', compact('workshop', 'remaining'));
    }

    public function register(Request $request, string $slug)
    {
        $workshop = Workshop::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $remaining = $workshop->remaining_seats;
        if ($remaining !== null && $remaining <= 0) {
            return back()->with('error', 'تم اكتمال عدد المقاعد في هذه الورشة.')->withInput();
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ];

        // إذا كانت الورشة تتيح اختيار طريقة الحضور، نتحقق من الحقل من الطالب
        if ($workshop->mode === 'both') {
            $rules['attendance_mode'] = 'required|in:online,offline';
        }

        $data = $request->validate($rules);

        // في حالة ورشة أونلاين فقط أو أوفلاين فقط، نضبط القيمة تلقائياً
        if ($workshop->mode === 'online') {
            $data['attendance_mode'] = 'online';
        } elseif ($workshop->mode === 'offline') {
            $data['attendance_mode'] = 'offline';
        }

        $data['workshop_id'] = $workshop->id;

        if (empty($data['checkin_token'] ?? null)) {
            $data['checkin_token'] = (string) Str::uuid();
        }

        WorkshopRegistration::create($data);

        return back()->with('success', 'تم استلام طلب التسجيل في الورشة بنجاح. سنقوم بالتواصل معك في حال وجود أي تفاصيل إضافية.');
    }
}

