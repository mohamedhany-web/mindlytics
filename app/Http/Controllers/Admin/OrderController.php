<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\StudentCourseEnrollment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class OrderController extends Controller
{
    /**
     * عرض قائمة الطلبات
     * محمي من: XSS, SQL Injection
     */
    public function index(Request $request)
    {
        // التحقق من الصلاحيات (مدير عام أو من لديه صلاحية إدارة الطلبات)
        if (!Auth::check() || !(Auth::user()->isSuperAdmin() || Auth::user()->can('manage.orders'))) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $query = Order::with(['user', 'course.academicSubject', 'course.academicYear', 'learningPath']);

        // فلترة حسب الحالة - حماية من SQL Injection
        if ($request->filled('status')) {
            $status = strip_tags(trim($request->status));
            if (in_array($status, ['pending', 'approved', 'rejected'])) {
                $query->where('status', $status);
            }
        }

        // فلترة حسب طريقة الدفع - حماية من SQL Injection
        if ($request->filled('payment_method')) {
            $paymentMethod = strip_tags(trim($request->payment_method));
            if (in_array($paymentMethod, ['bank_transfer', 'cash', 'online', 'other'])) {
                $query->where('payment_method', $paymentMethod);
            }
        }

        // البحث - حماية من XSS و SQL Injection
        if ($request->filled('search')) {
            $search = strip_tags(trim($request->search));
            $search = preg_replace('/[^a-zA-Z0-9\u0600-\u06FF\s@.-]/', '', $search); // السماح فقط بالأحرف والأرقام العربية والإنجليزية
            if (strlen($search) > 0 && strlen($search) <= 255) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%");
                    })->orWhereHas('course', function($cq) use ($search) {
                        $cq->where('title', 'like', "%{$search}%");
                    })->orWhereHas('learningPath', function($lq) use ($search) {
                        $lq->where('name', 'like', "%{$search}%");
                    });
                });
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total' => Order::count(),
            'pending' => Order::pending()->count(),
            'approved' => Order::approved()->count(),
            'rejected' => Order::rejected()->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * عرض تفاصيل الطلب
     * محمي من: Unauthorized Access
     */
    public function show(Order $order)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !(Auth::user()->isSuperAdmin() || Auth::user()->can('manage.orders'))) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        $order->load(['user', 'course.academicSubject', 'course.academicYear', 'learningPath', 'approver']);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * الموافقة على الطلب
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Race Conditions
     */
    public function approve(Request $request, Order $order)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !(Auth::user()->isSuperAdmin() || Auth::user()->can('manage.orders'))) {
            abort(403, 'غير مصرح لك بالموافقة على الطلبات');
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'order_approve_' . Auth::id();
        $maxAttempts = 10;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $msg = "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $msg], 429);
            }
            return back()->with('error', $msg);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        // التحقق من حالة الطلب
        if ($order->status !== Order::STATUS_PENDING) {
            RateLimiter::clear($key);
            $msg = 'لا يمكن الموافقة على هذا الطلب';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        try {
            DB::beginTransaction();

            // إعادة تحميل الطلب لتجنب Race Conditions
            $order->refresh();
            
            // التحقق مرة أخرى بعد إعادة التحميل
            if ($order->status !== Order::STATUS_PENDING) {
                DB::rollBack();
                RateLimiter::clear($key);
                $msg = 'تم تعديل حالة الطلب بالفعل';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            // التحقق من عدم وجود فاتورة للطلب مسبقاً
            if ($order->invoice_id) {
                DB::rollBack();
                RateLimiter::clear($key);
                $msg = 'تم إنشاء فاتورة لهذا الطلب مسبقاً';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            // تحديد نوع الطلب (كورس أو مسار)
            $isLearningPath = !empty($order->academic_year_id);
            $orderTitle = '';
            $orderType = 'course';
            
            if ($isLearningPath) {
                if (!$order->relationLoaded('learningPath')) {
                    $order->load('learningPath');
                }
                $learningPath = $order->learningPath;
                $orderTitle = $learningPath ? htmlspecialchars($learningPath->name ?? 'مسار تعليمي', ENT_QUOTES, 'UTF-8') : 'مسار تعليمي (محذوف)';
                $orderType = 'learning_path';
            } else {
                if (!$order->relationLoaded('course')) {
                    $order->load('course');
                }
                $orderTitle = $order->course ? htmlspecialchars($order->course->title ?? 'كورس', ENT_QUOTES, 'UTF-8') : 'كورس (محذوف أو غير موجود)';
            }

            // إنشاء الفاتورة تلقائياً
            $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $order->user_id,
                'type' => $orderType,
                'description' => $isLearningPath ? 'فاتورة تسجيل في المسار التعليمي: ' . $orderTitle : 'فاتورة تسجيل في الكورس: ' . $orderTitle,
                'subtotal' => $order->amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $order->amount,
                'status' => 'paid', // تم الدفع لأنه تم قبول الطلب
                'due_date' => now(),
                'paid_at' => now(),
                'notes' => 'فاتورة مسبقة الدفع - من طلب رقم: ' . $order->id,
                'items' => [
                    [
                        'description' => $isLearningPath ? 'تسجيل في المسار التعليمي: ' . $orderTitle : 'تسجيل في الكورس: ' . $orderTitle,
                        'quantity' => 1,
                        'price' => $order->amount,
                        'total' => $order->amount,
                    ]
                ],
            ]);

            // إنشاء المدفوعات تلقائياً
            $paymentNumber = 'PAY-' . str_pad(Payment::count() + 1, 8, '0', STR_PAD_LEFT);
            
            // تحويل طريقة الدفع من order إلى payment
            $paymentMethodMap = [
                'bank_transfer' => 'bank_transfer',
                'cash' => 'cash',
                'other' => 'other',
            ];
            $paymentMethod = $paymentMethodMap[$order->payment_method] ?? 'other';

            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'user_id' => $order->user_id,
                'payment_method' => $paymentMethod,
                'amount' => $order->amount,
                'currency' => 'EGP',
                'status' => 'completed',
                'paid_at' => now(),
                'processed_by' => auth()->id(),
                'notes' => 'دفعة من طلب رقم: ' . $order->id . ($order->wallet_id ? ' - محفظة: ' . $order->wallet_id : ''),
            ]);

            // ربط المدفوعات بالمحفظة إذا كانت موجودة وإضافة المبلغ للمحفظة
            $wallet = null;
            if ($order->wallet_id) {
                $payment->update([
                    'wallet_id' => $order->wallet_id,
                ]);

                // إضافة المبلغ للمحفظة (إيداع)
                $wallet = \App\Models\Wallet::find($order->wallet_id);
                if ($wallet) {
                    try {
                        $description = 'إيداع من طلب رقم: ' . $order->id . ' - فاتورة: ' . $invoice->invoice_number;
                        if ($isLearningPath) {
                            $description .= ' - المسار: ' . ($order->learningPath?->name ?? 'مسار تعليمي');
                        } else {
                            $description .= ' - الكورس: ' . ($order->course?->title ?? 'كورس');
                        }
                        $wallet->deposit(
                            $order->amount,
                            $payment->id,
                            null, // transaction_id سيتم ربطه لاحقاً
                            $description
                        );
                    } catch (\Exception $e) {
                        \Log::error('Error depositing to wallet: ' . $e->getMessage());
                        // لا نوقف العملية في حالة فشل الإيداع
                    }
                }
            }

            // إنشاء معاملة مالية (إيراد)
            $transactionNumber = 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT);
            $transactionDescription = $isLearningPath 
                ? 'دفعة مقابل تسجيل في المسار التعليمي: ' . ($order->learningPath?->name ?? 'مسار تعليمي')
                : 'دفعة مقابل تسجيل في الكورس: ' . ($order->course?->title ?? 'كورس');
            $transactionDescription .= ' - طلب رقم: ' . $order->id . ' - فاتورة: ' . $invoice->invoice_number . ($wallet ? ' - محفظة: ' . (optional($wallet)->name ?? $wallet->id) : '');
            
            $transaction = Transaction::create([
                'transaction_number' => $transactionNumber,
                'user_id' => $order->user_id,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'expense_id' => null,
                'subscription_id' => null,
                'type' => 'credit', // دائن (إيراد)
                'category' => 'course_payment', // المسار والكورس يستخدمان نفس التصنيف (الجدول لا يدعم learning_path_payment)
                'amount' => $order->amount,
                'currency' => 'EGP',
                'description' => $transactionDescription,
                'status' => 'completed',
                'metadata' => [
                    'order_id' => $order->id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'course_id' => $order->advanced_course_id,
                    'academic_year_id' => $order->academic_year_id,
                    'wallet_id' => $order->wallet_id,
                ],
                'created_by' => auth()->id(),
            ]);

            // ربط معاملة المحفظة بالمعاملة المالية إذا كانت موجودة
            if ($wallet) {
                $walletTransaction = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                    ->where('payment_id', $payment->id)
                    ->where('type', 'deposit')
                    ->latest()
                    ->first();
                
                if ($walletTransaction) {
                    $walletTransaction->update(['transaction_id' => $transaction->id]);
                }
            }

            // تحديث حالة الطلب وربطه بالفاتورة والمدفوعات
            $order->update([
                'status' => Order::STATUS_APPROVED,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
            ]);

            // تحديث حالة الإحالة إذا كانت موجودة (لا نوقف الموافقة إذا فشل)
            try {
                $referralService = app(\App\Services\ReferralService::class);
                $referral = \App\Models\Referral::where('referred_id', $order->user_id)
                    ->where('status', \App\Models\Referral::STATUS_PENDING)
                    ->first();
                
                if ($referral) {
                    $referralService->markReferralAsCompleted($referral, $order->amount);
                }
            } catch (\Exception $e) {
                \Log::warning('Referral update failed during order approval: ' . $e->getMessage(), ['order_id' => $order->id]);
            }

            // إذا كان الطلب للمسار التعليمي، تسجيل الطالب في المسار (لا نوقف الموافقة إذا فشل)
            if ($order->academic_year_id) {
                try {
                    $existingPathEnrollment = \App\Models\LearningPathEnrollment::where('user_id', $order->user_id)
                        ->where('academic_year_id', $order->academic_year_id)
                        ->first();

                    if (!$existingPathEnrollment) {
                        $pathEnrollment = \App\Models\LearningPathEnrollment::create([
                            'user_id' => $order->user_id,
                            'academic_year_id' => $order->academic_year_id,
                            'status' => 'active',
                            'enrolled_at' => now(),
                            'activated_at' => now(),
                            'activated_by' => auth()->id(),
                            'progress' => 0,
                        ]);
                        $this->enrollInPathCourses($pathEnrollment);
                    } else {
                        if ($existingPathEnrollment->status !== 'active') {
                            $existingPathEnrollment->update([
                                'status' => 'active',
                                'activated_at' => now(),
                                'activated_by' => auth()->id(),
                            ]);
                            $this->enrollInPathCourses($existingPathEnrollment);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Learning path enrollment failed during order approval: ' . $e->getMessage(), [
                        'order_id' => $order->id,
                        'academic_year_id' => $order->academic_year_id,
                    ]);
                }
            }

            // إذا كان الطلب للكورس، تسجيل الطالب في الكورس (لا نوقف الموافقة إذا فشل)
            if ($order->advanced_course_id) {
                try {
                    $existingEnrollment = StudentCourseEnrollment::where('user_id', $order->user_id)
                        ->where('advanced_course_id', $order->advanced_course_id)
                        ->first();

                    if (!$existingEnrollment) {
                        StudentCourseEnrollment::create([
                            'user_id' => $order->user_id,
                            'advanced_course_id' => $order->advanced_course_id,
                            'enrolled_at' => now(),
                            'activated_at' => now(),
                            'activated_by' => auth()->id(),
                            'status' => 'active',
                            'progress' => 0,
                            'invoice_id' => $invoice->id,
                            'payment_id' => $payment->id,
                            'payment_method' => $paymentMethod,
                            'final_price' => $order->amount,
                        ]);
                    } else {
                        $existingEnrollment->update([
                            'status' => 'active',
                            'activated_at' => now(),
                            'activated_by' => auth()->id(),
                            'invoice_id' => $invoice->id,
                            'payment_id' => $payment->id,
                            'payment_method' => $paymentMethod,
                            'final_price' => $order->amount,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Course enrollment failed during order approval: ' . $e->getMessage(), [
                        'order_id' => $order->id,
                        'advanced_course_id' => $order->advanced_course_id,
                    ]);
                }
            }

            // تسجيل النشاط (قيم مختصرة فقط لتجنب استهلاك الذاكرة أو دوائر في العلاقات)
            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'order_approved',
                    'model_type' => 'Order',
                    'model_id' => $order->id,
                    'new_values' => [
                        'id' => $order->id,
                        'user_id' => $order->user_id,
                        'status' => $order->status,
                        'approved_at' => $order->approved_at ? $order->approved_at->format('Y-m-d H:i:s') : null,
                        'approved_by' => $order->approved_by,
                        'invoice_id' => $order->invoice_id,
                        'payment_id' => $order->payment_id,
                        'amount' => (float) $order->amount,
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to create activity log: ' . $e->getMessage());
            }

            // التأكد من أن كل شيء تم بنجاح قبل الـ commit
            DB::commit();
            RateLimiter::clear($key);

            \Log::info('Order approved successfully', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'is_learning_path' => $isLearningPath,
                'invoice_number' => $invoice->invoice_number ?? null,
                'payment_number' => $payment->payment_number ?? null,
            ]);

            $successMessage = $isLearningPath 
                ? 'تمت الموافقة على الطلب وتم تسجيل الطالب في المسار التعليمي وتفعيل جميع الكورسات. تم إنشاء الفاتورة رقم: ' . $invoice->invoice_number . ' والمدفوعات رقم: ' . $payment->payment_number
                : 'تمت الموافقة على الطلب وتم تفعيل الكورس للطالب. تم إنشاء الفاتورة رقم: ' . $invoice->invoice_number . ' والمدفوعات رقم: ' . $payment->payment_number;

            // إذا كان الطلب AJAX، إرجاع JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'redirect' => route('admin.orders.show', $order)
                ]);
            }

            return back()->with('success', $successMessage);

        } catch (\Throwable $e) {
            DB::rollBack();
            RateLimiter::clear($key);

            Log::error('Error approving order: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'order_status' => $order->status ?? 'unknown',
                'order_academic_year_id' => $order->academic_year_id ?? null,
                'order_advanced_course_id' => $order->advanced_course_id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMsg = $e->getMessage() ?: 'حدث خطأ غير متوقع';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], 500);
            }
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب: ' . $errorMsg);
        }
    }

    /**
     * رفض الطلب
     * محمي من: XSS, SQL Injection, CSRF, Brute Force, Race Conditions
     */
    public function reject(Request $request, Order $order)
    {
        // التحقق من الصلاحيات
        if (!Auth::check() || !(Auth::user()->isSuperAdmin() || Auth::user()->can('manage.orders'))) {
            abort(403, 'غير مصرح لك برفض الطلبات');
        }

        // Rate Limiting - حماية من Brute Force
        $key = 'order_reject_' . Auth::id();
        $maxAttempts = 10;
        $decayMinutes = 1;

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $msg = "تم تجاوز عدد المحاولات المسموح. يرجى المحاولة بعد {$seconds} ثانية.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $msg], 429);
            }
            return back()->with('error', $msg);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        if ($order->status !== Order::STATUS_PENDING) {
            RateLimiter::clear($key);
            $msg = 'لا يمكن رفض هذا الطلب';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $msg], 400);
            }
            return back()->with('error', $msg);
        }

        try {
            DB::beginTransaction();

            $order->refresh();

            if ($order->status !== Order::STATUS_PENDING) {
                DB::rollBack();
                RateLimiter::clear($key);
                $msg = 'تم تعديل حالة الطلب بالفعل';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'error' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            $rejectionReason = strip_tags(trim((string) $request->input('rejection_reason', '')));
            $updateData = [
                'status' => Order::STATUS_REJECTED,
                'approved_by' => Auth::id(),
            ];
            if ($rejectionReason !== '') {
                $updateData['notes'] = trim(($order->notes ? $order->notes . "\n" : '') . 'سبب الرفض: ' . $rejectionReason);
            }
            $order->update($updateData);

            try {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'order_rejected',
                    'model_type' => 'Order',
                    'model_id' => $order->id,
                    'new_values' => [
                        'id' => $order->id,
                        'user_id' => $order->user_id,
                        'status' => $order->status,
                        'approved_by' => $order->approved_by,
                    ],
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ]);
            } catch (\Throwable $logEx) {
                Log::warning('Activity log failed on reject: ' . $logEx->getMessage());
            }

            DB::commit();
            RateLimiter::clear($key);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفض الطلب بنجاح',
                    'redirect' => route('admin.orders.show', $order),
                ]);
            }
            return back()->with('success', 'تم رفض الطلب بنجاح');

        } catch (\Throwable $e) {
            DB::rollBack();
            RateLimiter::clear($key);

            Log::error('Error rejecting order: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $errorMsg = $e->getMessage() ?: 'حدث خطأ غير متوقع';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $errorMsg, 'message' => $errorMsg], 500);
            }
            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * تسجيل الطالب في جميع الكورسات في المسار (المجانية والمدفوعة)
     * نفس الطريقة المستخدمة في LearningPathEnrollmentController بالضبط
     */
    private function enrollInPathCourses(\App\Models\LearningPathEnrollment $enrollment)
    {
        // تحميل المسار مع العلاقات المطلوبة (نفس الطريقة المستخدمة في LearningPathEnrollmentController)
        $learningPath = $enrollment->learningPath()->with(['linkedCourses', 'academicSubjects'])->first();
        
        if (!$learningPath) {
            return;
        }
        
        // جمع الكورسات من المسار
        $courses = collect();
        
        // الكورسات المرتبطة مباشرة
        // تحديد الجدول بشكل صريح لتجنب مشكلة ambiguous column
        $linkedCourses = $learningPath->linkedCourses()->where('advanced_courses.is_active', true)->get();
        $courses = $courses->merge($linkedCourses);
        
        // الكورسات من المواد الدراسية
        $subjectCourses = $learningPath->academicSubjects->flatMap(function($subject) {
            return $subject->advancedCourses()->where('is_active', true)->get();
        });
        
        $courses = $courses->merge($subjectCourses)->unique('id');

        // تسجيل الطالب في جميع الكورسات (المجانية والمدفوعة)
        foreach ($courses as $course) {
            StudentCourseEnrollment::firstOrCreate(
                [
                    'user_id' => $enrollment->user_id,
                    'advanced_course_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                    'progress' => 0,
                ]
            );
        }
    }
}