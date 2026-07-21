<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\SalesLeadGroup;
use App\Services\SalesAuditService;
use App\Services\SalesDailyReportService;
use App\Services\SalesNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class SalesLeadController extends Controller
{
    /** سياسات إلزامية — يمكن لاحقاً نقلها لإعدادات النظام */
    private const REQUIRED_ACTIVITY_DAYS_FOR_CLOSE = 7;

    public function index(Request $request)
    {
        $query = $this->indexQuery($request);
        $this->applySorting($query, $request);

        $leads = $query->paginate(20)->withQueryString();
        $categories = SalesLeadCategory::active()->ordered()->get();
        $importBatches = SalesLead::query()
            ->forAssignee(Auth::id())
            ->whereNotNull('import_batch')
            ->distinct()
            ->orderByDesc('import_batch')
            ->pluck('import_batch');

        $quickCounts = $this->indexQuickCounts(Auth::id());
        $groups = SalesLeadGroup::forAssignee(Auth::id())->orderBy('name')->get(['id', 'name']);

        return view('employee.sales.leads.index', compact('leads', 'categories', 'importBatches', 'quickCounts', 'groups'));
    }

    public function create(Request $request)
    {
        $groups = SalesLeadGroup::forAssignee(Auth::id())->orderBy('name')->get(['id', 'name', 'is_admin_managed']);
        $preselectedGroupId = $request->integer('group') ?: old('sales_lead_group_id');

        return view('employee.sales.leads.create', compact('groups', 'preselectedGroupId'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedLead($request);

        $validated['assigned_to'] = Auth::id();
        $validated['created_by'] = Auth::id();
        $validated['category_id'] = SalesLeadCategory::defaultGeneralId();

        $lead = SalesLead::create($validated);

        SalesAuditService::log(
            'sales_lead_created',
            $lead,
            null,
            $lead->only(array_keys($validated)),
            'موظف مبيعات أنشأ عميلاً محتملاً: ' . $lead->name
        );

        $this->syncTodayDailyReport();

        $warnings = $this->duplicateWarnings($request, Auth::id());

        if ($request->input('save_action') === 'another') {
            return redirect()->route('employee.sales.leads.create')
                ->with('success', 'تم إضافة «'.$lead->name.'» — سجّل العميل التالي')
                ->with('sales_duplicate_warnings', $warnings);
        }

        return redirect()->route('employee.sales.leads.show', $lead)
            ->with('success', 'تم إضافة العميل المحتمل')
            ->with('sales_duplicate_warnings', $warnings);
    }

    public function show(SalesLead $lead)
    {
        $this->authorizeOwn($lead);
        $lead->load(['activities.user', 'creator', 'category']);

        SalesAuditService::log(
            'sales_lead_viewed',
            $lead,
            null,
            null,
            'عرض عميل محتمل: ' . $lead->name
        );

        $whatsappConversation = WhatsAppConversation::query()
            ->where(function ($q) use ($lead) {
                $q->where('sales_lead_id', $lead->id);
                if ($lead->phone) {
                    $digits = preg_replace('/[^0-9]/', '', (string) $lead->phone);
                    if (strlen($digits) >= 8) {
                        $q->orWhere('phone_number', 'like', '%' . substr($digits, -9) . '%');
                    }
                }
            })
            ->where(function ($q) {
                $q->where('assigned_to', Auth::id())
                    ->orWhereHas('salesLead', fn ($lq) => $lq->where('assigned_to', Auth::id()));
            })
            ->latest('last_message_at')
            ->first();

        $whatsappInboxUrl = $whatsappConversation
            ? route('employee.sales.whatsapp.inbox.index', ['conversation' => $whatsappConversation->id])
            : ($lead->phone ? route('employee.sales.whatsapp.inbox.index', ['start_lead' => $lead->id]) : null);

        return view('employee.sales.leads.show', compact('lead', 'whatsappConversation', 'whatsappInboxUrl'));
    }

    public function edit(SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        $groups = SalesLeadGroup::forAssignee(Auth::id())->orderBy('name')->get(['id', 'name', 'is_admin_managed']);

        return view('employee.sales.leads.edit', compact('lead', 'groups'));
    }

    public function update(Request $request, SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        $validated = $this->validatedLead($request);
        $before = $lead->only(array_keys($validated));

        $oldStage = $lead->stage;

        $this->enforcePolicies($lead, $validated, $oldStage);

        $lead->update($validated);

        if ($oldStage !== $lead->stage) {
            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'type' => 'stage_change',
                'title' => 'تغيير المرحلة',
                'body' => 'من «' . SalesLead::stageLabel($oldStage) . '» إلى «' . SalesLead::stageLabel($lead->stage) . '»',
                'meta' => ['from' => $oldStage, 'to' => $lead->stage],
            ]);

            if ($lead->stage === 'won' && $oldStage !== 'won') {
                try {
                    app(SalesNotificationService::class)->notifyWinPendingApproval($lead->fresh(['assignee']));
                } catch (\Throwable $e) {
                    Log::warning('sales.lead.win_notification_failed', [
                        'lead_id' => $lead->id,
                        'user_id' => Auth::id(),
                        'message' => $e->getMessage(),
                    ]);
                    report($e);
                }
            }
        }

        $this->syncClosedAt($lead);

        SalesAuditService::log(
            'sales_lead_updated',
            $lead->fresh(),
            $before,
            $lead->only(array_keys($validated)),
            'تحديث عميل محتمل: ' . $lead->name
        );

        $this->syncTodayDailyReport();

        $warnings = $this->duplicateWarnings($request, (int) $lead->assigned_to, $lead->id);

        $successMessage = 'تم حفظ التعديلات';
        if ($lead->stage === 'won' && ! $lead->won_confirmed_at) {
            $successMessage = 'تم تسجيل الفوز — في انتظار موافقة الإدارة لاعتماد الكوميشن.';
        }

        return redirect()->route('employee.sales.leads.show', $lead)
            ->with('success', $successMessage)
            ->with('sales_duplicate_warnings', $warnings);
    }

    public function destroy(SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        $snapshot = $lead->only($lead->getFillable());
        $name = $lead->name;
        $lead->delete();

        SalesAuditService::log(
            'sales_lead_deleted',
            null,
            $snapshot,
            null,
            'حذف عميل محتمل: ' . $name
        );

        return redirect()->route('employee.sales.leads.index')
            ->with('success', 'تم حذف السجل');
    }

    public function storeActivity(Request $request, SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(SalesActivity::TYPES)),
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:5000',
        ]);

        $activity = SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'] ?? null,
        ]);

        $lead->touchLastContactFromActivity($validated['type']);

        SalesAuditService::log(
            'sales_activity_created',
            $lead,
            null,
            $activity->only(['type', 'title', 'body']),
            'نشاط مبيعات على: ' . $lead->name . ' — ' . SalesActivity::typeLabel($activity->type)
        );

        $this->syncTodayDailyReport();

        return back()->with('success', 'تم تسجيل النشاط');
    }

    public function quickActivity(Request $request, SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        $validated = $request->validate([
            'type' => 'required|string|in:call,whatsapp,follow_up,note',
            'body' => 'nullable|string|max:500',
        ]);

        $activity = SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'title' => match ($validated['type']) {
                'call' => 'مكالمة سريعة',
                'whatsapp' => 'واتساب سريع',
                'follow_up' => 'متابعة سريعة',
                default => 'ملاحظة سريعة',
            },
            'body' => $validated['body'] ?? null,
        ]);

        $lead->touchLastContactFromActivity($validated['type']);

        if ($validated['type'] === 'follow_up' && $lead->isOpen()) {
            $lead->update([
                'next_follow_up_at' => now()->addDay()->setTime(10, 0),
            ]);
        }

        SalesAuditService::log(
            'sales_activity_created',
            $lead,
            null,
            $activity->only(['type', 'title']),
            'نشاط سريع: ' . $lead->name . ' — ' . SalesActivity::typeLabel($activity->type)
        );

        $this->syncTodayDailyReport();

        $redirect = $request->input('redirect_to');
        if ($redirect && str_starts_with($redirect, url('/'))) {
            return redirect()->to($redirect)->with('success', 'تم التسجيل');
        }

        return redirect()->route('employee.sales.leads.index', $request->except(['_token', 'type', 'body', 'redirect_to']))
            ->with('success', 'تم تسجيل «'.SalesActivity::typeLabel($activity->type).'» — '.$lead->name);
    }

    public function setNextFollow(Request $request, SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        $validated = $request->validate([
            'next_follow_up_at' => 'required|date|after:now',
            'note' => 'nullable|string|max:500',
        ], [
            'next_follow_up_at.required' => 'حدد موعد المتابعة التالية.',
            'next_follow_up_at.after' => 'موعد المتابعة يجب أن يكون في المستقبل.',
        ]);

        $previous = $lead->next_follow_up_at?->toDateTimeString();
        $nextAt = \Carbon\Carbon::parse($validated['next_follow_up_at']);

        $lead->update([
            'next_follow_up_at' => $nextAt,
        ]);

        $activity = SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'type' => 'follow_up',
            'title' => 'تحديد Next Follow',
            'body' => trim(($validated['note'] ?? '')."\nموعد المتابعة: ".$nextAt->format('Y-m-d H:i')),
            'meta' => [
                'previous_next_follow_up_at' => $previous,
                'next_follow_up_at' => $nextAt->toDateTimeString(),
            ],
        ]);

        SalesAuditService::log(
            'sales_next_follow_set',
            $lead,
            ['next_follow_up_at' => $previous],
            ['next_follow_up_at' => $nextAt->toDateTimeString()],
            'Next Follow: '.$lead->name.' → '.$nextAt->format('Y-m-d H:i')
        );

        $this->syncTodayDailyReport();

        $redirect = $request->input('redirect_to');
        if ($redirect && str_starts_with($redirect, url('/'))) {
            return redirect()->to($redirect)->with('success', 'تم تحديد موعد المتابعة: '.$nextAt->format('Y-m-d H:i'));
        }

        return back()->with('success', 'تم تحديد Next Follow: '.$nextAt->format('Y-m-d H:i'));
    }

    private function syncTodayDailyReport(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        try {
            app(SalesDailyReportService::class)->syncAutoDraft($user, today());
        } catch (\Throwable $e) {
            Log::warning('sales.daily_report.sync_failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);
            report($e);
        }
    }

    /** @return array{today: int, overdue: int, stale: int, new: int} */
    private function indexQuickCounts(int $userId): array
    {
        $base = SalesLead::query()->forAssignee($userId);
        $open = fn () => (clone $base)->openPipeline();
        $staleDays = SalesLead::STALE_CONTACT_DAYS;

        return [
            'today' => $open()->whereNotNull('next_follow_up_at')->whereDate('next_follow_up_at', today())->count(),
            'overdue' => $open()->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now())->count(),
            'stale' => $open()->where(function ($q) use ($staleDays) {
                $q->where(function ($q2) use ($staleDays) {
                    $q2->whereNull('last_contacted_at')->where('created_at', '<', now()->subDays($staleDays));
                })->orWhere('last_contacted_at', '<', now()->subDays($staleDays));
            })->count(),
            'new' => (clone $base)->where('stage', 'new')->count(),
        ];
    }

    public function storeCsat(Request $request, SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        if ($lead->stage !== 'won') {
            return back()->withErrors(['csat_rating' => 'التقييم متاح عند مرحلة «مكتمل / فوز» فقط.']);
        }

        $data = $request->validate([
            'csat_rating' => 'required|integer|min:1|max:5',
            'csat_comment' => 'nullable|string|max:1000',
        ]);

        $lead->update([
            'csat_rating' => $data['csat_rating'],
            'csat_comment' => $data['csat_comment'] ?? null,
            'csat_recorded_at' => now(),
        ]);

        SalesAuditService::log(
            'sales_lead_csat_recorded',
            $lead->fresh(),
            null,
            ['csat_rating' => $data['csat_rating']],
            'تسجيل CSAT للعميل: '.$lead->name
        );

        return back()->with('success', 'تم حفظ تقييم رضا العميل.');
    }

    private function indexQuery(Request $request): Builder
    {
        $query = SalesLead::query()->forAssignee(Auth::id())->with(['assignee', 'category']);

        if ($request->filled('import_batch')) {
            $query->where('import_batch', $request->import_batch);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('group_id')) {
            $query->where('sales_lead_group_id', $request->group_id);
        }
        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('follow_up')) {
            match ($request->follow_up) {
                'overdue' => $query->openPipeline()
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<', now()),
                'today' => $query->openPipeline()
                    ->whereNotNull('next_follow_up_at')
                    ->whereDate('next_follow_up_at', today()),
                'week' => $query->openPipeline()
                    ->whereNotNull('next_follow_up_at')
                    ->where('next_follow_up_at', '<=', now()->addDays(7)->endOfDay())
                    ->where('next_follow_up_at', '>=', now()->startOfDay()),
                'none' => $query->openPipeline()->whereNull('next_follow_up_at'),
                default => null,
            };
        }
        if ($request->boolean('stale')) {
            $d = SalesLead::STALE_CONTACT_DAYS;
            $query->openPipeline()->where(function ($q) use ($d) {
                $q->where(function ($q2) use ($d) {
                    $q2->whereNull('last_contacted_at')
                        ->where('created_at', '<', now()->subDays($d));
                })->orWhere('last_contacted_at', '<', now()->subDays($d));
            });
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('company', 'like', "%{$s}%");
            });
        }

        return $query;
    }

    private function applySorting(Builder $query, Request $request): void
    {
        match ($request->input('sort')) {
            'priority' => $query->orderByPriorityDesc()->orderByDesc('updated_at'),
            'follow_up' => $query->orderByRaw('next_follow_up_at IS NULL ASC')->orderBy('next_follow_up_at'),
            'last_contact' => $query->orderByRaw('last_contacted_at IS NULL ASC')->orderByDesc('last_contacted_at'),
            'value' => $query->orderByRaw('expected_value IS NULL ASC')->orderByDesc('expected_value'),
            default => $query->orderByDesc('updated_at'),
        };
    }

    /**
     * @return list<string>
     */
    private function duplicateWarnings(Request $request, int $assigneeId, ?int $ignoreLeadId = null): array
    {
        $warnings = [];
        $phone = trim((string) $request->input('phone', ''));
        $email = trim((string) $request->input('email', ''));

        $base = SalesLead::query()->where('assigned_to', $assigneeId);
        if ($ignoreLeadId) {
            $base->where('id', '!=', $ignoreLeadId);
        }

        if ($phone !== '') {
            if ((clone $base)->where('phone', $phone)->exists()) {
                $warnings[] = 'تنبيه: يوجد عميل آخر لديك بنفس رقم الهاتف — راجع القائمة لتفادي التكرار.';
            }
        }
        if ($email !== '') {
            if ((clone $base)->where('email', $email)->exists()) {
                $warnings[] = 'تنبيه: يوجد عميل آخر لديك بنفس البريد — راجع القائمة لتفادي التكرار.';
            }
        }

        return $warnings;
    }

    private function authorizeOwn(SalesLead $lead): void
    {
        if ((int) $lead->assigned_to !== (int) Auth::id()) {
            abort(403);
        }
    }

    private function syncClosedAt(SalesLead $lead): void
    {
        $lead->refresh();
        if (in_array($lead->stage, ['won', 'lost'], true)) {
            if (! $lead->closed_at) {
                $lead->forceFill(['closed_at' => now()])->save();
            }
        } elseif ($lead->closed_at) {
            $lead->forceFill(['closed_at' => null])->save();
        }
    }

    private function validatedLead(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'source' => 'required|string|in:' . implode(',', array_keys(SalesLead::SOURCES)),
            'stage' => 'required|string|in:' . implode(',', array_keys(SalesLead::STAGES)),
            'priority' => 'required|string|in:' . implode(',', array_keys(SalesLead::PRIORITIES)),
            'interest' => 'nullable|string|max:2000',
            'course_type' => 'nullable|string|in:' . implode(',', array_keys(SalesLead::COURSE_TYPES)),
            'course_ref_id' => 'nullable|integer|min:1',
            'expected_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:5000',
            'next_follow_up_at' => 'nullable|date',
            'sales_lead_group_id' => 'nullable|integer',
            'lost_reason' => 'nullable|string|max:500',
            'lost_reason_code' => 'nullable|string|in:' . implode(',', array_keys(SalesLead::LOSS_REASONS)),
            'lost_reason_custom' => 'nullable|string|max:500',
        ]);

        $validated = $this->normalizeCourseFields($validated);

        if (($validated['stage'] ?? null) === 'lost') {
            $code = (string) ($validated['lost_reason_code'] ?? '');
            if ($code === '') {
                throw ValidationException::withMessages([
                    'lost_reason_code' => ['سبب الخسارة مطلوب عند اختيار مرحلة خسارة.'],
                ]);
            }

            if ($code === 'other') {
                $custom = trim((string) ($validated['lost_reason_custom'] ?? ''));
                if ($custom === '') {
                    throw ValidationException::withMessages([
                        'lost_reason_custom' => ['اكتب سبب الخسارة عند اختيار "أخرى".'],
                    ]);
                }
                $validated['lost_reason'] = $custom;
            } else {
                $validated['lost_reason'] = SalesLead::LOSS_REASONS[$code] ?? null;
            }
        } else {
            $validated['lost_reason'] = null;
        }

        unset($validated['lost_reason_code'], $validated['lost_reason_custom']);

        if (array_key_exists('expected_value', $validated) && $validated['expected_value'] === '') {
            $validated['expected_value'] = null;
        }

        if (
            $request->has('sales_lead_group_id')
            && Schema::hasTable('sales_lead_groups')
            && Schema::hasColumn('sales_leads', 'sales_lead_group_id')
        ) {
            $validated['sales_lead_group_id'] = $this->resolveGroupId($request->input('sales_lead_group_id'));
        } else {
            unset($validated['sales_lead_group_id']);
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalizeCourseFields(array $validated): array
    {
        $type = $validated['course_type'] ?? null;
        $refId = isset($validated['course_ref_id']) && $validated['course_ref_id'] !== ''
            ? (int) $validated['course_ref_id']
            : null;
        unset($validated['course_ref_id']);

        if (! $type || ! $refId || ! array_key_exists($type, SalesLead::COURSE_TYPES)) {
            $validated['course_type'] = null;
            $validated['advanced_course_id'] = null;
            $validated['offline_course_id'] = null;
            $validated['course_id'] = null;

            return $validated;
        }

        $exists = match ($type) {
            'advanced' => \App\Models\AdvancedCourse::query()->whereKey($refId)->exists(),
            'online' => \App\Models\OfflineCourse::query()->whereKey($refId)->where(function ($q) {
                $q->where('online_only', true)
                    ->orWhereHas('groups', function ($g) {
                        $g->where('online_booking_enabled', true)
                            ->where('is_active', true)
                            ->where('status', 'active');
                    });
            })->exists(),
            'offline' => \App\Models\OfflineCourse::query()->whereKey($refId)->where(function ($q) {
                $q->where('online_only', false)->orWhereNull('online_only');
            })->exists(),
            'legacy' => \App\Models\Course::query()->whereKey($refId)->exists(),
            default => false,
        };
        if (! $exists) {
            throw ValidationException::withMessages([
                'course_ref_id' => ['الكورس المحدد غير موجود.'],
            ]);
        }

        $validated['course_type'] = $type;
        $validated['advanced_course_id'] = $type === 'advanced' ? $refId : null;
        $validated['offline_course_id'] = in_array($type, ['online', 'offline'], true) ? $refId : null;
        $validated['course_id'] = $type === 'legacy' ? $refId : null;

        return $validated;
    }

    private function resolveGroupId(mixed $groupId): ?int
    {
        if ($groupId === null || $groupId === '') {
            return null;
        }

        $id = (int) $groupId;
        $owned = SalesLeadGroup::forAssignee(Auth::id())->whereKey($id)->exists();
        if (! $owned) {
            throw ValidationException::withMessages([
                'sales_lead_group_id' => ['المجموعة غير موجودة أو غير مسندة إليك.'],
            ]);
        }

        return $id;
    }

    /**
     * قواعد إلزامية لضبط جودة CRM قبل السماح بحفظ التغييرات.
     *
     * @param  array<string, mixed>  $validated
     */
    private function enforcePolicies(SalesLead $lead, array $validated, string $oldStage): void
    {
        $newStage = (string) ($validated['stage'] ?? $lead->stage);
        $nextFollow = $validated['next_follow_up_at'] ?? $lead->next_follow_up_at;
        $expectedValue = $validated['expected_value'] ?? $lead->expected_value;

        $isOpenNewStage = ! in_array($newStage, ['won', 'lost'], true);

        // 1) أي Lead خرج من "new" إلى مراحل أعمق يجب أن يكون له موعد متابعة
        if ($isOpenNewStage && $newStage !== 'new') {
            if (empty($nextFollow)) {
                throw ValidationException::withMessages([
                    'next_follow_up_at' => ['موعد المتابعة مطلوب عند اختيار مرحلة غير "جديد".'],
                ]);
            }
        }

        // 2) إذا كان موعد المتابعة متأخر، لا تسمح بالحفظ بدون موعد جديد في المستقبل
        if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
            $nf = $validated['next_follow_up_at'] ?? null;
            if ($nf === null || $nf === '') {
                throw ValidationException::withMessages([
                    'next_follow_up_at' => ['لا يمكن الحفظ والمتابعة متأخرة بدون تحديد موعد متابعة جديد.'],
                ]);
            }

            $parsedFollowUp = \Carbon\Carbon::parse($nf);

            if ($parsedFollowUp->isPast()) {
                throw ValidationException::withMessages([
                    'next_follow_up_at' => ['موعد المتابعة متأخر — حدّد موعداً جديداً في المستقبل قبل الحفظ.'],
                ]);
            }
        }

        // 3) الإغلاق (won/lost) يتطلب نشاط حديث + قيمة متوقعة + سبب خسارة عند lost
        if (in_array($newStage, ['won', 'lost'], true)) {
            if ($expectedValue === null || (float) $expectedValue <= 0) {
                throw ValidationException::withMessages([
                    'expected_value' => ['قيمة متوقعة مطلوبة (> 0) قبل إغلاق الـ lead (won/lost).'],
                ]);
            }

            // نشاط غير "stage_change" خلال آخر X أيام أو على الأقل وجود last_contacted_at حديث
            $cutoff = now()->subDays(self::REQUIRED_ACTIVITY_DAYS_FOR_CLOSE);

            $hasRecentActivity = SalesActivity::query()
                ->where('sales_lead_id', $lead->id)
                ->where('user_id', Auth::id())
                ->where('type', '!=', 'stage_change')
                ->where('created_at', '>=', $cutoff)
                ->exists();

            $lastContactOk = $lead->last_contacted_at && $lead->last_contacted_at->gte($cutoff);

            if (! $hasRecentActivity && ! $lastContactOk) {
                throw ValidationException::withMessages([
                    'stage' => ['لا يمكن إغلاق الـ lead بدون نشاط/تواصل حديث خلال آخر '.self::REQUIRED_ACTIVITY_DAYS_FOR_CLOSE.' أيام. سجّل Activity أولاً.'],
                ]);
            }

            if ($newStage === 'lost') {
                $lostReason = $validated['lost_reason'] ?? $lead->lost_reason;
                if (! $lostReason) {
                    throw ValidationException::withMessages([
                        'lost_reason_code' => ['سبب الخسارة مطلوب قبل إغلاق الـ lead كـ Lost.'],
                    ]);
                }
            }
        }

        // 4) حماية: لا تسمح بخفض المرحلة من won/lost إلى مراحل مفتوحة بدون مسح closed_at (يحدث تلقائياً) لكن نمنع التلاعب
        if (in_array($oldStage, ['won', 'lost'], true) && $isOpenNewStage) {
            throw ValidationException::withMessages([
                'stage' => ['لا يمكن إعادة فتح Lead مُغلَق (won/lost) من واجهة الموظف. تواصل مع الإدارة.'],
            ]);
        }
    }
}
