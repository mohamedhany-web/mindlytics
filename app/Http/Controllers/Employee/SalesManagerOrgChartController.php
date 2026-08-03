<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\SalesHierarchyService;
use App\Services\SalesTeamService;
use Illuminate\Support\Facades\Auth;

class SalesManagerOrgChartController extends Controller
{
    public function index(SalesTeamService $teamService, SalesHierarchyService $hierarchy)
    {
        $manager = Auth::user();
        $team = $teamService->managedTeamOrFail($manager);

        // Show manager as root + descendants in hierarchy, fallback to team members
        $tree = $hierarchy->buildTree($manager);
        $descendantIds = $hierarchy->descendantIds($manager);
        $memberIds = $teamService->memberUserIds($team);

        $staffIds = array_values(array_unique(array_merge([(int) $manager->id], $descendantIds, $memberIds)));
        $staff = $hierarchy->salesStaff()->whereIn('id', $staffIds)->load('salesInterestTypes');

        $openCounts = [];
        foreach ($staff as $user) {
            $openCounts[$user->id] = $hierarchy->openLeadsCount((int) $user->id);
        }

        return view('employee.sales-manager.org-chart.index', compact('tree', 'staff', 'openCounts', 'team', 'manager'));
    }
}
