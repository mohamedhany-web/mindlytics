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

class InvoiceController extends Controller
{
    /**
     * عرض قائمة الفواتير
     * محمي من: XSS, SQL Injection, Brute Force
     */
    public function index(Request $request)
    {
        try {
            $this->assertCanManageInvoices();

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
        $this->assertCanManageInvoices();

        $users = User::where('role', 'student')->where('is_active', true)->orderBy('name')->get();
        return view('admin.invoices.create', compact('users'));
    }

    /**
     * حفظ فاتورة جديدة
     * محمي من: XSS, SQL Injection, Mass Assignment, Brute Force
     */
    public function store(Request $request)
    {
        $this->assertCanManageInvoices();

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

            // وصف غير فارغ دائماً (NOT NULL / وسيط الفراغ → null / تنظيف المدخلات قد يفرّغ النص)
            $rawDescription = $validated['description'] ?? null;
            $validated['description'] = ($rawDescription !== null && $rawDescription !== '')
                ? strip_tags(trim((string) $rawDescription))
                : '';
            if ($validated['description'] === '') {
                $validated['description'] = '-';
            }

            $validated['type'] = strip_tags(trim($validated['type']));
            $rawNotes = $validated['notes'] ?? null;
            $validated['notes'] = ($rawNotes !== null && $rawNotes !== '')
                ? strip_tags(trim((string) $rawNotes))
                : null;

            $total = $validated['subtotal']
                + ($validated['tax_amount'] ?? 0)
                - ($validated['discount_amount'] ?? 0);

            // إدراج عبر Query Builder لتفادي أي تعارض مع نسخ قديمة من Eloquent/أحداث النموذج
            $invoice = $this->insertInvoiceAsRow($validated, (float) $total);

            DB::commit();

            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'created',
                    'model_type' => 'Invoice',
                    'model_id' => $invoice->id,
                    'description' => 'تم إنشاء فاتورة جديدة: ' . $invoice->invoice_number,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $logEx) {
                Log::warning('InvoiceController@store: فشل تسجيل النشاط بعد إنشاء الفاتورة', [
                    'invoice_id' => $invoice->id,
                    'exception' => $logEx,
                ]);
            }

            return redirect()->route('admin.invoices.index')
                ->with('success', 'تم إنشاء الفاتورة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@store: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            @file_put_contents(
                storage_path('logs/invoice_store_last_error.txt'),
                '[' . now()->toIso8601String() . "] " . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString(),
                LOCK_EX
            );
            $message = 'حدث خطأ أثناء إنشاء الفاتورة';
            if (config('app.debug')) {
                $message .= ': ' . $e->getMessage();
            }

            return back()->with('error', $message)->withInput();
        }
    }

    /**
     * عرض تفاصيل الفاتورة
     * محمي من: XSS
     */
    public function show(Invoice $invoice)
    {
        $this->assertCanManageInvoices();

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
        $this->assertCanManageInvoices();

        $users = User::where('role', 'student')->where('is_active', true)->get();
        return view('admin.invoices.edit', compact('invoice', 'users'));
    }

    /**
     * تحديث فاتورة
     * محمي من: XSS, SQL Injection, Mass Assignment, Brute Force
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->assertCanManageInvoices();

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

            $rawDescription = $validated['description'] ?? null;
            $validated['description'] = ($rawDescription !== null && $rawDescription !== '')
                ? strip_tags(trim((string) $rawDescription))
                : '';
            if ($validated['description'] === '') {
                $validated['description'] = '-';
            }

            $validated['type'] = strip_tags(trim($validated['type']));
            $validated['status'] = strip_tags(trim($validated['status']));
            $rawNotes = $validated['notes'] ?? null;
            $validated['notes'] = ($rawNotes !== null && $rawNotes !== '')
                ? strip_tags(trim((string) $rawNotes))
                : null;

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
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error in InvoiceController@update: ' . $e->getMessage(), ['exception' => $e]);
            $message = 'حدث خطأ أثناء تحديث الفاتورة';
            if (config('app.debug')) {
                $message .= ': ' . $e->getMessage();
            }

            return back()->with('error', $message)->withInput();
        }
    }

    /**
     * حذف فاتورة
     * محمي من: Brute Force
     */
    public function destroy(Invoice $invoice)
    {
        $this->assertCanManageInvoices();

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

    /**
     * مسموح بإدارة الفواتير لمن يملك دور admin أو super_admin (مطابقة لـ Route::middleware role:admin|super_admin).
     */
    private function assertCanManageInvoices(): void
    {
        if (! Auth::check()) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));
        if ($role === 'admin' || Auth::user()->isSuperAdmin()) {
            return;
        }
        abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
    }

    /**
     * إنشاء صف الفاتورة عبر الـ Query Builder حتى يُحفَظ الوصف كنص صريح ولا يعتمد على أحداث Eloquent.
     */
    private function insertInvoiceAsRow(array $validated, float $totalAmount): Invoice
    {
        $description = (string) ($validated['description'] ?? '');
        $description = trim(strip_tags($description));
        if ($description === '') {
            $description = '-';
        }

        $dueDate = $validated['due_date']
            ? date('Y-m-d', strtotime($validated['due_date']))
            : now()->addDays(30)->format('Y-m-d');

        $now = now();
        $nextSeq = (int) DB::table('invoices')->count() + 1;
        $invoiceNumber = 'INV-' . str_pad((string) $nextSeq, 8, '0', STR_PAD_LEFT);

        $id = DB::table('invoices')->insertGetId([
            'invoice_number' => $invoiceNumber,
            'user_id' => (int) $validated['user_id'],
            'type' => $validated['type'],
            'description' => $description,
            'subtotal' => round((float) $validated['subtotal'], 2),
            'tax_amount' => round((float) ($validated['tax_amount'] ?? 0), 2),
            'discount_amount' => round((float) ($validated['discount_amount'] ?? 0), 2),
            'total_amount' => round($totalAmount, 2),
            'status' => 'pending',
            'due_date' => $dueDate,
            'notes' => $validated['notes'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return Invoice::query()->findOrFail($id);
    }
}
