<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\SalesLeadGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesLeadGroupController extends Controller
{
    public function index(): View
    {
        $userId = (int) Auth::id();

        $groups = SalesLeadGroup::forAssignee($userId)
            ->with(['members:id,name'])
            ->withCount(['leads as leads_count' => fn ($q) => $q->where('assigned_to', $userId)])
            ->orderBy('name')
            ->get();

        $stats = [
            'groups' => $groups->count(),
            'leads' => $groups->sum('leads_count'),
            'admin' => $groups->where('is_admin_managed', true)->count(),
            'mine' => $groups->where('is_admin_managed', false)->count(),
        ];

        return view('employee.sales.groups.index', compact('groups', 'stats'));
    }

    public function create(): View
    {
        return view('employee.sales.groups.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
        ]);

        $group = SalesLeadGroup::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => Auth::id(),
            'created_by' => Auth::id(),
            'is_admin_managed' => false,
        ]);

        $group->syncMembers([(int) Auth::id()]);

        return redirect()->route('employee.sales.groups.show', $group)
            ->with('success', 'تم إنشاء المجموعة «'.$group->name.'»');
    }

    public function show(SalesLeadGroup $group): View
    {
        $this->authorizeGroup($group);

        $userId = (int) Auth::id();

        $group->load([
            'members:id,name',
            'leads' => fn ($q) => $q->where('assigned_to', $userId)->orderBy('name'),
        ]);

        $availableLeads = SalesLead::forAssignee($userId)
            ->openPipeline()
            ->where(function ($q) use ($group) {
                $q->whereNull('sales_lead_group_id')
                    ->orWhere('sales_lead_group_id', $group->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'sales_lead_group_id']);

        return view('employee.sales.groups.show', compact('group', 'availableLeads'));
    }

    public function update(Request $request, SalesLeadGroup $group): RedirectResponse
    {
        $this->authorizeGroup($group);

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'lead_ids' => 'nullable|array',
            'lead_ids.*' => 'integer|exists:sales_leads,id',
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $leadIds = collect($validated['lead_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        SalesLead::forAssignee(Auth::id())
            ->where('sales_lead_group_id', $group->id)
            ->whereNotIn('id', $leadIds)
            ->update(['sales_lead_group_id' => null]);

        if ($leadIds->isNotEmpty()) {
            SalesLead::forAssignee(Auth::id())
                ->whereIn('id', $leadIds)
                ->update(['sales_lead_group_id' => $group->id]);
        }

        return redirect()->route('employee.sales.groups.show', $group)
            ->with('success', 'تم تحديث المجموعة');
    }

    public function destroy(SalesLeadGroup $group): RedirectResponse
    {
        $this->authorizeGroup($group);

        if ($group->is_admin_managed) {
            return back()->with('error', 'مجموعة من الإدارة — لا يمكن حذفها.');
        }

        SalesLead::where('sales_lead_group_id', $group->id)->update(['sales_lead_group_id' => null]);
        $group->delete();

        return redirect()->route('employee.sales.groups.index')
            ->with('success', 'تم حذف المجموعة');
    }

    private function authorizeGroup(SalesLeadGroup $group): void
    {
        abort_unless($group->userHasAccess((int) Auth::id()), 403);
    }
}
