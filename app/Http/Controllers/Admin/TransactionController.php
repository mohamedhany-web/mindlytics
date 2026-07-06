<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\ActivityLog;
use App\Services\TransactionRefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * عرض قائمة المعاملات المالية
     * محمي من: XSS, SQL Injection, Brute Force
     */
    public function index(Request $request)
    {
        try {
            // التحقق من الصلاحيات
            if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
                abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
            }

            $query = Transaction::with(['user', 'payment', 'invoice', 'expense', 'subscription'])
                ->orderBy('created_at', 'desc');

            // فلترة حسب الحالة - حماية من SQL Injection
            if ($request->filled('status')) {
                $status = strip_tags(trim($request->status));
                $status = preg_replace('/[^a-z_]/', '', $status);
                if (in_array($status, ['pending', 'completed', 'failed', 'cancelled'])) {
                    $query->where('status', $status);
                }
            }

            // فلترة حسب النوع - حماية من SQL Injection
            if ($request->filled('type')) {
                $type = strip_tags(trim($request->type));
                $type = preg_replace('/[^a-z_]/', '', $type);
                if (in_array($type, ['credit', 'debit', 'income', 'expense', 'transfer', 'refund'])) {
                    $query->where('type', $type);
                }
            }

            // البحث - حماية من XSS و SQL Injection
            if ($request->filled('search')) {
                $search = strip_tags(trim($request->search));
                $search = preg_replace('/[^a-zA-Z0-9\u0600-\u06FF\s@.-]/', '', $search);
                if (strlen($search) > 0 && strlen($search) <= 255) {
                    $query->where(function($q) use ($search) {
                        $q->where('transaction_number', 'like', "%{$search}%")
                          ->orWhereHas('user', function($uq) use ($search) {
                              $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                    });
                }
            }

            $transactions = $query->paginate(20);

            // إحصائيات سريعة
            $stats = [
                'total' => Transaction::count(),
                'total_amount' => Transaction::where('status', 'completed')->sum('amount'),
                'pending' => Transaction::where('status', 'pending')->count(),
                'completed' => Transaction::where('status', 'completed')->count(),
            ];

            return view('admin.transactions.index', compact('transactions', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in TransactionController@index: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user', 'payment', 'invoice', 'expense', 'subscription', 'createdBy');
        $refundService = app(TransactionRefundService::class);
        $canRefund = $refundService->canRefund($transaction);
        $needsWalletSync = $refundService->needsWalletWithdrawalSync($transaction);
        $suggestedWalletId = $refundService->suggestedWalletId($transaction);
        $academyWallets = Wallet::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'balance']);

        return view('admin.transactions.show', compact(
            'transaction',
            'canRefund',
            'needsWalletSync',
            'suggestedWalletId',
            'academyWallets',
        ));
    }

    public function create()
    {
        $users = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get();
        return view('admin.transactions.create', compact('users'));
    }

    public function edit(Transaction $transaction)
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        $refundService = app(TransactionRefundService::class);
        $canRefund = $refundService->canRefund($transaction);
        $needsWalletSync = $refundService->needsWalletWithdrawalSync($transaction);
        $suggestedWalletId = $refundService->suggestedWalletId($transaction);
        $academyWallets = Wallet::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'balance']);

        return view('admin.transactions.edit', compact(
            'transaction',
            'users',
            'canRefund',
            'needsWalletSync',
            'suggestedWalletId',
            'academyWallets',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:credit,debit',
            'category' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'payment_id' => 'nullable|exists:payments,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'expense_id' => 'nullable|exists:expenses,id',
            'subscription_id' => 'nullable|exists:subscriptions,id',
        ]);

        Transaction::create([
            'transaction_number' => 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT),
            'user_id' => $validated['user_id'],
            'payment_id' => $validated['payment_id'] ?? null,
            'invoice_id' => $validated['invoice_id'] ?? null,
            'expense_id' => $validated['expense_id'] ?? null,
            'subscription_id' => $validated['subscription_id'] ?? null,
            'type' => $validated['type'],
            'category' => $validated['category'] ?? 'other',
            'amount' => $validated['amount'],
            'currency' => 'EGP',
            'description' => $validated['description'] ?? 'معاملة مالية',
            'status' => 'completed',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'تم إنشاء المعاملة بنجاح');
    }

    public function update(Request $request, Transaction $transaction)
    {
        if ($request->boolean('process_refund') || $request->input('type') === 'refund') {
            return $this->refund($request, $transaction);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled,reversed',
            'description' => 'nullable|string',
        ]);

        if ($transaction->status === 'reversed') {
            return back()->withErrors([
                'status' => 'لا يمكن تعديل معاملة تم استردادها. يمكنك فقط عرض التفاصيل.',
            ])->withInput();
        }

        if ($validated['status'] === 'reversed' && $transaction->status !== 'reversed') {
            return $this->refund($request->merge([
                'amount' => $validated['amount'],
                'description' => $validated['description'] ?? null,
            ]), $transaction);
        }

        $transaction->update($validated);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'تم تحديث المعاملة بنجاح');
    }

    public function refund(Request $request, Transaction $transaction)
    {
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بتنفيذ الاسترداد');
        }

        $validated = $request->validate([
            'amount' => 'nullable|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            $result = app(TransactionRefundService::class)->process(
                $transaction,
                isset($validated['amount']) ? (float) $validated['amount'] : null,
                $validated['description'] ?? null,
                auth()->id()
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            Log::error('Transaction refund failed: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'تعذر تنفيذ الاسترداد: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.transactions.show', $result['refund'])
            ->with('success', 'تم استرداد المبلغ بنجاح. تم سحبه من المحفظة المرتبطة (إن وُجدت) وتحديث الدفعة والفاتورة.');
    }

    public function syncWalletWithdrawal(Transaction $transaction)
    {
        if (! Auth::check() || ! Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بتنفيذ الاسترداد');
        }

        $validated = $request->validate([
            'wallet_id' => 'nullable|exists:wallets,id',
            'amount' => 'nullable|numeric|min:0.01',
        ]);

        try {
            $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
            $amount = isset($validated['amount'])
                ? (float) $validated['amount']
                : (float) ($metadata['refund_amount'] ?? $transaction->amount);

            $walletTxn = app(TransactionRefundService::class)->syncWalletWithdrawalIfMissing(
                $transaction,
                $amount,
                auth()->id(),
                isset($validated['wallet_id']) ? (int) $validated['wallet_id'] : null,
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Throwable $e) {
            Log::error('Wallet withdrawal sync failed: ' . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);

            return back()->with('error', 'تعذر سحب المبلغ من المحفظة: ' . $e->getMessage());
        }

        return back()->with('success', 'تم سحب ' . number_format((float) $walletTxn->amount, 2) . ' ج.م من المحفظة بنجاح.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('admin.transactions.index')
            ->with('success', 'تم حذف المعاملة بنجاح');
    }
}
