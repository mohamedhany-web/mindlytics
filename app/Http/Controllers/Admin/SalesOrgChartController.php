<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SalesHierarchyService;
use App\Services\SalesSpecialtyService;
use Illuminate\Http\Request;

class SalesOrgChartController extends Controller
{
    public function index(SalesHierarchyService $hierarchy, SalesSpecialtyService $specialtyService)
    {
        $tree = $hierarchy->buildTree();
        $staff = $hierarchy->salesStaff()->load(['salesInterestTypes', 'employeeJob']);
        $openCounts = [];
        foreach ($staff as $user) {
            $openCounts[$user->id] = $hierarchy->openLeadsCount((int) $user->id);
        }

        $managers = User::salesManagers()
            ->where('is_active', true)
            ->with(['employeeJob', 'salesInterestTypes'])
            ->orderBy('name')
            ->get();

        $employees = User::salesEmployees()
            ->where('is_active', true)
            ->with(['employeeJob', 'salesInterestTypes'])
            ->orderBy('name')
            ->get();

        $directReportsCount = [];
        foreach ($staff as $user) {
            $directReportsCount[$user->id] = $staff
                ->filter(fn (User $u) => (int) $u->sales_reports_to_id === (int) $user->id)
                ->count();
        }

        $stats = [
            'managers' => $managers->count(),
            'employees' => $employees->count(),
            'open_leads' => (int) array_sum($openCounts),
            'unlinked_reps' => $employees->filter(fn (User $u) => ! $u->sales_reports_to_id)->count(),
        ];

        return view('admin.sales.org-chart.index', compact(
            'tree',
            'staff',
            'openCounts',
            'managers',
            'employees',
            'directReportsCount',
            'stats'
        ));
    }

    public function update(Request $request, User $user, SalesHierarchyService $hierarchy)
    {
        if (! $user->isSalesEmployee() && ! $user->isSalesManager()) {
            abort(404);
        }

        $validated = $request->validate([
            'sales_reports_to_id' => 'nullable|integer|exists:users,id',
        ]);

        $hierarchy->setReportsTo(
            $user,
            isset($validated['sales_reports_to_id']) && $validated['sales_reports_to_id'] !== ''
                ? (int) $validated['sales_reports_to_id']
                : null
        );

        return back()->with('success', 'تم تحديث المدير المباشر لـ '.$user->name);
    }
}
