<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\Transaction;
use App\Models\User;
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

            $pendingEst = 0.0;
            foreach ($pendingLeads as $pl) {
                $pendingEst += $rep->calculateSalesCommissionAmount((float) ($pl->expected_value ?? 0));
            }

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
}
