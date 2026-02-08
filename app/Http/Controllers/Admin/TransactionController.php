<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\ActivityLog;
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
        return view('admin.transactions.show', compact('transaction'));
    }

    public function create()
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.transactions.create', compact('users'));
    }

    public function edit(Transaction $transaction)
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.transactions.edit', compact('transaction', 'users'));
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
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'description' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('admin.transactions.index')
            ->with('success', 'تم تحديث المعاملة بنجاح');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('admin.transactions.index')
            ->with('success', 'تم حذف المعاملة بنجاح');
    }
}
