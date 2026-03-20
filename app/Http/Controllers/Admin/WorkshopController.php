<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WorkshopAcceptanceMail;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::query()
            ->orderByDesc('starts_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.workshops.index', compact('workshops'));
    }

    public function create()
    {
        return view('admin.workshops.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'mode' => 'required|in:online,offline,both',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_seats' => 'nullable|integer|min:0',
            'seats_online' => 'nullable|integer|min:0',
            'seats_offline' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active', true);

        $workshop = Workshop::create($data);

        return redirect()->route('admin.workshops.show', $workshop)
            ->with('success', 'تم إنشاء الورشة بنجاح.');
    }

    public function show(Request $request, Workshop $workshop)
    {
        $filterMode = $request->get('attendance_mode', 'all');

        $registrationsQuery = $workshop->registrations()->latest();

        if (in_array($filterMode, ['online', 'offline'], true)) {
            $registrationsQuery->where('attendance_mode', $filterMode);
        }

        $registrations = $registrationsQuery->paginate(25)->appends(['attendance_mode' => $filterMode]);

        $emailPendingCount = (int) $workshop->registrations()
            ->whereNotNull('email')
            ->whereNull('acceptance_email_sent_at')
            ->count();

        return view('admin.workshops.show', compact('workshop', 'registrations', 'filterMode', 'emailPendingCount'));
    }

    /**
     * تأكيد الحضور عبر مسح QR (checkin_token).
     */
    public function checkin(Request $request, Workshop $workshop)
    {
        $data = $request->validate([
            'token' => 'required|string',
        ]);

        $registration = WorkshopRegistration::where('workshop_id', $workshop->id)
            ->where('checkin_token', $data['token'])
            ->first();

        if (!$registration) {
            return response()->json([
                'status' => 'error',
                'message' => 'الرمز غير صالح أو لا يخص هذه الورشة.',
            ], 404);
        }

        if ($registration->checked_in_at) {
            return response()->json([
                'status' => 'already',
                'message' => 'تم تسجيل حضور هذا المتدرب من قبل في ' . $registration->checked_in_at->format('Y-m-d H:i'),
                'name' => $registration->name,
            ]);
        }

        $registration->checked_in_at = now();
        $registration->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل حضور ' . ($registration->name ?? 'المتدرب') . ' بنجاح.',
            'name' => $registration->name,
        ]);
    }

    public function edit(Workshop $workshop)
    {
        return view('admin.workshops.edit', compact('workshop'));
    }

    public function update(Request $request, Workshop $workshop)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'mode' => 'required|in:online,offline,both',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'max_seats' => 'nullable|integer|min:0',
            'seats_online' => 'nullable|integer|min:0',
            'seats_offline' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', $workshop->is_active);

        $workshop->update($data);

        return redirect()->route('admin.workshops.show', $workshop)
            ->with('success', 'تم تحديث بيانات الورشة بنجاح.');
    }

    public function destroy(Workshop $workshop)
    {
        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', 'تم حذف الورشة وجميع الحجوزات المرتبطة بها.');
    }

    /**
     * تصدير بيانات المسجلين في ورشة إلى ملف Excel (تصميم جدولي منسّق).
     */
    public function exportRegistrations(Workshop $workshop): StreamedResponse
    {
        $fileName = 'workshop_' . $workshop->id . '_registrations_' . now()->format('Ymd_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($workshop) {
            echo chr(0xEF) . chr(0xBB) . chr(0xBF); // UTF-8 BOM

            $title = 'تسجيلات الورشة: ' . $workshop->title;

            echo '<html><head><meta charset="utf-8"><style>
                body { font-family: Arial, Helvetica, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #cccccc; padding: 6px 8px; font-size: 12px; }
                th { background-color: #0f172a; color: #ffffff; text-align: center; }
                tr:nth-child(even) td { background-color: #f9fafb; }
                tr:hover td { background-color: #e5f1fb; }
                caption { font-weight: bold; margin-bottom: 8px; font-size: 14px; }
            </style></head><body>';

            echo '<table>';
            echo '<caption>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</caption>';
            echo '<thead><tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>طريقة الحضور</th>
                    <th>الملاحظات</th>
                    <th>الحالة</th>
                    <th>تاريخ التسجيل</th>
                  </tr></thead><tbody>';

            WorkshopRegistration::where('workshop_id', $workshop->id)
                ->orderBy('created_at')
                ->chunk(200, function ($chunk) {
                    foreach ($chunk as $reg) {
                        echo '<tr>';
                        echo '<td>' . (int) $reg->id . '</td>';
                        echo '<td>' . htmlspecialchars($reg->name ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($reg->email ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($reg->phone ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        $mode = $reg->attendance_mode === 'offline'
                            ? 'أوفلاين'
                            : ($reg->attendance_mode === 'online' ? 'أونلاين' : '');
                        echo '<td>' . htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($reg->notes ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars($reg->status ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '<td>' . htmlspecialchars(optional($reg->created_at)->format('Y-m-d H:i') ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        echo '</tr>';
                    }
                });

            echo '</tbody></table></body></html>';
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * إرسال نموذج قبول الورشة عبر الإيميل (لكل المسجلين أو لإيميل محدد).
     */
    public function sendAcceptanceEmails(Request $request, Workshop $workshop)
    {
        $data = $request->validate([
            'scope' => 'required|in:all,email',
            'email' => 'nullable|email',
        ]);

        $query = WorkshopRegistration::where('workshop_id', $workshop->id);

        if ($data['scope'] === 'email') {
            if (empty($data['email'])) {
                return back()->with('error', 'يرجى إدخال البريد الإلكتروني عند اختيار الإرسال لبريد محدد.');
            }
            $query->where('email', $data['email']);
        } else {
            // عند الإرسال الجماعي: لا نعيد إرسال الإيميل لمن تم إرسال نموذج القبول لهم سابقاً
            $query->whereNotNull('email')->whereNull('acceptance_email_sent_at');
        }

        $registrations = $query->get();
        $count = 0;

        foreach ($registrations as $reg) {
            if (empty($reg->checkin_token)) {
                $reg->checkin_token = (string) Str::uuid();
            }
            if (!$reg->email) {
                continue;
            }
            // نستخدم send() مباشرة للتأكد من الإرسال فوراً (بدون الاعتماد على الـ queue worker)
            Mail::to($reg->email)->send(new WorkshopAcceptanceMail($workshop, $reg));
            $reg->acceptance_email_sent_at = now();
            $reg->save();
            $count++;
        }

        if ($count === 0) {
            return back()->with('error', 'لا توجد تسجيلات متوافقة مع المعايير لإرسال الإيميل.');
        }

        return back()->with('success', 'تم إرسال نموذج قبول الورشة إلى ' . $count . ' مشترك/مشتركة.');
    }

    /**
     * إنشاء روابط واتساب جماعية/فردية برسالة جاهزة.
     */
    public function sendWhatsappMessages(Request $request, Workshop $workshop)
    {
        $data = $request->validate([
            'scope' => 'required|in:all,phone',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
        ]);

        $numbers = [];
        $targetRegistrations = collect();

        if ($data['scope'] === 'phone') {
            if (empty($data['phone'])) {
                return back()->with('error', 'يرجى إدخال رقم الهاتف عند اختيار الإرسال لرقم محدد.');
            }
            $normalizedPhone = $this->normalizePhone($data['phone']);
            $numbers[] = $normalizedPhone;

            $targetRegistrations = $workshop->registrations()
                ->whereNotNull('phone')
                ->get()
                ->filter(fn ($reg) => $this->normalizePhone($reg->phone) === $normalizedPhone)
                ->values();
        } else {
            $targetRegistrations = $workshop->registrations()
                ->whereNotNull('phone')
                ->get();

            $numbers = $targetRegistrations
                ->pluck('phone')
                ->map(fn ($phone) => $this->normalizePhone($phone))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $numbers = array_values(array_filter($numbers));

        if (count($numbers) === 0) {
            return back()->with('error', 'لا توجد أرقام واتساب متاحة للإرسال.');
        }

        if ($targetRegistrations->isNotEmpty()) {
            $now = now();
            WorkshopRegistration::whereIn('id', $targetRegistrations->pluck('id')->all())
                ->update(['whatsapp_link_sent_at' => $now]);
        }

        $links = collect($numbers)->map(function ($phone) use ($data) {
            return [
                'phone' => $phone,
                'url' => 'https://web.whatsapp.com/send/?phone=' . $phone . '&text=' . urlencode($data['message']) . '&type=phone_number&app_absent=0',
            ];
        })->all();

        return view('admin.workshops.whatsapp-launch', [
            'workshop' => $workshop,
            'links' => $links,
            'message' => $data['message'],
        ]);
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);

        // تحويل رقم مصري محلي 01xxxxxxxxx إلى 201xxxxxxxxx
        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            $clean = '2' . $clean;
        }

        return $clean ?: null;
    }
}

