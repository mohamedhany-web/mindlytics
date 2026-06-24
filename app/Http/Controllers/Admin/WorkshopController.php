<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WorkshopAcceptanceMail;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
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
        $leadFilter = $request->get('lead_status', 'all');

        $registrationsQuery = $workshop->registrations()
            ->with(['salesLead.assignee:id,name'])
            ->latest();

        if (in_array($filterMode, ['online', 'offline'], true)) {
            $registrationsQuery->where('attendance_mode', $filterMode);
        }

        if ($leadFilter === 'pending' && Schema::hasColumn('workshop_registrations', 'converted_to_lead_at')) {
            $registrationsQuery->whereNull('converted_to_lead_at');
        } elseif ($leadFilter === 'converted' && Schema::hasColumn('workshop_registrations', 'converted_to_lead_at')) {
            $registrationsQuery->whereNotNull('converted_to_lead_at');
        }

        $registrations = $registrationsQuery
            ->paginate(25)
            ->appends([
                'attendance_mode' => $filterMode,
                'lead_status' => $leadFilter,
            ]);

        $stats = [
            'total' => (int) $workshop->registrations()->count(),
            'converted' => Schema::hasColumn('workshop_registrations', 'converted_to_lead_at')
                ? (int) $workshop->registrations()->whereNotNull('converted_to_lead_at')->count()
                : 0,
            'checked_in' => (int) $workshop->registrations()->whereNotNull('checked_in_at')->count(),
            'email_pending' => (int) $workshop->registrations()
                ->whereNotNull('email')
                ->whereNull('acceptance_email_sent_at')
                ->count(),
        ];
        $stats['pending_leads'] = max(0, $stats['total'] - $stats['converted']);

        $emailPendingCount = $stats['email_pending'];

        $salesReps = User::salesEmployees()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $salesLeadGroups = SalesLeadGroup::query()
            ->with('members:id')
            ->orderBy('name')
            ->get(['id', 'name', 'assigned_to', 'is_admin_managed'])
            ->map(fn (SalesLeadGroup $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'is_admin_managed' => (bool) $group->is_admin_managed,
                'member_ids' => $group->memberIds()->map(fn ($id) => (int) $id)->values()->all(),
            ])
            ->values()
            ->all();

        return view('admin.workshops.show', compact(
            'workshop',
            'registrations',
            'filterMode',
            'leadFilter',
            'stats',
            'emailPendingCount',
            'salesReps',
            'salesLeadGroups'
        ));
    }

    public function convertRegistrationsToLeads(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'integer|exists:users,id',
            'sales_lead_group_id' => 'nullable|integer',
        ]);

        $assigneeIds = collect($validated['assigned_to'])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $validCount = User::salesEmployees()
            ->where('is_active', true)
            ->whereIn('id', $assigneeIds)
            ->count();

        if ($validCount !== count($assigneeIds)) {
            return back()->with('error', 'يرجى اختيار موظفي مبيعات فعّالين فقط.');
        }

        $groupId = $this->resolveConvertLeadGroupId($validated['sales_lead_group_id'] ?? null, $assigneeIds);
        if ($groupId === false) {
            return back()->with('error', 'مجموعة العملاء المختارة لا تشمل كل موظفي المبيعات المحددين.');
        }

        $created = 0;
        $skipped = 0;
        $skippedAlready = 0;
        $skippedDuplicate = 0;
        $createdByRep = array_fill_keys($assigneeIds, 0);
        $assigneeCount = count($assigneeIds);
        $hasConversionColumn = Schema::hasColumn('workshop_registrations', 'converted_to_lead_at');

        DB::transaction(function () use ($workshop, $assigneeIds, $assigneeCount, $groupId, $hasConversionColumn, &$created, &$skipped, &$skippedAlready, &$skippedDuplicate, &$createdByRep) {
            $registrations = $workshop->registrations()->orderBy('id')->get();
            $eligible = [];

            foreach ($registrations as $reg) {
                if ($this->registrationAlreadyConverted($reg, $hasConversionColumn)) {
                    $skipped++;
                    $skippedAlready++;

                    continue;
                }

                $eligible[] = $reg;
            }

            foreach ($eligible as $index => $reg) {
                $assigneeId = $assigneeIds[$index % $assigneeCount];

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
                    $skippedDuplicate++;

                    continue;
                }

                $attendanceLabel = $reg->attendance_mode === 'offline'
                    ? 'أوفلاين'
                    : ($reg->attendance_mode === 'online' ? 'أونلاين' : 'غير محدد');

                $notes = trim(
                    "تم التحويل تلقائياً من تسجيل ورشة.\n"
                    ."[workshop:{$workshop->id}] [workshop_registration:{$reg->id}]\n"
                    ."اسم الورشة: {$workshop->title}\n"
                    ."نوع الحضور: {$attendanceLabel}\n"
                    .'تاريخ التسجيل: '.optional($reg->created_at)->format('Y-m-d H:i')
                    .(! empty($reg->notes) ? "\nملاحظات التسجيل: {$reg->notes}" : '')
                );

                $lead = SalesLead::create([
                    'assigned_to' => $assigneeId,
                    'created_by' => auth()->id(),
                    'sales_lead_group_id' => $groupId,
                    'name' => $reg->name ?: 'عميل محتمل من ورشة',
                    'phone' => $reg->phone,
                    'email' => $reg->email,
                    'source' => 'event',
                    'stage' => 'new',
                    'priority' => 'normal',
                    'interest' => 'الاهتمام بورشة: '.$workshop->title,
                    'notes' => $notes,
                ]);

                if ($hasConversionColumn) {
                    $reg->update([
                        'converted_to_lead_at' => now(),
                        'sales_lead_id' => $lead->id,
                    ]);
                }

                $created++;
                $createdByRep[$assigneeId]++;
            }
        });

        $distribution = collect($createdByRep)
            ->filter(fn ($count) => $count > 0)
            ->map(function ($count, $userId) {
                $name = User::query()->whereKey($userId)->value('name') ?? '#'.$userId;

                return $name.': '.$count;
            })
            ->implode(' · ');

        if ($created === 0) {
            $message = 'لا يوجد مسجّلون جدد للترحيل.';
            if ($skippedAlready > 0) {
                $message .= " ({$skippedAlready} مُرحَّل مسبقاً)";
            }
            if ($skippedDuplicate > 0) {
                $message .= " ({$skippedDuplicate} مكرر عند موظف)";
            }

            return back()->with('error', $message);
        }

        $message = "تم ترحيل {$created} مسجّل جديد فقط إلى Leads.";
        if ($skippedAlready > 0) {
            $message .= " تخطّي {$skippedAlready} مُرحَّل سابقاً.";
        }
        if ($skippedDuplicate > 0) {
            $message .= " تخطّي {$skippedDuplicate} مكرر.";
        }
        if ($distribution !== '') {
            $message .= ' التوزيع: '.$distribution.'.';
        }

        return back()->with('success', $message);
    }

    private function registrationAlreadyConverted(WorkshopRegistration $registration, bool $hasConversionColumn): bool
    {
        if ($hasConversionColumn && $registration->converted_to_lead_at) {
            return true;
        }

        return SalesLead::query()
            ->where('notes', 'like', '%[workshop_registration:'.$registration->id.']%')
            ->exists();
    }

    /**
     * @param  list<int>  $assigneeIds
     * @return int|null|false  null = بدون مجموعة، false = مجموعة غير صالحة
     */
    private function resolveConvertLeadGroupId(mixed $groupId, array $assigneeIds): int|null|false
    {
        if ($groupId === null || $groupId === '') {
            return null;
        }

        if (! Schema::hasTable('sales_lead_groups') || ! Schema::hasColumn('sales_leads', 'sales_lead_group_id')) {
            return null;
        }

        $group = SalesLeadGroup::query()
            ->with('members:id')
            ->find((int) $groupId);

        if (! $group || ! $group->includesAllMembers($assigneeIds)) {
            return false;
        }

        return $group->id;
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

