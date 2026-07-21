<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\SalesLeadGroup;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SalesAuditService;
use App\Services\SalesLeadsExcelExportService;
use App\Services\SalesLeadsImportService;
use App\Services\SalesNotificationService;
use App\Services\SalesWinCommissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesLeadController extends Controller
{
    public function index(Request $request)
    {
        $filterQuery = $this->indexQuery($request);

        $stats = [
            'total' => (clone $filterQuery)->count(),
            'open' => (clone $filterQuery)->openPipeline()->count(),
            'overdue_followups' => (clone $filterQuery)->openPipeline()
                ->whereNotNull('next_follow_up_at')
                ->where('next_follow_up_at', '<', now())
                ->count(),
            'won' => (clone $filterQuery)->where('stage', 'won')->count(),
        ];

        $query = clone $filterQuery;
        $this->applySorting($query, $request);

        $leads = $query->paginate(25)->withQueryString();
        $salesReps = User::salesEmployees()->orderBy('name')->get(['id', 'name']);
        $categories = SalesLeadCategory::active()->ordered()->get();

        return view('admin.sales.leads.index', compact('leads', 'salesReps', 'stats', 'categories'));
    }

    public function export(Request $request, SalesLeadsExcelExportService $excel): StreamedResponse
    {
        $query = $this->indexQuery($request);
        $this->applySorting($query, $request);

        // نفس تقرير Excel الخاص بالموظف (أعمدة وتنسيق وهوية الملف)
        $user = auth()->user();
        $context = 'تصدير بواسطة: ' . ($user->name ?? '') . ' — لوحة الإدارة';

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
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $categories = SalesLeadCategory::active()->ordered()->get();

        return view('admin.sales.leads.create', compact('salesReps', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedLead($request, true);
        $this->assertSalesRep($validated['assigned_to']);
        $validated['created_by'] = auth()->id();

        $lead = SalesLead::create($validated);

        SalesAuditService::log(
            'sales_lead_created_admin',
            $lead,
            null,
            $lead->only(array_keys($validated)),
            'الإدارة أنشأت عميلاً محتملاً: ' . $lead->name . ' — مسند إلى: ' . ($lead->assignee->name ?? $lead->assigned_to)
        );

        app(SalesNotificationService::class)->notifyLeadAssigned($lead->fresh(['assignee', 'category']));

        $warnings = $this->duplicateWarnings($request, (int) $validated['assigned_to']);

        return redirect()->route('admin.sales.leads.show', $lead)
            ->with('success', 'تم إضافة العميل المحتمل')
            ->with('sales_duplicate_warnings', $warnings);
    }

    public function importForm()
    {
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $categories = SalesLeadCategory::active()->ordered()->get();

        $stats = [
            'reps' => $salesReps->count(),
            'categories' => $categories->count(),
            'import_batches' => (int) SalesLead::query()->whereNotNull('import_batch')->distinct()->count('import_batch'),
            'imported_leads' => (int) SalesLead::query()->whereNotNull('import_batch')->count(),
        ];

        $recentBatches = SalesLead::query()
            ->whereNotNull('import_batch')
            ->selectRaw('import_batch, COUNT(*) as leads_count, MAX(created_at) as imported_at')
            ->groupBy('import_batch')
            ->orderByDesc('imported_at')
            ->limit(5)
            ->get();

        $groups = SalesLeadGroup::query()
            ->with(['assignee:id,name', 'members:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'assigned_to', 'is_admin_managed']);

        return view('admin.sales.leads.import', compact('salesReps', 'categories', 'stats', 'recentBatches', 'groups'));
    }

    public function importTemplate(SalesLeadsExcelExportService $excel): StreamedResponse
    {
        $spreadsheet = $excel->buildImportTemplate();
        $filename = 'قالب-استيراد-عملاء-'.now()->format('Y-m-d').'.xlsx';

        return response()->streamDownload(function () use ($excel, $spreadsheet) {
            $excel->writeToOutput($spreadsheet);
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function importStore(Request $request, SalesLeadsImportService $importService)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'assigned_to_ids' => 'nullable|array',
            'assigned_to_ids.*' => 'exists:users,id',
            'category_id' => 'required|exists:sales_lead_categories,id',
            'source' => 'nullable|string|in:'.implode(',', array_keys(SalesLead::SOURCES)),
            'default_priority' => 'nullable|string|in:'.implode(',', array_keys(SalesLead::PRIORITIES)),
            'group_id' => 'nullable|integer|exists:sales_lead_groups,id',
        ], [
            'file.required' => 'اختر ملف Excel أو CSV.',
            'category_id.required' => 'التصنيف مطلوب لتنظيم البيانات.',
        ]);

        $assigneeIds = array_values(array_unique(array_map('intval', $validated['assigned_to_ids'] ?? [])));

        if (! empty($validated['group_id'])) {
            $group = SalesLeadGroup::query()->with('members:id')->findOrFail((int) $validated['group_id']);
            $memberIds = $group->memberIds()->map(fn ($id) => (int) $id)->filter()->values()->all();

            // إن اختيرت مجموعة بموظفين ولم يُحدَّد أحد: وزّع على أعضاء المجموعة.
            // إن اختير موظفون مع المجموعة: يقتصر التوزيع على تقاطع الاختيار مع أعضاء المجموعة.
            // مجموعة بلا أعضاء + بلا اختيار موظفين: استيراد بدون إسناد — مسموح.
            if ($assigneeIds === [] && $memberIds !== []) {
                $assigneeIds = $memberIds;
            } elseif ($assigneeIds !== [] && $memberIds !== []) {
                $assigneeIds = array_values(array_intersect($assigneeIds, $memberIds));
                if ($assigneeIds === []) {
                    return back()
                        ->withInput()
                        ->withErrors(['assigned_to_ids' => 'الموظفون المختارون ليسوا ضمن مجموعة العملاء المحددة.']);
                }
            }
        }

        foreach ($assigneeIds as $repId) {
            $this->assertSalesRep((int) $repId);
        }

        try {
            $result = $importService->import(
                $request->file('file'),
                $assigneeIds,
                (int) $validated['category_id'],
                (int) auth()->id(),
                $validated['source'] ?? 'other',
                $validated['default_priority'] ?? 'normal',
                isset($validated['group_id']) ? (int) $validated['group_id'] : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $repSummary = User::query()->whereIn('id', array_keys(array_filter($result['per_rep'] ?? [])))
            ->pluck('name', 'id')
            ->map(fn ($name, $id) => $name.': '.($result['per_rep'][$id] ?? 0))
            ->implode(' · ');

        $unassignedNote = ($assigneeIds === [] && ($result['created'] ?? 0) > 0)
            ? ' (بدون إسناد موظف — يمكن التوزيع لاحقاً)'
            : '';

        return redirect()->route('admin.sales.leads.index', array_filter([
            'category_id' => $validated['category_id'],
            'import_batch' => $result['batch_id'],
            'group_id' => $validated['group_id'] ?? null,
        ]))->with('success', "تم استيراد {$result['created']} عميل — تخطي {$result['skipped']}.".($repSummary ? " ({$repSummary})" : '').$unassignedNote)
            ->with('import_errors', $result['errors']);
    }

    public function show(SalesLead $lead)
    {
        $lead->load(['activities.user', 'assignee', 'creator', 'category']);

        SalesAuditService::log(
            'sales_lead_viewed_admin',
            $lead,
            null,
            null,
            'الإدارة عرضت عميلاً محتملاً: ' . $lead->name
        );

        return view('admin.sales.leads.show', compact('lead'));
    }

    public function edit(SalesLead $lead)
    {
        $salesReps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $categories = SalesLeadCategory::active()->ordered()->get();

        return view('admin.sales.leads.edit', compact('lead', 'salesReps', 'categories'));
    }

    public function update(Request $request, SalesLead $lead)
    {
        $validated = $this->validatedLead($request, true);
        $this->assertSalesRep($validated['assigned_to']);
        $before = $lead->only(array_merge(array_keys($validated), ['assigned_to']));
        $oldStage = $lead->stage;
        $oldAssignee = $lead->assigned_to;

        $lead->update($validated);

        if ($request->filled('csat_rating')) {
            $lead->forceFill(['csat_recorded_at' => now()])->save();
        }

        if ((int) $oldAssignee !== (int) $lead->assigned_to) {
            SalesAuditService::log(
                'sales_lead_reassigned',
                $lead,
                ['assigned_to' => $oldAssignee],
                ['assigned_to' => $lead->assigned_to],
                'إعادة إسناد عميل: ' . $lead->name
            );
            app(SalesNotificationService::class)->notifyLeadAssigned($lead->fresh(['assignee', 'category']), (int) $oldAssignee);
        }

        if ($oldStage !== $lead->stage) {
            SalesActivity::create([
                'sales_lead_id' => $lead->id,
                'user_id' => auth()->id(),
                'type' => 'stage_change',
                'title' => 'تغيير المرحلة (إدارة)',
                'body' => 'من «' . SalesLead::stageLabel($oldStage) . '» إلى «' . SalesLead::stageLabel($lead->stage) . '»',
                'meta' => ['from' => $oldStage, 'to' => $lead->stage, 'by' => 'admin'],
            ]);
        }

        $this->syncClosedAt($lead);

        SalesAuditService::log(
            'sales_lead_updated_admin',
            $lead->fresh(),
            $before,
            $lead->only(array_keys($validated)),
            'الإدارة حدّثت عميلاً محتملاً: ' . $lead->name
        );

        $warnings = $this->duplicateWarnings($request, (int) $validated['assigned_to'], $lead->id);

        return redirect()->route('admin.sales.leads.show', $lead)
            ->with('success', 'تم حفظ التعديلات')
            ->with('sales_duplicate_warnings', $warnings);
    }

    public function destroy(SalesLead $lead)
    {
        $snapshot = $lead->only($lead->getFillable());
        $name = $lead->name;
        $lead->delete();

        SalesAuditService::log(
            'sales_lead_deleted_admin',
            null,
            $snapshot,
            null,
            'الإدارة حذفت عميلاً محتملاً: ' . $name
        );

        return redirect()->route('admin.sales.leads.index')
            ->with('success', 'تم حذف السجل');
    }

    public function storeActivity(Request $request, SalesLead $lead)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(SalesActivity::TYPES)),
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:5000',
        ]);

        $activity = SalesActivity::create([
            'sales_lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'] ?? null,
        ]);

        $lead->touchLastContactFromActivity($validated['type']);

        SalesAuditService::log(
            'sales_activity_created_admin',
            $lead,
            null,
            $activity->only(['type', 'title', 'body']),
            'الإدارة سجّلت نشاطاً على: ' . $lead->name
        );

        return back()->with('success', 'تم تسجيل النشاط');
    }

    /**
     * اعتماد فوز Lead وصرف الكوميشن لموظف المبيعات (ينزل في حساب الموظف).
     */
    public function confirmWin(Request $request, SalesLead $lead)
    {
        $validated = $request->validate([
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_notes' => 'nullable|string|max:2000',
        ]);

        $result = app(SalesWinCommissionService::class)->approveAndPayCommission(
            $lead,
            array_key_exists('commission_amount', $validated) && $validated['commission_amount'] !== null
                ? (float) $validated['commission_amount']
                : null,
            $validated['commission_notes'] ?? null,
        );

        if (! ($result['success'] ?? false)) {
            return back()->withErrors(['error' => $result['error'] ?? 'فشل الاعتماد']);
        }

        return back()->with('success', 'تم اعتماد الفوز وصرف الكوميشن بنجاح.');
    }

    private function indexQuery(Request $request): Builder
    {
        $query = SalesLead::query()->with(['assignee', 'creator', 'category']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('import_batch')) {
            $query->where('import_batch', $request->import_batch);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
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
                $warnings[] = 'تنبيه: يوجد عميل آخر مسند لنفس الموظف بنفس رقم الهاتف.';
            }
        }
        if ($email !== '') {
            if ((clone $base)->where('email', $email)->exists()) {
                $warnings[] = 'تنبيه: يوجد عميل آخر مسند لنفس الموظف بنفس البريد.';
            }
        }

        return $warnings;
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

    private function assertSalesRep(int $userId): void
    {
        if (! User::salesEmployees()->where('is_active', true)->whereKey($userId)->exists()) {
            throw ValidationException::withMessages([
                'assigned_to' => ['يجب اختيار موظف مبيعات فعّال.'],
            ]);
        }
    }

    private function validatedLead(Request $request, bool $admin): array
    {
        $rules = [
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
            'lost_reason' => 'nullable|string|max:500',
            'lost_reason_code' => 'nullable|string|in:' . implode(',', array_keys(SalesLead::LOSS_REASONS)),
            'lost_reason_custom' => 'nullable|string|max:500',
        ];
        if ($admin) {
            $rules['assigned_to'] = 'required|exists:users,id';
            $rules['category_id'] = 'required|exists:sales_lead_categories,id';
            $rules['csat_rating'] = 'nullable|integer|min:1|max:5';
            $rules['csat_comment'] = 'nullable|string|max:1000';
        }

        $validated = $request->validate($rules);
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
                $q->where('is_active', true)->orWhere('status', 'active');
            })->exists(),
            'legacy' => \App\Models\Course::query()->whereKey($refId)->exists(),
            default => false,
        };
        if (! $exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'course_ref_id' => ['الكورس المحدد غير موجود.'],
            ]);
        }

        $validated['course_type'] = $type;
        $validated['advanced_course_id'] = $type === 'advanced' ? $refId : null;
        $validated['offline_course_id'] = in_array($type, ['online', 'offline'], true) ? $refId : null;
        $validated['course_id'] = $type === 'legacy' ? $refId : null;

        return $validated;
    }
}
