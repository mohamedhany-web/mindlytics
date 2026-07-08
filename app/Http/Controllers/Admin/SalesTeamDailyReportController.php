<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesTeamDailyReport;
use Illuminate\Http\Request;

class SalesTeamDailyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesTeamDailyReport::query()
            ->with(['team:id,name', 'manager:id,name'])
            ->orderByDesc('report_date')
            ->orderByDesc('id');

        if ($request->filled('team_id')) {
            $query->where('sales_team_id', (int) $request->team_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('report_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('report_date', '<=', $request->to);
        }

        $reports = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => SalesTeamDailyReport::count(),
            'submitted' => SalesTeamDailyReport::submitted()->count(),
        ];

        return view('admin.sales.team-daily-reports.index', compact('reports', 'stats'));
    }

    public function show(SalesTeamDailyReport $teamDailyReport)
    {
        $teamDailyReport->load(['team.members.user', 'manager']);

        return view('admin.sales.team-daily-reports.show', [
            'report' => $teamDailyReport,
        ]);
    }
}
