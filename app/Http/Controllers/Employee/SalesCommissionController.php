<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesCommissionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
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

        $confirmedBase = SalesLead::query()
            ->where('assigned_to', $user->id)
            ->whereNotNull('won_confirmed_at');

        if ($rangeStart && $rangeEnd) {
            $confirmedBase->whereBetween('won_confirmed_at', [$rangeStart, $rangeEnd]);
        }

        $confirmedWins = (int) (clone $confirmedBase)->count();
        $expectedSum = (float) (clone $confirmedBase)->sum('expected_value');
        $commissionFromLeads = (float) (clone $confirmedBase)->sum('commission_amount');

        $txnQ = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('category', 'commission');
        if ($rangeStart && $rangeEnd) {
            $txnQ->whereBetween('created_at', [$rangeStart, $rangeEnd]);
        }
        $txnSum = (float) $txnQ->sum('amount');

        $pendingLeads = SalesLead::query()
            ->where('assigned_to', $user->id)
            ->where('stage', 'won')
            ->whereNull('won_confirmed_at')
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'expected_value', 'closed_at']);

        $pendingEst = 0.0;
        foreach ($pendingLeads as $pl) {
            $pendingEst += $user->calculateSalesCommissionAmount((float) ($pl->expected_value ?? 0));
        }

        $confirmedLeads = SalesLead::query()
            ->where('assigned_to', $user->id)
            ->whereNotNull('won_confirmed_at')
            ->when($rangeStart && $rangeEnd, fn ($q) => $q->whereBetween('won_confirmed_at', [$rangeStart, $rangeEnd]))
            ->with('category')
            ->orderByDesc('won_confirmed_at')
            ->limit(50)
            ->get();

        $recentTxns = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('category', 'commission')
            ->when($rangeStart && $rangeEnd, fn ($q) => $q->whereBetween('created_at', [$rangeStart, $rangeEnd]))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $periodLabel = $view === 'all'
            ? 'كل الفترات'
            : ($rangeStart ? $rangeStart->copy()->locale('ar')->translatedFormat('F Y') : '');

        $commissionRatePct = $expectedSum > 0.0001
            ? round($commissionFromLeads / $expectedSum * 100, 2)
            : null;

        return view('employee.sales.commissions.index', compact(
            'user',
            'view',
            'yearMonth',
            'periodLabel',
            'confirmedWins',
            'expectedSum',
            'commissionFromLeads',
            'txnSum',
            'pendingLeads',
            'pendingEst',
            'confirmedLeads',
            'recentTxns',
            'commissionRatePct'
        ));
    }
}
