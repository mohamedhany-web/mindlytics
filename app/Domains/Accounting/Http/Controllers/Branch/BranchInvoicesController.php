<?php

namespace App\Domains\Accounting\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Domains\Accounting\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchInvoicesController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = Invoice::query()
            ->where('branch_id', $branch->id)
            ->with('user')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        $invoices = $q->paginate(25)->withQueryString();

        return view('accounting::branch.invoices', compact('branch', 'invoices'));
    }
}
