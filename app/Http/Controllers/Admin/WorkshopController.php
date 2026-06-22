<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WorkshopAcceptanceMail;
use App\Models\SalesLead;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WhatsAppBatch;
use App\Models\WorkshopRegistration;
use App\Services\WhatsAppBatchService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WorkshopController extends Controller
{
    public function __construct(
        private WhatsAppBatchService $whatsappBatch
    ) {}

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

        $whatsappEligibleCount = (int) $workshop->registrations()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->count();

        $salesReps = User::salesEmployees()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $latestWhatsappBatch = WhatsAppBatch::where('source_type', 'workshop')
            ->where('source_id', $workshop->id)
            ->latest()
            ->first();

        return view('admin.workshops.show', compact(
            'workshop',
            'registrations',
            'filterMode',
            'emailPendingCount',
            'whatsappEligibleCount',
            'salesReps',
            'latestWhatsappBatch'
        ));
    }

    public function convertRegistrationsToLeads(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|integer|exists:users,id',
        ]);

        $assigneeId = (int) $validated['assigned_to'];

        if (! User::salesEmployees()->where('is_active', true)->whereKey($assigneeId)->exists()) {
            return back()->with('error', 'يرجى اختيار موظف مبيعات فعّال.');
        }

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($workshop, $assigneeId, &$created, &$skipped) {
            $workshop->registrations()->orderBy('id')->chunk(200, function ($chunk) use ($workshop, $assigneeId, &$created, &$skipped) {
                foreach ($chunk as $reg) {
                    $marker = '[workshop_registration:' . $reg->id . ']';

                    $alreadyConverted = SalesLead::query()
                        ->where('assigned_to', $assigneeId)
                        ->where('notes', 'like', '%' . $marker . '%')
                        ->exists();

                    if ($alreadyConverted) {
                        $skipped++;
                        continue;
                    }

                    $duplicateByContact = SalesLead::query()
                        ->where('assigned_to', $assigneeId)
                        ->where(function ($q) use ($reg) {
                            if (! empty($reg->phone)) {
                                $q->orWhere('phone', $reg->phone);
                            }
                            if (! empty($reg->email)) {
                                $q->orWhere('email', $reg->email);
                            }
                        })
                        ->exists();

                    if ($duplicateByContact) {
                        $skipped++;
                        continue;
                    }

                    $attendanceLabel = $reg->attendance_mode === 'offline'
                        ? 'أوفلاين'
                        : ($reg->attendance_mode === 'online' ? 'أونلاين' : 'غير محدد');

                    $notes = trim(
                        "تم التحويل تلقائياً من تسجيل ورشة.\n"
                        . "[workshop:{$workshop->id}] [workshop_registration:{$reg->id}]\n"
                        . "اسم الورشة: {$workshop->title}\n"
                        . "نوع الحضور: {$attendanceLabel}\n"
                        . "تاريخ التسجيل: " . optional($reg->created_at)->format('Y-m-d H:i')
                        . (! empty($reg->notes) ? "\nملاحظات التسجيل: {$reg->notes}" : '')
                    );

                    SalesLead::create([
                        'assigned_to' => $assigneeId,
                        'created_by' => auth()->id(),
                        'name' => $reg->name ?: 'عميل محتمل من ورشة',
                        'phone' => $reg->phone,
                        'email' => $reg->email,
                        'source' => 'event',
                        'stage' => 'new',
                        'priority' => 'normal',
                        'interest' => 'الاهتمام بورشة: ' . $workshop->title,
                        'notes' => $notes,
                    ]);

                    $created++;
                }
            });
        });

        return back()->with('success', "تم تحويل {$created} تسجيل إلى Leads، وتم تخطي {$skipped} سجل (مكرر أو محوّل مسبقاً).");
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

    public function deactivate(Workshop $workshop)
    {
        $workshop->update(['is_active' => false]);

        return back()->with('success', 'تم إيقاف الورشة. الرابط العام يعرض الآن أن الورشة انتهت ولا يقبل تسجيلات جديدة.');
    }

    public function activate(Workshop $workshop)
    {
        $workshop->update(['is_active' => true]);

        return back()->with('success', 'تم تفعيل الورشة مرة أخرى وعاد رابط الحجز ليعمل.');
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
     * إرسال رسائل واتساب للمسجلين عبر Bridge (تحديد يدوي أو كل المسجلين).
     */
    public function sendWhatsappMessages(Request $request, Workshop $workshop)
    {
        $data = $request->validate([
            'message' => 'required|string|max:4096',
            'select_all' => 'nullable|boolean',
            'registration_ids' => 'nullable|array',
            'registration_ids.*' => 'integer|exists:workshop_registrations,id',
            'attendance_mode' => 'nullable|in:all,online,offline',
        ], [
            'message.required' => 'نص الرسالة مطلوب',
        ]);

        if ($request->boolean('select_all')) {
            $query = $workshop->registrations()
                ->whereNotNull('phone')
                ->where('phone', '!=', '');

            $attendanceMode = $data['attendance_mode'] ?? 'all';
            if (in_array($attendanceMode, ['online', 'offline'], true)) {
                $query->where('attendance_mode', $attendanceMode);
            }

            $registrations = $query->get();
        } else {
            $ids = array_filter($data['registration_ids'] ?? []);
            if ($ids === []) {
                return back()->with('error', 'يرجى تحديد مشترك واحد على الأقل من الجدول.');
            }

            $registrations = $workshop->registrations()
                ->whereIn('id', $ids)
                ->whereNotNull('phone')
                ->where('phone', '!=', '')
                ->get();
        }

        if ($registrations->isEmpty()) {
            return back()->with('error', 'لا توجد تسجيلات بأرقام واتساب صالحة للإرسال.');
        }

        $items = $registrations->shuffle()->map(function (WorkshopRegistration $reg) use ($data, $workshop) {
            return [
                'recipient_name' => $reg->name,
                'phone' => $reg->phone,
                'message' => $this->renderWorkshopWhatsappMessage($data['message'], $reg, $workshop),
                'message_type' => 'workshop',
                'workshop_registration_id' => $reg->id,
            ];
        });

        $batch = $this->whatsappBatch->createAndDispatch(
            sourceType: 'workshop',
            sourceId: $workshop->id,
            title: 'ورشة: ' . $workshop->title,
            messageTemplate: $data['message'],
            items: $items,
            createdBy: (int) auth()->id(),
            meta: [
                'workshop_id' => $workshop->id,
                'select_all' => $request->boolean('select_all'),
                'attendance_mode' => $data['attendance_mode'] ?? 'all',
            ]
        );

        return redirect()
            ->route('admin.whatsapp.batches.show', $batch)
            ->with('success', 'تم بدء إرسال ' . $items->count() . ' رسالة في الخلفية — تابع التقدم أدناه.');
    }

    private function renderWorkshopWhatsappMessage(string $template, WorkshopRegistration $reg, Workshop $workshop): string
    {
        $attendance = $reg->attendance_mode === 'offline'
            ? 'حضور في المقر'
            : ($reg->attendance_mode === 'online' ? 'حضور أونلاين' : '');

        $replacements = [
            '{name}' => $reg->name,
            '{{name}}' => $reg->name,
            '{student_name}' => $reg->name,
            '{{student_name}}' => $reg->name,
            '{email}' => $reg->email ?? '',
            '{{email}}' => $reg->email ?? '',
            '{phone}' => $reg->phone ?? '',
            '{{phone}}' => $reg->phone ?? '',
            '{workshop_title}' => $workshop->title,
            '{{workshop_title}}' => $workshop->title,
            '{workshop_date}' => optional($workshop->starts_at)->format('Y-m-d H:i') ?? '',
            '{{workshop_date}}' => optional($workshop->starts_at)->format('Y-m-d H:i') ?? '',
            '{attendance_mode}' => $attendance,
            '{{attendance_mode}}' => $attendance,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
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

