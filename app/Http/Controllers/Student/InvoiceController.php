<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->id())
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => $invoices->total(),
            'pending' => Invoice::where('user_id', auth()->id())
                ->where('status', 'pending')->count(),
            'paid' => Invoice::where('user_id', auth()->id())
                ->where('status', 'paid')->count(),
        ];

        return view('student.invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load(['payments', 'enrollments.course']);
        return view('student.invoices.show', compact('invoice'));
    }
}
