<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingGatewayOperationsController extends Controller
{
    public function index(Request $request): View
    {
        $gateway = $request->filled('gateway') ? (string) $request->get('gateway') : null;
        $from = $request->filled('date_from') ? (string) $request->get('date_from') : null;
        $to = $request->filled('date_to') ? (string) $request->get('date_to') : null;
        $q = trim((string) $request->get('q', ''));

        $base = Payment::query()->gatewayOnline();

        if ($gateway) {
            $base->where('payment_gateway', $gateway);
        }
        if ($from) {
            $base->whereDate('paid_at', '>=', $from);
        }
        if ($to) {
            $base->whereDate('paid_at', '<=', $to);
        }
        if ($q !== '') {
            $like = '%'.$q.'%';
            $base->where(function ($sub) use ($like, $q) {
                $sub->where('payment_number', 'like', $like)
                    ->orWhere('notes', 'like', $like)
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('invoice_number', 'like', $like));
                if (ctype_digit($q)) {
                    $id = (int) $q;
                    $sub->orWhere('order_id', $id)
                        ->orWhere('id', $id);
                }
            });
        }

        $summary = [
            'operations_count' => (clone $base)->count(),
            'gross_total' => (float) (clone $base)->sum('amount'),
            'fees_total' => (float) (clone $base)->sum('gateway_fee_amount'),
        ];
        $summary['net_after_fees'] = round($summary['gross_total'] - $summary['fees_total'], 2);

        $byGateway = (clone $base)
            ->selectRaw('payment_gateway, COUNT(*) as cnt, SUM(amount) as gross, SUM(COALESCE(gateway_fee_amount, 0)) as fees')
            ->groupBy('payment_gateway')
            ->orderByDesc('gross')
            ->get();

        $payments = (clone $base)
            ->with(['invoice', 'user', 'order', 'transactions'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(35)
            ->withQueryString();

        $gatewayOptions = ['kashier', 'fawaterak', 'moyasar', 'stripe', 'paypal', 'other'];

        return view('admin.accounting.gateway-operations', compact(
            'payments',
            'summary',
            'byGateway',
            'gatewayOptions',
            'gateway',
            'from',
            'to',
            'q'
        ));
    }
}
