<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WorkshopAcceptanceMail;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use App\Services\SalesNotificationService;
use App\Services\WorkshopWhatsAppBatchService;
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
            'whatsapp_group_link' => 'nullable|url|max:500',
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
        $data['whatsapp_group_link'] = $this->normalizeWorkshopGroupLink($data['whatsapp_group_link'] ?? null);

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

        $waBulkService = app(WorkshopWhatsAppBatchService::class);
        $whatsappPhoneCountAll = $waBulkService->countDistinctPhones($workshop, 'all');
        $whatsappPhoneCountOnline = $waBulkService->countDistinctPhones($workshop, 'online');
        $whatsappPhoneCountOffline = $waBulkService->countDistinctPhones($workshop, 'offline');
        $latestWhatsAppBatch = $waBulkService->latestForWorkshop((int) $workshop->id);
        $workshopWhatsAppBatches = app(\App\Services\WhatsAppBatchService::class)->batchesForWorkshop((int) $workshop->id, 8);

        $welcomeTemplate = null;
        $defaultWelcomeBody = '';
        $approvedWhatsAppTemplates = collect();
        if (Schema::hasColumn('workshops', 'welcome_meta_template_id')) {
            $workshop->load('welcomeMetaTemplate');
            $welcomeTemplate = $workshop->welcomeMetaTemplate;
            $defaultWelcomeBody = app(\App\Services\WorkshopWhatsAppTemplateService::class)->defaultWelcomeBody();
        }
        if (Schema::hasTable('whatsapp_meta_templates')) {
            $approvedWhatsAppTemplates = \App\Models\WhatsAppMetaTemplate::query()
                ->where('status', \App\Models\WhatsAppMetaTemplate::STATUS_APPROVED)
                ->orderBy('name')
                ->get(['id', 'name', 'language', 'body_text', 'body_variable_count']);
        }

        $workshopTemplateService = app(\App\Services\WorkshopWhatsAppTemplateService::class);
        $workshopGroupInviteCode = $workshopTemplateService->resolveGroupInviteCode($workshop);
        $whatsappTemplatesSendMeta = $approvedWhatsAppTemplates->map(fn ($tpl) => [
            'name' => $tpl->name,
            'language' => $tpl->language,
            'needs_invite_code' => $workshopTemplateService->templateNeedsGroupInviteCode($tpl),
            'invite_var_index' => $workshopTemplateService->groupInviteVariableIndex($tpl),
        ])->values()->all();

        return view('admin.workshops.show', compact(
            'workshop',
            'registrations',
            'filterMode',
            'leadFilter',
            'stats',
            'emailPendingCount',
            'salesReps',
            'salesLeadGroups',
            'whatsappPhoneCountAll',
            'whatsappPhoneCountOnline',
            'whatsappPhoneCountOffline',
            'latestWhatsAppBatch',
            'workshopWhatsAppBatches',
            'welcomeTemplate',
            'defaultWelcomeBody',
            'approvedWhatsAppTemplates',
            'workshopGroupInviteCode',
            'whatsappTemplatesSendMeta',
        ));
    }

    public function confirmations(Workshop $workshop)
    {
        $confirmedAttendees = $workshop->registrations()
            ->whereNotNull('checked_in_at')
            ->orderByDesc('checked_in_at')
            ->get();

        $confirmUrl = route('public.workshops.confirm.show', $workshop->slug);

        return view('admin.workshops.confirmations', compact(
            'workshop',
            'confirmedAttendees',
            'confirmUrl'
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
        $linkedExisting = 0;
        $skippedAlready = 0;
        $createdByRep = array_fill_keys($assigneeIds, ['new' => [], 'existing' => []]);
        $assigneeCount = count($assigneeIds);
        $hasConversionColumn = Schema::hasColumn('workshop_registrations', 'converted_to_lead_at');
        $batchId = 'WS-'.$workshop->id.'-'.now()->format('YmdHis');
        $transferSummary = [
            'batch_id' => $batchId,
            'new' => [],
            'existing' => [],
            'already' => [],
        ];

        DB::transaction(function () use ($workshop, $assigneeIds, $assigneeCount, $groupId, $hasConversionColumn, $batchId, &$created, &$linkedExisting, &$skippedAlready, &$createdByRep, &$transferSummary) {
            $registrations = $workshop->registrations()->orderBy('id')->get();
            $eligible = [];

            foreach ($registrations as $reg) {
                if ($this->registrationAlreadyConverted($reg, $hasConversionColumn)) {
                    $skippedAlready++;
                    $transferSummary['already'][] = $reg->name ?: ('#'.$reg->id);

                    continue;
                }

                $eligible[] = $reg;
            }

            foreach ($eligible as $index => $reg) {
                $assigneeId = $assigneeIds[$index % $assigneeCount];
                $notes = $this->buildLeadNotesFromRegistration($workshop, $reg);
                $displayName = $reg->name ?: 'عميل محتمل من ورشة';

                $existingLead = $this->findExistingLeadByContact($reg);

                if ($existingLead) {
                    $existingLead->update([
                        'notes' => trim(($existingLead->notes ?? '')."\n\n".$notes),
                        'interest' => $existingLead->interest ?: 'الاهتمام بورشة: '.$workshop->title,
                    ]);

                    $this->markRegistrationConverted($reg, $existingLead->id, $hasConversionColumn);

                    $linkedExisting++;
                    $transferSummary['existing'][] = [
                        'name' => $displayName,
                        'lead_id' => $existingLead->id,
                        'assignee' => $existingLead->assignee?->name,
                    ];

                    $repId = (int) $existingLead->assigned_to;
                    if (! isset($createdByRep[$repId])) {
                        $createdByRep[$repId] = ['new' => [], 'existing' => []];
                    }
                    $createdByRep[$repId]['existing'][] = $displayName;

                    continue;
                }

                $lead = SalesLead::create([
                    'assigned_to' => $assigneeId,
                    'created_by' => auth()->id(),
                    'sales_lead_group_id' => $groupId,
                    'import_batch' => $batchId,
                    'name' => $displayName,
                    'phone' => $reg->phone,
                    'email' => $reg->email,
                    'source' => 'event',
                    'stage' => 'new_lead',
                    'priority' => 'normal',
                    'interest' => 'الاهتمام بورشة: '.$workshop->title,
                    'notes' => $notes,
                ]);

                $this->markRegistrationConverted($reg, $lead->id, $hasConversionColumn);

                $created++;
                $transferSummary['new'][] = [
                    'name' => $displayName,
                    'lead_id' => $lead->id,
                    'assignee' => User::query()->whereKey($assigneeId)->value('name'),
                ];
                $createdByRep[$assigneeId]['new'][] = $displayName;
            }
        });

        $notificationService = app(SalesNotificationService::class);
        foreach ($createdByRep as $repId => $repSummary) {
            if (empty($repSummary['new']) && empty($repSummary['existing'])) {
                continue;
            }

            $rep = User::query()->find($repId);
            if ($rep) {
                $notificationService->notifyWorkshopLeadsTransferred($rep, $workshop, $repSummary, $batchId);
            }
        }

        $distribution = collect($createdByRep)
            ->filter(fn ($summary) => count($summary['new']) > 0 || count($summary['existing']) > 0)
            ->map(function ($summary, $userId) {
                $name = User::query()->whereKey($userId)->value('name') ?? '#'.$userId;
                $parts = [];
                if (count($summary['new']) > 0) {
                    $parts[] = count($summary['new']).' جديد';
                }
                if (count($summary['existing']) > 0) {
                    $parts[] = count($summary['existing']).' موجود';
                }

                return $name.': '.implode(' + ', $parts);
            })
            ->implode(' · ');

        if ($created === 0 && $linkedExisting === 0) {
            $message = 'لا يوجد مسجّلون جدد للترحيل.';
            if ($skippedAlready > 0) {
                $message .= " ({$skippedAlready} مُرحَّل مسبقاً)";
            }

            return back()
                ->with('error', $message)
                ->with('workshop_transfer_summary', $transferSummary);
        }

        $message = "تم ترحيل {$created} عميل جديد";
        if ($linkedExisting > 0) {
            $message .= " وربط {$linkedExisting} موجود مسبقاً";
        }
        $message .= ' إلى Leads.';
        if ($skippedAlready > 0) {
            $message .= " تخطّي {$skippedAlready} مُرحَّل سابقاً.";
        }
        if ($distribution !== '') {
            $message .= ' التوزيع: '.$distribution.'.';
        }

        return back()
            ->with('success', $message)
            ->with('workshop_transfer_summary', $transferSummary);
    }

    private function registrationAlreadyConverted(WorkshopRegistration $registration, bool $hasConversionColumn): bool
    {
        if ($registration->isConvertedToLead()) {
            return true;
        }

        if ($hasConversionColumn && $registration->converted_to_lead_at) {
            return true;
        }

        return SalesLead::query()
            ->where('notes', 'like', '%[workshop_registration:'.$registration->id.']%')
            ->exists();
    }

    private function markRegistrationConverted(WorkshopRegistration $registration, int $leadId, bool $hasConversionColumn): void
    {
        if (! $hasConversionColumn) {
            return;
        }

        $registration->update([
            'converted_to_lead_at' => now(),
            'sales_lead_id' => $leadId,
        ]);
    }

    private function buildLeadNotesFromRegistration(Workshop $workshop, WorkshopRegistration $reg): string
    {
        $attendanceLabel = $reg->attendance_mode === 'offline'
            ? 'أوفلاين'
            : ($reg->attendance_mode === 'online' ? 'أونلاين' : 'غير محدد');

        $checkedIn = $reg->checked_in_at
            ? 'نعم — '.$reg->checked_in_at->format('Y-m-d H:i')
            : 'لا';

        return trim(
            "تم التحويل تلقائياً من تسجيل ورشة.\n"
            ."[workshop:{$workshop->id}] [workshop_registration:{$reg->id}]\n"
            ."اسم الورشة: {$workshop->title}\n"
            ."نوع الحضور: {$attendanceLabel}\n"
            ."تأكيد الحضور: {$checkedIn}\n"
            .'حالة التسجيل: '.($reg->status ?: '—')."\n"
            .'تاريخ التسجيل: '.optional($reg->created_at)->format('Y-m-d H:i')
            .(! empty($reg->notes) ? "\nملاحظات التسجيل: {$reg->notes}" : '')
        );
    }

    private function findExistingLeadByContact(WorkshopRegistration $reg): ?SalesLead
    {
        $email = $reg->email ? strtolower(trim($reg->email)) : null;
        $phoneVariants = $this->phoneMatchVariants($reg->phone);

        if (! $email && $phoneVariants === []) {
            return null;
        }

        return SalesLead::query()
            ->with('assignee:id,name')
            ->where(function ($q) use ($email, $phoneVariants) {
                if ($email) {
                    $q->whereRaw('LOWER(TRIM(email)) = ?', [$email]);
                }
                if ($phoneVariants !== []) {
                    $q->orWhereIn('phone', $phoneVariants);
                }
            })
            ->orderByDesc('id')
            ->first();
    }

    /**
     * @return list<string>
     */
    private function phoneMatchVariants(?string $phone): array
    {
        if (! $phone) {
            return [];
        }

        $normalized = $this->normalizePhone($phone);
        $variants = array_filter(array_unique([
            trim($phone),
            $normalized,
            $normalized && str_starts_with($normalized, '20') ? '0'.substr($normalized, 2) : null,
            $normalized && str_starts_with($normalized, '20') ? '+'.substr($normalized, 0, 2).' '.substr($normalized, 2) : null,
        ]));

        return array_values($variants);
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
            'whatsapp_group_link' => 'nullable|url|max:500',
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
        $data['whatsapp_group_link'] = $this->normalizeWorkshopGroupLink($data['whatsapp_group_link'] ?? null);

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

    private function normalizeWorkshopGroupLink(?string $link): ?string
    {
        $link = trim((string) $link);
        if ($link === '') {
            return null;
        }

        return app(\App\Services\WhatsAppTemplateService::class)->normalizeMetaButtonUrl($link) ?: null;
    }
}

