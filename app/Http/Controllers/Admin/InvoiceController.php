<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * عرض قائمة الفواتير
     * محمي من: XSS, SQL Injection, Brute Force
     */
    public function index(Request $request)
    {
        try {
            // التحقق من الصلاحيات
            if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
                abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
            }

            $query = Invoice::with('user')
                ->orderBy('created_at', 'desc');

            // فلترة حسب الحالة - حماية من SQL Injection
            if ($request->filled('status')) {
                $status = strip_tags(trim($request->status));
                $status = preg_replace('/[^a-z_]/', '', $status); // السماح فقط بالأحرف الصغيرة والشرطة السفلية
                if (in_array($status, ['pending', 'paid', 'overdue', 'cancelled'])) {
                    $query->where('status', $status);
                }
            }

            // البحث - حماية من XSS و SQL Injection
            if ($request->filled('search')) {
                $search = strip_tags(trim($request->search));
                $search = preg_replace('/[^a-zA-Z0-9\u0600-\u06FF\s@.-]/', '', $search);
                if (strlen($search) > 0 && strlen($search) <= 255) {
                    $query->where(function($q) use ($search) {
                        $q->where('invoice_number', 'like', "%{$search}%")
                          ->orWhereHas('user', function($uq) use ($search) {
                              $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                    });
                }
            }

            $invoices = $query->paginate(20);

            // إحصائيات سريعة
            $stats = [
                'total' => Invoice::count(),
                'pending' => Invoice::pending()->count(),
                'paid' => Invoice::paid()->count(),
                'overdue' => Invoice::overdue()->count(),
            ];

            return view('admin.invoices.index', compact('invoices', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error in InvoiceController@index: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function create()
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.invoices.create', compact('users'));
    }

    /**
     * حفظ فاتورة جديدة
     * محمي من: XSS, SQL Injection, Mass Assignment, Brute Force
     */
    public function store(Request $request)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'create-invoice:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'لقد قمت بمحاولات كثيرة. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.');
        }
        RateLimiter::hit($key, 60);

        try {
            DB::beginTransaction();

            // Sanitization
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'type' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'subtotal' => 'required|numeric|min:0|max:99999999.99',
                'tax_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'discount_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'due_date' => 'nullable|date|after_or_equal:today',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Additional sanitization
            $validated['description'] = $validated['description'] ? strip_tags(trim($validated['description'])) : null;
            $validated['type'] = strip_tags(trim($validated['type']));
            $validated['notes'] = $validated['notes'] ? strip_tags(trim($validated['notes'])) : null;

            $total = $validated['subtotal'] 
                + ($validated['tax_amount'] ?? 0) 
                - ($validated['discount_amount'] ?? 0);

            // Mass Assignment Protection - استخدام fillable فقط
            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT),
                'user_id' => (int) $validated['user_id'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'subtotal' => (float) $validated['subtotal'],
                'tax_amount' => (float) ($validated['tax_amount'] ?? 0),
                'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
                'total_amount' => (float) $total,
                'status' => 'pending',
                'due_date' => $validated['due_date'] ? date('Y-m-d', strtotime($validated['due_date'])) : now()->addDays(30)->format('Y-m-d'),
                'notes' => $validated['notes'],
                'currency' => 'EGP',
            ]);

            // Activity Logging
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'model_type' => 'Invoice',
                'model_id' => $invoice->id,
                'description' => 'تم إنشاء فاتورة جديدة: ' . $invoice->invoice_number,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@store: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إنشاء الفاتورة')->withInput();
        }
    }

    /**
     * عرض تفاصيل الفاتورة
     * محمي من: XSS
     */
    public function show(Invoice $invoice)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        try {
            $invoice->load('user', 'payments', 'transactions', 'order', 'subscription', 'expense');
            return view('admin.invoices.show', compact('invoice'));
        } catch (\Exception $e) {
            Log::error('Error in InvoiceController@show: ' . $e->getMessage());
            abort(500, 'حدث خطأ أثناء تحميل الصفحة');
        }
    }

    public function edit(Invoice $invoice)
    {
        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.invoices.edit', compact('invoice', 'users'));
    }

    /**
     * تحديث فاتورة
     * محمي من: XSS, SQL Injection, Mass Assignment, Brute Force
     */
    public function update(Request $request, Invoice $invoice)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // Rate Limiting
        $key = 'update-invoice:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'لقد قمت بمحاولات كثيرة. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.');
        }
        RateLimiter::hit($key, 60);

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'type' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'subtotal' => 'required|numeric|min:0|max:99999999.99',
                'tax_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'discount_amount' => 'nullable|numeric|min:0|max:99999999.99',
                'status' => 'required|in:pending,paid,overdue,cancelled',
                'due_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Additional sanitization
            $validated['description'] = $validated['description'] ? strip_tags(trim($validated['description'])) : null;
            $validated['type'] = strip_tags(trim($validated['type']));
            $validated['status'] = strip_tags(trim($validated['status']));
            $validated['notes'] = $validated['notes'] ? strip_tags(trim($validated['notes'])) : null;

            $total = $validated['subtotal'] 
                + ($validated['tax_amount'] ?? 0) 
                - ($validated['discount_amount'] ?? 0);

            // Mass Assignment Protection
            $invoice->update([
                'user_id' => (int) $validated['user_id'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'subtotal' => (float) $validated['subtotal'],
                'tax_amount' => (float) ($validated['tax_amount'] ?? 0),
                'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
                'total_amount' => (float) $total,
                'status' => $validated['status'],
                'due_date' => $validated['due_date'] ? date('Y-m-d', strtotime($validated['due_date'])) : null,
                'notes' => $validated['notes'],
            ]);

            // Activity Logging
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'model_type' => 'Invoice',
                'model_id' => $invoice->id,
                'description' => 'تم تحديث فاتورة: ' . $invoice->invoice_number,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم تحديث الفاتورة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@update: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء تحديث الفاتورة')->withInput();
        }
    }

    /**
     * حذف فاتورة
     * محمي من: Brute Force
     */
    public function destroy(Invoice $invoice)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // Rate Limiting
        $key = 'delete-invoice:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', 'لقد قمت بمحاولات كثيرة. يرجى المحاولة بعد ' . ceil($seconds / 60) . ' دقيقة.');
        }
        RateLimiter::hit($key, 300);

        try {
            DB::beginTransaction();

            $invoiceNumber = $invoice->invoice_number;
            $invoice->delete();

            // Activity Logging
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'model_type' => 'Invoice',
                'model_id' => $invoice->id,
                'description' => 'تم حذف فاتورة: ' . $invoiceNumber,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم حذف الفاتورة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@destroy: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حذف الفاتورة');
        }
    }
}
