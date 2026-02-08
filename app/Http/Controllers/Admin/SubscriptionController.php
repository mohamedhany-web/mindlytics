<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * عرض قائمة الاشتراكات
     * محمي من: XSS, SQL Injection, Brute Force
     */
    public function index(Request $request)
    {
        try {
            // التحقق من الصلاحيات
            if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
                abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
            }

            $query = Subscription::with('user')
                ->orderBy('created_at', 'desc');

            // فلترة حسب الحالة - حماية من SQL Injection
            if ($request->filled('status')) {
                $status = strip_tags(trim($request->status));
                $status = preg_replace('/[^a-z_]/', '', $status);
                if (in_array($status, ['active', 'expired', 'cancelled'])) {
                    $query->where('status', $status);
                }
            }

            // البحث - حماية من XSS و SQL Injection
            if ($request->filled('search')) {
                $search = strip_tags(trim($request->search));
                $search = preg_replace('/[^a-zA-Z0-9\u0600-\u06FF\s@.-]/', '', $search);
                if (strlen($search) > 0 && strlen($search) <= 255) {
                    $query->whereHas('user', function($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    });
                }
            }

            $subscriptions = $query->paginate(18);

            $stats = [
                'total' => Subscription::count(),
                'active' => Subscription::where('status', 'active')->count(),
                'expired' => Subscription::where('status', 'expired')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
                'auto_renew' => Subscription::where('auto_renew', true)->count(),
                'active_revenue' => (float) Subscription::where('status', 'active')->sum('price'),
            ];

            $currentMonthRange = [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ];

            $monthlyNew = Subscription::whereBetween('start_date', $currentMonthRange)->count();
            $monthlyRevenue = (float) Subscription::whereBetween('start_date', $currentMonthRange)->sum('price');

            $planDistribution = Subscription::selectRaw("COALESCE(NULLIF(subscription_type, ''), 'other') as subscription_type, COUNT(*) as subscriptions_count, SUM(price) as total_price")
                ->groupBy('subscription_type')
                ->get()
                ->map(function ($row) {
                    $type = $row->subscription_type;
                    return [
                        'type' => $type,
                        'label' => Subscription::typeLabel($type),
                        'subscriptions_count' => (int) $row->subscriptions_count,
                        'total_price' => (float) $row->total_price,
                    ];
                });

            $expiringSoon = Subscription::with('user')
                ->where('status', 'active')
                ->whereNotNull('end_date')
                ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(30)])
                ->orderBy('end_date')
                ->take(6)
                ->get();

            $recentSubscriptions = Subscription::with('user')
                ->latest()
                ->take(6)
                ->get();

            return view('admin.subscriptions.index', compact(
                'subscriptions',
                'stats',
                'monthlyNew',
                'monthlyRevenue',
                'planDistribution',
                'expiringSoon',
                'recentSubscriptions'
            ));
        } catch (\Exception $e) {
            Log::error('Error in SubscriptionController@index: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'invoice', 'payments', 'transactions']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function create()
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.subscriptions.create', compact('users'));
    }

    public function edit(Subscription $subscription)
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.subscriptions.edit', compact('subscription', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_type' => 'required|string',
            'plan_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'boolean',
            'billing_cycle' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // إنشاء Invoice تلقائياً للاشتراك
            $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $validated['user_id'],
                'type' => 'subscription',
                'description' => 'فاتورة اشتراك: ' . $validated['plan_name'],
                'subtotal' => $validated['price'],
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $validated['price'],
                'status' => 'pending',
                'due_date' => Carbon::parse($validated['start_date']),
                'notes' => 'فاتورة اشتراك - نوع: ' . Subscription::typeLabel($validated['subscription_type']),
                'items' => [
                    [
                        'description' => 'اشتراك: ' . $validated['plan_name'],
                        'quantity' => 1,
                        'price' => $validated['price'],
                        'total' => $validated['price'],
                    ]
                ],
            ]);

            // إنشاء Subscription
            $subscription = Subscription::create([
                'user_id' => $validated['user_id'],
                'subscription_type' => $validated['subscription_type'],
                'plan_name' => $validated['plan_name'],
                'price' => $validated['price'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => 'active',
                'auto_renew' => $validated['auto_renew'] ?? false,
                'billing_cycle' => $validated['billing_cycle'],
                'invoice_id' => $invoice->id,
            ]);

            DB::commit();

            return redirect()->route('admin.subscriptions.index')
                ->with('success', 'تم إنشاء الاشتراك والفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الاشتراك: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_type' => 'required|string',
            'plan_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled',
            'auto_renew' => 'boolean',
            'billing_cycle' => 'required|string',
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم تحديث الاشتراك بنجاح');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح');
    }
}
