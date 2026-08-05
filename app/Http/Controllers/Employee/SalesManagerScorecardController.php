<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesManagerDailyReview;
use App\Models\User;
use App\Services\SalesManagerDailyScorecardExcelExport;
use App\Services\SalesManagerDailyScorecardPdfService;
use App\Services\SalesManagerDailyScorecardService;
use App\Services\SalesTeamService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesManagerScorecardController extends Controller
{
    public function __construct(
        private SalesTeamService $teamService,
        private SalesManagerDailyScorecardService $scorecard,
    ) {
        $this->middleware('sales.manager');
    }

    public function index(Request $request): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();
        if ($date->isFuture()) {
            $date = now()->startOfDay();
        }

        $employeeId = $request->filled('user_id') ? (int) $request->get('user_id') : null;
        if ($employeeId && ! in_array($employeeId, $memberIds, true)) {
            $employeeId = null;
        }

        $channel = $request->get('channel');
        if (! in_array($channel, ['calls', 'whatsapp', 'social', 'cold', 'exceptions', null, ''], true)) {
            $channel = null;
        }
        $channel = $channel ?: null;

        $board = $this->scorecard->buildForTeam($team, $memberIds, $date, $employeeId, $channel);

        $members = User::query()
            ->whereIn('id', $memberIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('employee.sales-manager.scorecard.index', [
            'team' => $team,
            'members' => $members,
            'board' => $board,
            'date' => $date,
            'selectedId' => $employeeId,
            'channel' => $channel,
            'recommendations' => config('sales_manager_scorecard.recommendations', []),
        ]);
    }

    public function show(Request $request, User $employee): View
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();

        $review = SalesManagerDailyReview::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', $date->toDateString())
            ->first();

        $row = $this->scorecard->buildEmployeeDay(
            $employee,
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
            $team,
            $review
        );

        return view('employee.sales-manager.scorecard.show', [
            'team' => $team,
            'employee' => $employee,
            'date' => $date,
            'row' => $row,
            'recommendations' => config('sales_manager_scorecard.recommendations', []),
        ]);
    }

    public function review(Request $request, User $employee): RedirectResponse
    {
        $manager = Auth::user();
        $team = $this->teamService->managedTeamOrFail($manager);
        $memberIds = $this->teamService->memberUserIds($team);
        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in([
                SalesManagerDailyReview::STATUS_DRAFT,
                SalesManagerDailyReview::STATUS_REVIEWED,
                SalesManagerDailyReview::STATUS_APPROVED,
            ])],
            'recommendation' => ['required', Rule::in(array_keys(config('sales_manager_scorecard.recommendations', [])))],
            'proposed_deduction_amount' => ['nullable', 'numeric', 'min:0', 'max:99999'],
            'manager_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $date = Carbon::parse($validated['date'])->startOfDay();
        $existing = SalesManagerDailyReview::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', $date->toDateString())
            ->first();

        if ($existing?->isApproved()) {
            return redirect()
                ->route('employee.sales-manager.scorecard.show', [
                    'employee' => $employee->id,
                    'date' => $date->toDateString(),
                ])
                ->withErrors(['status' => 'هذه المراجعة معتمدة ولا يمكن تعديلها.']);
        }

        $row = $this->scorecard->buildEmployeeDay(
            $employee,
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
            $team,
            $existing
        );

        $this->scorecard->saveReview($manager, $team, $employee, $date, $row, $validated);

        return redirect()
            ->route('employee.sales-manager.scorecard.show', [
                'employee' => $employee->id,
                'date' => $date->toDateString(),
            ])
            ->with('success', 'تم حفظ مراجعة الأداء اليومية. لم يُنشأ أي خصم مالي تلقائياً.');
    }

    public function exportPdf(Request $request, SalesManagerDailyScorecardPdfService $pdf): StreamedResponse
    {
        $board = $this->boardFromRequest($request);

        return $pdf->downloadTeam($board);
    }

    public function exportExcel(Request $request, SalesManagerDailyScorecardExcelExport $excel): StreamedResponse
    {
        $board = $this->boardFromRequest($request);

        return $excel->download($board);
    }

    public function exportEmployeePdf(Request $request, User $employee, SalesManagerDailyScorecardPdfService $pdf): StreamedResponse
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);
        abort_unless(in_array((int) $employee->id, $memberIds, true), 403);

        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();

        $review = SalesManagerDailyReview::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', $date->toDateString())
            ->first();

        $row = $this->scorecard->buildEmployeeDay(
            $employee,
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
            $team,
            $review
        );

        return $pdf->downloadEmployee($team, $date, $row);
    }

    /**
     * @return array<string, mixed>
     */
    private function boardFromRequest(Request $request): array
    {
        $team = $this->teamService->managedTeamOrFail(Auth::user());
        $memberIds = $this->teamService->memberUserIds($team);

        $date = $request->filled('date')
            ? Carbon::parse($request->get('date'))->startOfDay()
            : now()->startOfDay();

        $employeeId = $request->filled('user_id') ? (int) $request->get('user_id') : null;
        if ($employeeId && ! in_array($employeeId, $memberIds, true)) {
            $employeeId = null;
        }

        $channel = $request->get('channel') ?: null;

        return $this->scorecard->buildForTeam($team, $memberIds, $date, $employeeId, $channel);
    }
}
