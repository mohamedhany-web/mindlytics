<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesActivity;
use App\Models\SalesLead;
use App\Models\User;
use App\Services\SalesAuditService;
use App\Services\SalesLeadsExcelExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->indexQuery($request);
        $this->applySorting($query, $request);

        $leads = $query->paginate(25)->withQueryString();
        $salesReps = User::salesEmployees()->orderBy('name')->get(['id', 'name']);

        return view('admin.sales.leads.index', compact('leads', 'salesReps'));
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

        return view('admin.sales.leads.create', compact('salesReps'));
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

        $warnings = $this->duplicateWarnings($request, (int) $validated['assigned_to']);

        return redirect()->route('admin.sales.leads.show', $lead)
            ->with('success', 'تم إضافة العميل المحتمل')
            ->with('sales_duplicate_warnings', $warnings);
    }

    public function show(SalesLead $lead)
    {
        $lead->load(['activities.user', 'assignee', 'creator']);

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

        return view('admin.sales.leads.edit', compact('lead', 'salesReps'));
    }

    public function update(Request $request, SalesLead $lead)
    {
        $validated = $this->validatedLead($request, true);
        $this->assertSalesRep($validated['assigned_to']);
        $before = $lead->only(array_merge(array_keys($validated), ['assigned_to']));
        $oldStage = $lead->stage;
        $oldAssignee = $lead->assigned_to;

        $lead->update($validated);

        if ((int) $oldAssignee !== (int) $lead->assigned_to) {
            SalesAuditService::log(
                'sales_lead_reassigned',
                $lead,
                ['assigned_to' => $oldAssignee],
                ['assigned_to' => $lead->assigned_to],
                'إعادة إسناد عميل: ' . $lead->name
            );
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

    private function indexQuery(Request $request): Builder
    {
        $query = SalesLead::query()->with(['assignee', 'creator']);

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
            'expected_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:5000',
            'next_follow_up_at' => 'nullable|date',
            'lost_reason' => 'nullable|string|max:500',
        ];
        if ($admin) {
            $rules['assigned_to'] = 'required|exists:users,id';
        }

        return $request->validate($rules);
    }
}
