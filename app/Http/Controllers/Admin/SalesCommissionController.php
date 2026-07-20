<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\Transaction;
use App\Models\User;
use App\Services\SalesCommissionTierService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesCommissionController extends Controller
{
    /**
     * ملخص كوميشن المبيعات لكل موظف (معتمد / معلّق / نسب / إعدادات).
     */
    public function index(Request $request)
    {
        $view = $request->query('view', 'month');
        if (! in_array($view, ['month', 'all'], true)) {
            $view = 'month';
        }

        $yearMonth = (string) $request->query('year_month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $yearMonth = now()->format('Y-m');
        }

        $rangeStart = null;
        $rangeEnd = null;
        if ($view === 'month') {
            $rangeStart = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();
            $rangeEnd = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->endOfMonth();
        }

        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();

        $rows = [];
        $totals = [
            'confirmed_wins' => 0,
            'expected_confirmed' => 0.0,
            'commission_from_leads' => 0.0,
            'txn_commission' => 0.0,
            'pending_wins' => 0,
            'pending_estimated' => 0.0,
        ];

        foreach ($reps as $rep) {
            $confirmedBase = SalesLead::query()
                ->where('assigned_to', $rep->id)
                ->whereNotNull('won_confirmed_at');

            if ($rangeStart && $rangeEnd) {
                $confirmedBase->whereBetween('won_confirmed_at', [$rangeStart, $rangeEnd]);
            }

            $confirmedWins = (int) (clone $confirmedBase)->count();
            $expectedSum = (float) (clone $confirmedBase)->sum('expected_value');
            $commissionFromLeads = (float) (clone $confirmedBase)->sum('commission_amount');

            $txnQ = Transaction::query()
                ->where('user_id', $rep->id)
                ->where('type', 'credit')
                ->where('category', 'commission');
            if ($rangeStart && $rangeEnd) {
                $txnQ->whereBetween('created_at', [$rangeStart, $rangeEnd]);
            }
            $txnSum = (float) $txnQ->sum('amount');

            $pendingLeads = SalesLead::query()
                ->where('assigned_to', $rep->id)
                ->where('stage', 'won')
                ->whereNull('won_confirmed_at')
                ->get(['expected_value']);

            $pendingEst = $this->estimatePendingCommission($rep, $pendingLeads->count(), $pendingLeads);

            $commissionRatePct = $expectedSum > 0.0001
                ? round($commissionFromLeads / $expectedSum * 100, 2)
                : null;

            $rows[] = [
                'rep' => $rep,
                'confirmed_wins' => $confirmedWins,
                'expected_confirmed' => $expectedSum,
                'commission_from_leads' => $commissionFromLeads,
                'txn_commission' => $txnSum,
                'commission_rate_pct' => $commissionRatePct,
                'pending_wins' => $pendingLeads->count(),
                'pending_estimated' => round($pendingEst, 2),
                'mismatch' => abs($commissionFromLeads - $txnSum) > 0.02,
            ];

            $totals['confirmed_wins'] += $confirmedWins;
            $totals['expected_confirmed'] += $expectedSum;
            $totals['commission_from_leads'] += $commissionFromLeads;
            $totals['txn_commission'] += $txnSum;
            $totals['pending_wins'] += $pendingLeads->count();
            $totals['pending_estimated'] += $pendingEst;
        }

        $totals['pending_estimated'] = round($totals['pending_estimated'], 2);
        $teamRatePct = $totals['expected_confirmed'] > 0.0001
            ? round($totals['commission_from_leads'] / $totals['expected_confirmed'] * 100, 2)
            : null;

        $periodLabel = $view === 'all'
            ? 'كل الفترات'
            : ($rangeStart ? $rangeStart->copy()->locale('ar')->translatedFormat('F Y') : '');

        return view('admin.sales.commissions.index', compact(
            'rows',
            'totals',
            'view',
            'yearMonth',
            'teamRatePct',
            'periodLabel'
        ));
    }

    /**
     * تفاصيل كوميشن موظف: كل العملاء المعتمدين بالكامل (won + won_confirmed_at).
     */
    public function show(Request $request, User $user)
    {
        if (! $user->isSalesEmployee()) {
            abort(404);
        }

        $view = $request->query('view', 'all');
        if (! in_array($view, ['month', 'all'], true)) {
            $view = 'all';
        }

        $yearMonth = (string) $request->query('year_month', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            $yearMonth = now()->format('Y-m');
        }

        $rangeStart = null;
        $rangeEnd = null;
        if ($view === 'month') {
            $rangeStart = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfMonth();
            $rangeEnd = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->endOfMonth();
        }

        $confirmedQuery = SalesLead::query()
            ->where('assigned_to', $user->id)
            ->where('stage', 'won')
            ->whereNotNull('won_confirmed_at')
            ->with(['category:id,name', 'creator:id,name', 'advancedCourse:id,title', 'offlineCourse:id,title', 'legacyCourse:id,title']);

        if ($rangeStart && $rangeEnd) {
            $confirmedQuery->whereBetween('won_confirmed_at', [$rangeStart, $rangeEnd]);
        }

        $confirmedLeads = (clone $confirmedQuery)
            ->orderByDesc('won_confirmed_at')
            ->get();

        $pendingLeads = SalesLead::query()
            ->where('assigned_to', $user->id)
            ->where('stage', 'won')
            ->whereNull('won_confirmed_at')
            ->with(['category:id,name'])
            ->orderByDesc('closed_at')
            ->get();

        $expectedSum = (float) $confirmedLeads->sum('expected_value');
        $commissionFromLeads = (float) $confirmedLeads->sum('commission_amount');

        $txnQ = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('category', 'commission');
        if ($rangeStart && $rangeEnd) {
            $txnQ->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        }
        $txnSum = (float) $txnQ->sum('amount');

        $pendingEst = $this->estimatePendingCommission($user, $pendingLeads->count(), $pendingLeads);

        $tierBreakdown = null;
        $tierLineByLeadId = [];
        if (($user->sales_commission_mode ?? '') === 'tier') {
            $tierAt = $rangeStart?->copy() ?? now();
            [$tierStart, $tierEnd] = SalesCommissionTierService::periodRange($user, $tierAt);

            $tierLeadsQ = SalesLead::query()
                ->where('assigned_to', $user->id)
                ->where('stage', 'won')
                ->whereNotNull('won_confirmed_at')
                ->with(['category:id,name', 'creator:id,name'])
                ->orderBy('won_confirmed_at')
                ->orderBy('id');

            if ($tierStart && $tierEnd) {
                $tierLeadsQ->whereBetween('won_confirmed_at', [$tierStart, $tierEnd]);
            }

            $tierLeads = $tierLeadsQ->get();
            $tierBreakdown = SalesCommissionTierService::buildBreakdown($user, $tierLeads);
            foreach ($tierBreakdown['lines'] as $line) {
                $tierLineByLeadId[$line['lead']->id] = $line;
            }
        }

        $periodLabel = $view === 'all'
            ? 'كل الفترات'
            : ($rangeStart ? $rangeStart->copy()->locale('ar')->translatedFormat('F Y') : '');

        $stats = [
            'confirmed_wins' => $confirmedLeads->count(),
            'expected_confirmed' => $expectedSum,
            'commission_from_leads' => $commissionFromLeads,
            'txn_commission' => $txnSum,
            'pending_wins' => $pendingLeads->count(),
            'pending_estimated' => round($pendingEst, 2),
            'rate_pct' => $expectedSum > 0.0001
                ? round($commissionFromLeads / $expectedSum * 100, 2)
                : null,
        ];

        return view('admin.sales.commissions.show', compact(
            'user',
            'confirmedLeads',
            'pendingLeads',
            'stats',
            'view',
            'yearMonth',
            'periodLabel',
            'tierBreakdown',
            'tierLineByLeadId'
        ));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, SalesLead>  $pendingLeads
     */
    private function estimatePendingCommission(User $rep, int $pendingCount, $pendingLeads): float
    {
        if ($pendingCount < 1) {
            return 0.0;
        }

        if (($rep->sales_commission_mode ?? '') === 'tier') {
            $base = SalesCommissionTierService::confirmedWinsCount($rep);
            $total = 0.0;
            for ($i = 1; $i <= $pendingCount; $i++) {
                $saleNumber = $base + $i;
                $total += SalesCommissionTierService::rateForSaleNumber($rep, $saleNumber);
                $total += SalesCommissionTierService::milestoneBonusForSaleNumber($rep, $saleNumber);
            }

            return round($total, 2);
        }

        $total = 0.0;
        foreach ($pendingLeads as $pl) {
            $total += $rep->calculateSalesCommissionAmount((float) ($pl->expected_value ?? 0));
        }

        return round($total, 2);
    }
}
