<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Services\SalesGroupPrintPdfService;
use App\Services\SalesLeadWhatsAppBatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesLeadGroupController extends Controller
{
    public function index(Request $request): View
    {
        $query = SalesLeadGroup::query()
            ->with(['assignee:id,name', 'creator:id,name', 'members:id,name'])
            ->withCount('leads');

        if (Schema::hasTable('sales_lead_group_members')) {
            $query->withCount('members');
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $groups = $query->orderByDesc('updated_at')->paginate(20)->withQueryString();

        $stats = [
            'total' => SalesLeadGroup::count(),
            'leads' => (int) SalesLead::whereNotNull('sales_lead_group_id')->count(),
            'members' => Schema::hasTable('sales_lead_group_members')
                ? (int) DB::table('sales_lead_group_members')->distinct()->count('user_id')
                : (int) SalesLeadGroup::whereNotNull('assigned_to')->distinct()->count('assigned_to'),
            'empty' => SalesLeadGroup::doesntHave('leads')->count(),
        ];

        return view('admin.sales.groups.index', compact('groups', 'stats'));
    }

    public function create(): View
    {
        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.sales.groups.create', compact('reps'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $memberIds = $this->resolveMemberIds($validated['member_ids'] ?? []);
        if ($memberIds === null) {
            return back()->withErrors(['member_ids' => 'اختيار الموظفين غير صالح — اختر موظفي مبيعات فعّالين فقط، أو اترك الحقل فارغاً.'])->withInput();
        }

        $group = SalesLeadGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $memberIds[0] ?? null,
            'created_by' => Auth::id(),
            'is_admin_managed' => true,
        ]);

        $group->syncMembers($memberIds);
        $this->syncGroupLeads($group, $validated['lead_ids'] ?? [], $memberIds);

        $message = $memberIds === []
            ? 'تم إنشاء المجموعة بدون إسناد موظفين — يمكنك إضافتهم لاحقاً من صفحة المجموعة.'
            : 'تم إنشاء المجموعة وإسنادها لـ '.User::query()->whereIn('id', $memberIds)->orderBy('name')->pluck('name')->implode('، ');

        return redirect()->route('admin.sales.groups.show', $group)
            ->with('success', $message);
    }

    public function show(SalesLeadGroup $group): View
    {
        $group->load([
            'assignee',
            'creator',
            'members:id,name',
            'leads' => fn ($q) => $q->with('assignee:id,name')->orderBy('name'),
        ]);

        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $memberIds = $group->memberIds()->all();

        $availableLeads = SalesLead::query()
            ->with('assignee:id,name')
            ->when($memberIds !== [], fn ($q) => $q->whereIn('assigned_to', $memberIds))
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'phone', 'assigned_to', 'sales_lead_group_id']);

        $latestBatch = app(SalesLeadWhatsAppBatchService::class)->latestForGroup($group->id);
        $leadsWithPhone = $group->leads->filter(fn ($l) => ! empty($l->phone));

        return view('admin.sales.groups.show', compact('group', 'reps', 'availableLeads', 'latestBatch', 'leadsWithPhone'));
    }

    public function update(Request $request, SalesLeadGroup $group): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'integer|exists:users,id',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $memberIds = $this->resolveMemberIds($validated['member_ids'] ?? []);
        if ($memberIds === null) {
            return back()->withErrors(['member_ids' => 'اختيار الموظفين غير صالح — اختر موظفي مبيعات فعّالين فقط، أو اترك الحقل فارغاً.'])->withInput();
        }

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $memberIds[0] ?? null,
        ]);

        $group->syncMembers($memberIds);
        $this->syncGroupLeads($group, $validated['lead_ids'] ?? [], $memberIds);

        return redirect()->route('admin.sales.groups.show', $group)
            ->with('success', 'تم تحديث المجموعة');
    }

    public function destroy(SalesLeadGroup $group): RedirectResponse
    {
        SalesLead::where('sales_lead_group_id', $group->id)->update(['sales_lead_group_id' => null]);
        $group->delete();

        return redirect()->route('admin.sales.groups.index')->with('success', 'تم حذف المجموعة');
    }

    /**
     * طباعة PDF نموذج ورقي لعملاء المجموعة (كامل أو لموظف محدد).
     */
    public function printPdf(Request $request, SalesLeadGroup $group, SalesGroupPrintPdfService $pdf): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|integer|exists:users,id',
        ]);

        $employee = null;
        if (! empty($validated['employee_id'])) {
            // نقبل أي مستخدم موجود في المجموعة / مسند له عملاء — ليس فقط job=sales
            // (قد يكون مدير مبيعات أو موظف مضاف للمجموعة)
            $employee = User::query()
                ->whereKey((int) $validated['employee_id'])
                ->first(['id', 'name', 'is_active', 'is_employee']);

            if (! $employee) {
                return redirect()
                    ->route('admin.sales.groups.show', $group)
                    ->with('error', 'الموظف المحدد غير موجود.');
            }
        }

        try {
            return $pdf->download($group, $employee);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Admin group print PDF endpoint failed', [
                'group_id' => $group->id,
                'employee_id' => $employee?->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $detail = trim(mb_substr($e->getMessage(), 0, 300));

            return redirect()
                ->route('admin.sales.groups.show', $group)
                ->with('error', 'تعذّر إنشاء ملف PDF'.($detail !== '' ? ': '.$detail : '. حاول مرة أخرى.'));
        }
    }

    /**
     * @param  list<int|string>  $rawIds
     * @return list<int>|null null = اختيار غير صالح (وليس فراغ مقصود)
     */
    private function resolveMemberIds(array $rawIds): ?array
    {
        $ids = collect($rawIds)->map(fn ($id) => (int) $id)->unique()->filter()->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $valid = User::salesEmployees()
            ->where('is_active', true)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($valid->count() !== $ids->count()) {
            return null;
        }

        return $ids->all();
    }

    /**
     * @param  list<int|string>  $leadIds
     * @param  list<int>  $memberIds
     */
    private function syncGroupLeads(SalesLeadGroup $group, array $leadIds, array $memberIds): void
    {
        $ids = collect($leadIds)->map(fn ($id) => (int) $id)->unique()->values();

        SalesLead::where('sales_lead_group_id', $group->id)
            ->whereNotIn('id', $ids)
            ->update(['sales_lead_group_id' => null]);

        if ($ids->isEmpty()) {
            return;
        }

        $query = SalesLead::query()->whereIn('id', $ids);

        // عند وجود موظفين: اربط فقط عملاء محافظهم. بدون موظفين (مجموعة إدارية): اربط أي Lead محدد.
        if ($memberIds !== []) {
            $query->whereIn('assigned_to', $memberIds);
        }

        $query->update(['sales_lead_group_id' => $group->id]);
    }
}
