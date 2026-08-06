<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\SalesTeam;
use App\Models\User;
use App\Services\SalesCrmComplianceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SalesCrmComplianceController extends Controller
{
    public function index(Request $request, SalesCrmComplianceService $compliance): View
    {
        $salesReps = User::salesEmployees()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $teams = SalesTeam::query()
            ->where('is_active', true)
            ->with('members:id,sales_team_id,user_id')
            ->orderBy('name')
            ->get(['id', 'name']);

        $validated = $request->validate([
            'period' => ['nullable', Rule::in(['day', 'week', 'month', 'custom'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'team_id' => ['nullable', 'integer', Rule::exists('sales_teams', 'id')],
        ]);

        $period = $validated['period'] ?? 'week';
        [$from, $to] = $this->rangeFor($period, $validated);

        $employeeId = isset($validated['user_id']) ? (int) $validated['user_id'] : null;
        if ($employeeId) {
            $rep = User::query()->find($employeeId);
            abort_unless($rep && $rep->isSalesEmployee(), 422, 'المستخدم ليس موظف مبيعات.');
        }

        $memberFilter = null;
        if (! empty($validated['team_id'])) {
            $team = $teams->firstWhere('id', (int) $validated['team_id']);
            $memberFilter = $team?->members->pluck('user_id')->map(fn ($id) => (int) $id)->all() ?? [];
            if ($employeeId && ! in_array($employeeId, $memberFilter, true)) {
                $employeeId = null;
            }
        }

        $board = $compliance->buildBoard($from, $to, $employeeId);

        if (is_array($memberFilter)) {
            $board['rows'] = array_values(array_filter(
                $board['rows'],
                fn ($row) => in_array((int) $row['employee_id'], $memberFilter, true)
            ));
            $board['summary'] = $compliance->summarizeRows($board['rows']);
            $board['insights'] = $compliance->insightsForRows($board['rows']);
            $board['exceptions'] = [];
            foreach ($board['rows'] as $row) {
                foreach ($row['exceptions'] as $ex) {
                    $board['exceptions'][] = array_merge($ex, [
                        'employee_id' => $row['employee_id'],
                        'employee_name' => $row['name'],
                    ]);
                }
            }
        }

        return view('admin.sales.crm-compliance.index', [
            'board' => $board,
            'salesReps' => $salesReps,
            'teams' => $teams,
            'period' => $period,
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
            'filters' => [
                'user_id' => $employeeId,
                'team_id' => isset($validated['team_id']) ? (int) $validated['team_id'] : null,
            ],
        ]);
    }

    public function show(Request $request, User $employee, SalesCrmComplianceService $compliance): View
    {
        abort_unless($employee->isSalesEmployee(), 404);

        $validated = $request->validate([
            'period' => ['nullable', Rule::in(['day', 'week', 'month', 'custom'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $period = $validated['period'] ?? 'week';
        [$from, $to] = $this->rangeFor($period, $validated);
        $row = $compliance->buildEmployee($employee, $from, $to);

        return view('admin.sales.crm-compliance.show', [
            'row' => $row,
            'employee' => $employee,
            'period' => $period,
            'dateFrom' => $from->toDateString(),
            'dateTo' => $to->toDateString(),
        ]);
    }

    public function lead(SalesLead $lead, SalesCrmComplianceService $compliance): View
    {
        $lead->loadMissing(['assignee:id,name']);
        $timeline = $compliance->leadTimeline($lead);

        return view('admin.sales.crm-compliance.lead', [
            'timeline' => $timeline,
            'lead' => $lead,
        ]);
    }

    /**
     * @param  array{period?: string, date_from?: string, date_to?: string}  $validated
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rangeFor(string $period, array $validated): array
    {
        return match ($period) {
            'day' => [today()->startOfDay(), today()->endOfDay()],
            'month' => [now()->startOfMonth()->startOfDay(), now()->endOfDay()],
            'custom' => [
                Carbon::parse($validated['date_from'] ?? now()->subDays(6)->toDateString())->startOfDay(),
                Carbon::parse($validated['date_to'] ?? now()->toDateString())->endOfDay(),
            ],
            default => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
        };
    }
}
