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
        $staff = $hierarchy->salesStaff()->load('salesInterestTypes');
        $openCounts = [];
        foreach ($staff as $user) {
            $openCounts[$user->id] = $hierarchy->openLeadsCount((int) $user->id);
        }

        return view('admin.sales.org-chart.index', compact('tree', 'staff', 'openCounts'));
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
