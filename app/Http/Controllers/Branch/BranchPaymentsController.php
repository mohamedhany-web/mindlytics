<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchPaymentsController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = Payment::query()
            ->where('branch_id', $branch->id)
            ->with(['user', 'invoice', 'order'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        $payments = $q->paginate(25)->withQueryString();

        return view('branch-office.payments', compact('branch', 'payments'));
    }
}
