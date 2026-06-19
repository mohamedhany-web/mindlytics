<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesLeadGroupController extends Controller
{
    public function index(): View
    {
        $groups = SalesLeadGroup::query()
            ->with(['assignee:id,name', 'creator:id,name'])
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
            'assigned_to' => 'required|integer|exists:users,id',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $rep = User::findOrFail($validated['assigned_to']);
        if (! $rep->isSalesEmployee()) {
            return back()->withErrors(['assigned_to' => 'المستخدم ليس موظف مبيعات.'])->withInput();
        }

        $group = SalesLeadGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $rep->id,
            'created_by' => Auth::id(),
            'is_admin_managed' => true,
        ]);

        $this->syncGroupLeads($group, $validated['lead_ids'] ?? [], (int) $rep->id);

        return redirect()->route('admin.sales.groups.show', $group)
            ->with('success', 'تم إنشاء المجموعة وإسنادها لـ '.$rep->name);
    }

    public function show(SalesLeadGroup $group): View
    {
        $group->load(['assignee', 'creator', 'leads' => fn ($q) => $q->orderBy('name')]);
        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $availableLeads = SalesLead::query()
            ->where(function ($q) use ($group) {
                $q->where('assigned_to', $group->assigned_to)
                    ->orWhereNull('assigned_to');
            })
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name', 'phone', 'assigned_to', 'sales_lead_group_id']);

        return view('admin.sales.groups.show', compact('group', 'reps', 'availableLeads'));
    }

    public function update(Request $request, SalesLeadGroup $group): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'assigned_to' => 'required|integer|exists:users,id',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $rep = User::findOrFail($validated['assigned_to']);
        if (! $rep->isSalesEmployee()) {
            return back()->withErrors(['assigned_to' => 'المستخدم ليس موظف مبيعات.'])->withInput();
        }

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $rep->id,
        ]);

        $this->syncGroupLeads($group, $validated['lead_ids'] ?? [], (int) $rep->id);

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
     * @param  list<int|string>  $leadIds
     */
    private function syncGroupLeads(SalesLeadGroup $group, array $leadIds, int $assigneeId): void
    {
        $ids = collect($leadIds)->map(fn ($id) => (int) $id)->unique()->values();

        SalesLead::where('sales_lead_group_id', $group->id)
            ->whereNotIn('id', $ids)
            ->update(['sales_lead_group_id' => null]);

        if ($ids->isEmpty()) {
            return;
        }

        SalesLead::whereIn('id', $ids)->update([
            'sales_lead_group_id' => $group->id,
            'assigned_to' => $assigneeId,
        ]);
    }
}
