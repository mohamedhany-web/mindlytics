<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use App\Services\SalesLeadWhatsAppBatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesLeadGroupController extends Controller
{
    public function index(): View
    {
        $groups = SalesLeadGroup::query()
            ->with(['assignee:id,name', 'creator:id,name', 'members:id,name'])
            ->withCount('leads')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.sales.groups.index', compact('groups'));
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
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:users,id',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $memberIds = $this->resolveMemberIds($validated['member_ids']);
        if ($memberIds === null) {
            return back()->withErrors(['member_ids' => 'اختر موظفي مبيعات فعّالين.'])->withInput();
        }

        $group = SalesLeadGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $memberIds[0],
            'created_by' => Auth::id(),
            'is_admin_managed' => true,
        ]);

        $group->syncMembers($memberIds);
        $this->syncGroupLeads($group, $validated['lead_ids'] ?? [], $memberIds);

        $repNames = User::query()->whereIn('id', $memberIds)->orderBy('name')->pluck('name')->implode('، ');

        return redirect()->route('admin.sales.groups.show', $group)
            ->with('success', 'تم إنشاء المجموعة وإسنادها لـ '.$repNames);
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
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'integer|exists:users,id',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $memberIds = $this->resolveMemberIds($validated['member_ids']);
        if ($memberIds === null) {
            return back()->withErrors(['member_ids' => 'اختر موظفي مبيعات فعّالين.'])->withInput();
        }

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $memberIds[0],
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
     * @param  list<int|string>  $rawIds
     * @return list<int>|null
     */
    private function resolveMemberIds(array $rawIds): ?array
    {
        $ids = collect($rawIds)->map(fn ($id) => (int) $id)->unique()->filter()->values();

        if ($ids->isEmpty()) {
            return null;
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

        SalesLead::query()
            ->whereIn('id', $ids)
            ->whereIn('assigned_to', $memberIds)
            ->update(['sales_lead_group_id' => $group->id]);
    }
}
