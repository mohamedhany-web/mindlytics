<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Services\SalesAuditService;
use App\Services\SalesLeadsExcelExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesLeadController extends Controller
{
    /** سياسات إلزامية — يمكن لاحقاً نقلها لإعدادات النظام */
    private const REQUIRED_ACTIVITY_DAYS_FOR_CLOSE = 7;

    public function index(Request $request)
    {
        $query = $this->indexQuery($request);
        $this->applySorting($query, $request);

        $leads = $query->paginate(20)->withQueryString();

        return view('employee.sales.leads.index', compact('leads'));
    }

    public function export(Request $request, SalesLeadsExcelExportService $excel): StreamedResponse
    {
        $query = $this->indexQuery($request);
        $this->applySorting($query, $request);

        $user = Auth::user();
        $context = 'تصدير بواسطة: ' . ($user->name ?? '') . ' — موظف مبيعات';

        $spreadsheet = $excel->buildSpreadsheet($query, false, $context);
        $filename = 'عملاء-محتملون-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($excel, $spreadsheet) {
            $excel->writeToOutput($spreadsheet);
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function create()
    {
        return view('employee.sales.leads.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatedLead($request);

        $validated['assigned_to'] = Auth::id();
        $validated['created_by'] = Auth::id();

        $lead = SalesLead::create($validated);

        SalesAuditService::log(
            'sales_lead_created',
            $lead,
            null,
            $lead->only(array_keys($validated)),
            'موظف مبيعات أنشأ عميلاً محتملاً: ' . $lead->name
        );

        $warnings = $this->duplicateWarnings($request, Auth::id());

        return redirect()->route('employee.sales.leads.show', $lead)
            ->with('success', 'تم إضافة العميل المحتمل')
            ->with('sales_duplicate_warnings', $warnings);
    }

    public function show(SalesLead $lead)
    {
        $this->authorizeOwn($lead);
        $lead->load(['activities.user', 'creator']);

        SalesAuditService::log(
            'sales_lead_viewed',
            $lead,
            null,
            null,
            'عرض عميل محتمل: ' . $lead->name
        );

        return view('employee.sales.leads.show', compact('lead'));
    }

    public function edit(SalesLead $lead)
    {
        $this->authorizeOwn($lead);

        return view('employee.sales.leads.edit', compact('lead'));
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
        }

        $this->syncClosedAt($lead);

        SalesAuditService::log(
            'sales_lead_updated',
            $lead->fresh(),
            $before,
            $lead->only(array_keys($validated)),
            'تحديث عميل محتمل: ' . $lead->name
        );

        $warnings = $this->duplicateWarnings($request, (int) $lead->assigned_to, $lead->id);

        return redirect()->route('employee.sales.leads.show', $lead)
            ->with('success', 'تم حفظ التعديلات')
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

        return back()->with('success', 'تم تسجيل النشاط');
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
        $query = SalesLead::query()->forAssignee(Auth::id())->with('assignee');

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
            'expected_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:5000',
            'next_follow_up_at' => 'nullable|date',
            'lost_reason' => 'nullable|string|max:500',
            'lost_reason_code' => 'nullable|string|in:' . implode(',', array_keys(SalesLead::LOSS_REASONS)),
            'lost_reason_custom' => 'nullable|string|max:500',
        ]);

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

        return $validated;
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

        // 2) إذا كان موعد المتابعة متأخر، لا تسمح بالحفظ بدون تعديل الموعد لمستقبل
        if ($lead->isOpen() && $lead->isFollowUpOverdue()) {
            $nf = $validated['next_follow_up_at'] ?? null;
            if ($nf === null) {
                throw ValidationException::withMessages([
                    'next_follow_up_at' => ['لا يمكن الحفظ والمتابعة متأخرة بدون تحديد موعد متابعة جديد.'],
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
