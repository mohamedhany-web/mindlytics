<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\SalesManagerHubService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesManagerDashboardController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private SalesManagerHubService $hub,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $this->teamService->managedTeamOrFail($user);
        $memberIds = $this->teamService->memberUserIds($team);

        $date = $request->filled('date') && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('date'))
            ? Carbon::parse($request->query('date'))->startOfDay()
            : today();

        $compareA = $request->integer('compare_a') ?: null;
        $compareB = $request->integer('compare_b') ?: null;
        if ($compareA && ! in_array($compareA, $memberIds, true)) {
            $compareA = null;
        }
        if ($compareB && ! in_array($compareB, $memberIds, true)) {
            $compareB = null;
        }

        $hub = $this->hub->build($team, $memberIds, $date, $compareA, $compareB);

        return view('employee.sales-manager.dashboard', [
            'team' => $team,
            'hub' => $hub,
            'date' => $date,
            'compareA' => $compareA,
            'compareB' => $compareB,
        ]);
    }
}
