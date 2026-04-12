<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Wallet;
use App\Support\PlatformSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $platformPaymentMode = PlatformSettings::paymentMode();

        return view('student.invoices.show', compact('invoice', 'wallets', 'platformPaymentMode'));
    }

    public function storePaymentProof(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            abort(403);
        }

        if (PlatformSettings::paymentMode() !== 'manual') {
            return redirect()->route('student.invoices.show', $invoice)
                ->with('error', 'وضع الدفع الحالي لا يدعم رفع إيصال من هنا.');
        }

        if ($invoice->isPaid() || (float) $invoice->remaining_amount <= 0) {
            return redirect()->route('student.invoices.show', $invoice)
                ->with('info', 'لا يوجد مبلغ مستحق على هذه الفاتورة.');
        }

        if (Payment::query()
            ->where('invoice_id', $invoice->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists()) {
            return redirect()->route('student.invoices.show', $invoice)
                ->with('info', 'لديك طلب سداد قيد المراجعة لهذه الفاتورة.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', Rule::in(['bank_transfer', 'wallet'])],
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,wallet',
                Rule::exists('wallets', 'id')->whereNull('user_id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'payment_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'payment_proof.required' => 'صورة إيصال الدفع مطلوبة',
        ]);

        $remaining = (float) $invoice->remaining_amount;
        $amount = min((float) $validated['amount'], $remaining);

        DB::beginTransaction();
        try {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
            $paymentNumber = 'PAY-' . str_pad((string) (Payment::count() + 1), 8, '0', STR_PAD_LEFT);

            Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
                'payment_method' => $validated['payment_method'] === 'wallet' ? 'wallet' : 'bank_transfer',
                'payment_gateway' => 'manual',
                'wallet_id' => $validated['payment_method'] === 'wallet' ? ($validated['wallet_id'] ?? null) : ($validated['wallet_id'] ?: null),
                'amount' => $amount,
                'currency' => 'EGP',
                'status' => 'pending',
                'notes' => $validated['notes'] ?? '',
                'proof_path' => $proofPath,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Student invoice payment proof failed', ['invoice_id' => $invoice->id, 'message' => $e->getMessage()]);

            return redirect()->route('student.invoices.show', $invoice)
                ->with('error', 'تعذر حفظ الطلب. حاول مرة أخرى.');
        }

        return redirect()->route('student.invoices.show', $invoice)
            ->with('success', 'تم استلام طلب السداد مع الإيصال. سيتم المراجعة وتسجيل الدفعة بعد الموافقة.');
    }
}

