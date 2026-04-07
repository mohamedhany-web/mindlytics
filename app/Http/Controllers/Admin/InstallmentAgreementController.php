<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\InstallmentAgreement;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\OfflineCourseGroup;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Support\OfflineEnrollmentProvisioner;
use App\Services\InstructorCoursePercentageService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallmentAgreementController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage.installments');
    }

    public function index(Request $request): View
    {
        $base = $this->filteredAgreementsQuery($request);

        $summary = [
            'total_count' => (clone $base)->count(),
            'active' => (clone $base)->where('status', InstallmentAgreement::STATUS_ACTIVE)->count(),
            'overdue' => (clone $base)->where('status', InstallmentAgreement::STATUS_OVERDUE)->count(),
            'completed' => (clone $base)->where('status', InstallmentAgreement::STATUS_COMPLETED)->count(),
            'total_amount' => (float) (clone $base)->sum('total_amount'),
            'deposit_amount' => (float) (clone $base)->sum('deposit_amount'),
        ];

        $agreements = (clone $base)
            ->with(['student', 'course', 'offlineEnrollment.course', 'plan', 'payments'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = $this->statusOptions();
        $courseTypes = [
            '' => 'كل أنواع الكورسات',
            'online' => 'كورس أونلاين (تسجيل منصة)',
            'offline' => 'كورس أوفلاين (حضوري)',
        ];

        return view('admin.installments.agreements.index', compact('agreements', 'statuses', 'summary', 'courseTypes'));
    }

    protected function filteredAgreementsQuery(Request $request): Builder
    {
        return InstallmentAgreement::query()
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->input('status')))
            ->when($request->input('course_type') === 'online', fn (Builder $q) => $q->whereNotNull('student_course_enrollment_id'))
            ->when($request->input('course_type') === 'offline', fn (Builder $q) => $q->whereNotNull('offline_course_enrollment_id'))
            ->when($request->filled('search'), function (Builder $q) use ($request) {
                $raw = $request->input('search');
                $term = '%' . addcslashes((string) $raw, '%_\\') . '%';
                $q->whereHas('student', function (Builder $s) use ($term) {
                    $s->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
            });
    }

    public function createManualBooking(Request $request): View
    {
        $plans = InstallmentPlan::active()->with('course')->orderBy('name')->get();
        $onlineCourses = AdvancedCourse::active()->orderBy('title')->get(['id', 'title', 'price']);
        $offlineCourses = OfflineCourse::active()->with(['groups' => fn ($q) => $q->where('is_active', true)->where('status', 'active')->orderBy('name')])->orderBy('title')->get(['id', 'title', 'price']);
        $statuses = $this->statusOptions();
        $selectedPlanId = $request->integer('plan_id');

        $offlineCoursesGroups = $offlineCourses->map(fn (OfflineCourse $c) => [
            'id' => $c->id,
            'groups' => $c->groups->map(fn (OfflineCourseGroup $g) => ['id' => $g->id, 'name' => $g->name])->values(),
        ])->values();

        return view('admin.installments.agreements.manual-booking', compact(
            'plans',
            'onlineCourses',
            'offlineCourses',
            'offlineCoursesGroups',
            'statuses',
            'selectedPlanId'
        ));
    }

    public function storeManualBooking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_email' => ['required', 'email'],
            'course_mode' => ['required', 'in:online,offline'],
            'installment_plan_id' => ['required', 'exists:installment_plans,id'],
            'advanced_course_id' => ['required_if:course_mode,online', 'nullable', 'exists:advanced_courses,id'],
            'offline_course_id' => ['required_if:course_mode,offline', 'nullable', 'exists:offline_courses,id'],
            'offline_group_id' => ['required_if:course_mode,offline', 'nullable', 'exists:offline_course_groups,id'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'start_date' => ['required', 'date'],
            'status' => ['nullable', 'in:' . implode(',', array_keys($this->statusOptions()))],
            'notes' => ['nullable', 'string'],
        ], [
            'student_email.required' => 'البريد الإلكتروني للطالب مطلوب.',
            'course_mode.required' => 'نوع الكورس (أونلاين / أوفلاين) مطلوب.',
        ]);

        $plan = InstallmentPlan::findOrFail($data['installment_plan_id']);
        if (! $plan->is_active) {
            return back()->withErrors(['installment_plan_id' => 'الخطة المختارة غير مفعّلة.'])->withInput();
        }

        $user = User::where('email', $data['student_email'])->first();
        if (! $user) {
            return back()->withErrors(['student_email' => 'لا يوجد حساب بهذا البريد. أنشئ حساب الطالب أولاً ثم أعد المحاولة.'])->withInput();
        }

        if ($data['course_mode'] === 'online') {
            if ($plan->advanced_course_id && (int) $plan->advanced_course_id !== (int) $data['advanced_course_id']) {
                return back()->withErrors(['installment_plan_id' => 'الخطة مربوطة بكورس أونلاين آخر. اختر خطة بدون كورس محدد أو تطابق الكورس.'])->withInput();
            }

            $advancedCourse = AdvancedCourse::findOrFail($data['advanced_course_id']);
            $enrollment = StudentCourseEnrollment::where('user_id', $user->id)
                ->where('advanced_course_id', $advancedCourse->id)
                ->first();

            if (! $enrollment) {
                $enrollment = StudentCourseEnrollment::create([
                    'user_id' => $user->id,
                    'advanced_course_id' => $advancedCourse->id,
                    'status' => 'active',
                    'notes' => trim('تسجيل يدوي مع تقسيط' . (! empty($data['notes'] ?? '') ? "\n" . $data['notes'] : '')),
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => $request->user()?->id,
                ]);
                InstructorCoursePercentageService::processEnrollmentActivation($enrollment->fresh());
            }

            if (InstallmentAgreement::where('student_course_enrollment_id', $enrollment->id)
                ->whereIn('status', [InstallmentAgreement::STATUS_ACTIVE, InstallmentAgreement::STATUS_OVERDUE])
                ->exists()) {
                return back()->withErrors(['student_email' => 'هناك خطة تقسيط نشطة بالفعل لهذا التسجيل الأونلاين.'])->withInput();
            }

            $totalAmount = $data['total_amount'] ?? $plan->total_amount ?? $advancedCourse->price ?? 0;
            $depositAmount = $data['deposit_amount'] ?? $plan->deposit_amount ?? 0;
            if ($totalAmount < $depositAmount) {
                return back()->withErrors(['deposit_amount' => 'الدفعة المقدمة أكبر من إجمالي المبلغ.'])->withInput();
            }

            $agreement = InstallmentAgreement::create([
                'installment_plan_id' => $plan->id,
                'student_course_enrollment_id' => $enrollment->id,
                'offline_course_enrollment_id' => null,
                'user_id' => $user->id,
                'advanced_course_id' => $advancedCourse->id,
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
                'installments_count' => $data['installments_count'] ?? $plan->installments_count,
                'start_date' => $data['start_date'],
                'status' => $data['status'] ?? InstallmentAgreement::STATUS_ACTIVE,
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()?->id,
            ]);
        } else {
            if ($plan->advanced_course_id !== null) {
                return back()->withErrors(['installment_plan_id' => 'للكورسات الأوفلاين استخدم خطة تقسيط غير مربوطة بكورس أونلاين (حقل الكورس في الخطة فارغ).'])->withInput();
            }

            $offlineCourse = OfflineCourse::findOrFail($data['offline_course_id']);
            $group = OfflineCourseGroup::where('id', $data['offline_group_id'])
                ->where('offline_course_id', $offlineCourse->id)
                ->first();
            if (! $group) {
                return back()->withErrors(['offline_group_id' => 'المجموعة لا تنتمي لهذا الكورس الأوفلاين.'])->withInput();
            }

            if (! $offlineCourse->canEnroll()) {
                return back()->withErrors(['offline_course_id' => 'الكورس الأوفلاين غير متاح للتسجيل أو وصل للحد الأقصى.'])->withInput();
            }
            if (! $group->canEnroll('offline')) {
                return back()->withErrors(['offline_group_id' => 'المجموعة غير متاحة أو ممتلئة.'])->withInput();
            }

            $existingOffline = OfflineCourseEnrollment::where('user_id', $user->id)
                ->where('offline_course_id', $offlineCourse->id)
                ->where('enrollment_channel', 'offline')
                ->first();

            if ($existingOffline) {
                if (InstallmentAgreement::where('offline_course_enrollment_id', $existingOffline->id)
                    ->whereIn('status', [InstallmentAgreement::STATUS_ACTIVE, InstallmentAgreement::STATUS_OVERDUE])
                    ->exists()) {
                    return back()->withErrors(['student_email' => 'هناك خطة تقسيط نشطة بالفعل لهذا التسجيل الأوفلاين.'])->withInput();
                }
                $offlineEnrollment = $existingOffline;
            } else {
                $offlineEnrollment = OfflineEnrollmentProvisioner::create(
                    $offlineCourse,
                    $user->id,
                    $group->id,
                    'active',
                    (float) $offlineCourse->price,
                    0.0,
                    ['payment_method' => 'installment', 'payment_notes' => 'تسجيل يدوي مع تقسيط'],
                    isset($data['notes']) ? 'تسجيل يدوي مع تقسيط' . "\n" . $data['notes'] : 'تسجيل يدوي مع تقسيط',
                    'offline'
                );
            }

            $totalAmount = $data['total_amount'] ?? $plan->total_amount ?? $offlineCourse->price ?? 0;
            $depositAmount = $data['deposit_amount'] ?? $plan->deposit_amount ?? 0;
            if ($totalAmount < $depositAmount) {
                return back()->withErrors(['deposit_amount' => 'الدفعة المقدمة أكبر من إجمالي المبلغ.'])->withInput();
            }

            $offlineEnrollment->total_amount = $totalAmount;
            $offlineEnrollment->remaining_amount = max(0, $totalAmount - (float) $offlineEnrollment->paid_amount);
            $offlineEnrollment->updatePaymentStatus();
            $offlineEnrollment->save();

            $agreement = InstallmentAgreement::create([
                'installment_plan_id' => $plan->id,
                'student_course_enrollment_id' => null,
                'offline_course_enrollment_id' => $offlineEnrollment->id,
                'user_id' => $user->id,
                'advanced_course_id' => null,
                'total_amount' => $totalAmount,
                'deposit_amount' => $depositAmount,
                'installments_count' => $data['installments_count'] ?? $plan->installments_count,
                'start_date' => $data['start_date'],
                'status' => $data['status'] ?? InstallmentAgreement::STATUS_ACTIVE,
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()?->id,
            ]);
        }

        $agreement->payments()->delete();
        $agreement->generateSchedule(Carbon::parse($agreement->start_date));

        return redirect()->route('admin.installments.agreements.show', $agreement)->with('success', 'تم إنشاء الحجز وخطة التقسيط بنجاح.');
    }

    public function create(Request $request): View
    {
        $plans = InstallmentPlan::active()->with('course')->orderBy('name')->get();
        $statuses = $this->statusOptions();
        $selectedPlanId = $request->integer('plan_id');

        $enrollmentsQuery = StudentCourseEnrollment::with(['student', 'course'])->latest();

        if ($request->filled('student')) {
            $term = $request->string('student');
            $enrollmentsQuery->whereHas('student', function (Builder $query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        if ($request->filled('course')) {
            $term = $request->string('course');
            $enrollmentsQuery->whereHas('course', function (Builder $query) use ($term) {
                $query->where('title', 'like', "%{$term}%");
            });
        }

        $enrollments = $enrollmentsQuery->limit(50)->get();

        return view('admin.installments.agreements.create', compact('plans', 'enrollments', 'statuses', 'selectedPlanId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $plan = InstallmentPlan::findOrFail($data['installment_plan_id']);
        $enrollment = StudentCourseEnrollment::with(['student', 'course'])->findOrFail($data['student_course_enrollment_id']);

        if (InstallmentAgreement::where('student_course_enrollment_id', $enrollment->id)
            ->whereIn('status', ['active', 'overdue'])
            ->exists()) {
            return back()->withErrors(['student_course_enrollment_id' => 'هناك خطة تقسيط نشطة بالفعل لهذا التسجيل.'])->withInput();
        }

        $totalAmount = $data['total_amount'] ?? $plan->total_amount ?? $enrollment->course?->price ?? 0;
        $depositAmount = $data['deposit_amount'] ?? $plan->deposit_amount ?? 0;

        if ($totalAmount < $depositAmount) {
            return back()->withErrors(['deposit_amount' => 'الدفعة المقدمة أكبر من إجمالي المبلغ.'])->withInput();
        }

        $agreement = InstallmentAgreement::create([
            'installment_plan_id' => $plan->id,
            'student_course_enrollment_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'advanced_course_id' => $enrollment->advanced_course_id,
            'total_amount' => $totalAmount,
            'deposit_amount' => $depositAmount,
            'installments_count' => $data['installments_count'] ?? $plan->installments_count,
            'start_date' => $data['start_date'],
            'status' => $data['status'] ?? InstallmentAgreement::STATUS_ACTIVE,
            'notes' => $data['notes'] ?? null,
            'created_by' => $request->user()?->id,
        ]);

        $agreement->payments()->delete();
        $agreement->generateSchedule(Carbon::parse($agreement->start_date));

        return redirect()->route('admin.installments.agreements.show', $agreement)->with('success', 'تم إنشاء اتفاقية التقسيط وجدول السداد بنجاح.');
    }

    public function show(InstallmentAgreement $agreement): View
    {
        $agreement->load(['student', 'course', 'offlineEnrollment.course', 'plan', 'payments' => function ($query) {
            $query->orderBy('sequence_number');
        }]);

        $statuses = $this->statusOptions();
        $paymentStatuses = $this->paymentStatuses();
        $frequencyUnits = $this->frequencyUnits();

        return view('admin.installments.agreements.show', compact('agreement', 'statuses', 'paymentStatuses', 'frequencyUnits'));
    }

    public function edit(InstallmentAgreement $agreement): View
    {
        $agreement->load(['student', 'course', 'offlineEnrollment.course']);
        $plans = InstallmentPlan::active()->with('course')->orderBy('name')->get();
        $statuses = $this->statusOptions();

        return view('admin.installments.agreements.edit', compact('agreement', 'plans', 'statuses'));
    }

    public function update(Request $request, InstallmentAgreement $agreement): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions()))],
            'notes' => ['nullable', 'string'],
        ]);

        $agreement->update($data);

        return back()->with('success', 'تم تحديث حالة الاتفاقية بنجاح.');
    }

    public function destroy(InstallmentAgreement $agreement): RedirectResponse
    {
        $agreement->update(['status' => InstallmentAgreement::STATUS_CANCELLED]);
        $agreement->payments()->update(['status' => InstallmentPayment::STATUS_SKIPPED]);

        return redirect()->route('admin.installments.agreements.index')->with('success', 'تم إلغاء اتفاقية التقسيط.');
    }

    public function markPayment(Request $request, InstallmentPayment $payment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys($this->paymentStatuses()))],
            'paid_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'in:cash,card,bank_transfer,online,wallet,other'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            DB::beginTransaction();

            if ($data['status'] === InstallmentPayment::STATUS_PAID) {
                // التحقق من عدم وجود Payment مرتبط بالفعل
                if (!$payment->payment_id) {
                    $agreement = $payment->agreement;
                    $agreement->loadMissing(['enrollment', 'offlineEnrollment', 'course', 'offlineEnrollment.course']);

                    $linkedEnrollment = $agreement->student_course_enrollment_id
                        ? $agreement->enrollment
                        : $agreement->offlineEnrollment;

                    $courseTitle = $agreement->display_course_title;
                    $invoiceType = $agreement->student_course_enrollment_id ? 'course' : 'offline_course';

                    // إنشاء أو الحصول على Invoice للتقسيط
                    $invoice = $linkedEnrollment?->invoice_id
                        ? Invoice::find($linkedEnrollment->invoice_id)
                        : null;

                    // إذا لم يكن هناك Invoice، أنشئ واحدة
                    if (!$invoice) {
                        $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
                        $invoice = Invoice::create([
                            'invoice_number' => $invoiceNumber,
                            'user_id' => $agreement->user_id,
                            'type' => $invoiceType,
                            'description' => 'فاتورة تقسيط - ' . $courseTitle,
                            'subtotal' => $payment->amount,
                            'tax_amount' => 0,
                            'discount_amount' => 0,
                            'total_amount' => $payment->amount,
                            'status' => 'paid',
                            'due_date' => $payment->due_date,
                            'paid_at' => $data['paid_at'] ? Carbon::parse($data['paid_at']) : now(),
                            'notes' => 'فاتورة قسط تقسيط رقم: ' . $payment->sequence_number,
                            'items' => [
                                [
                                    'description' => 'قسط تقسيط - ' . $courseTitle,
                                    'quantity' => 1,
                                    'price' => $payment->amount,
                                    'total' => $payment->amount,
                                ]
                            ],
                        ]);
                    }

                    // إنشاء Payment
                    $paymentNumber = 'PAY-' . str_pad(Payment::count() + 1, 8, '0', STR_PAD_LEFT);
                    $paymentRecord = Payment::create([
                        'payment_number' => $paymentNumber,
                        'invoice_id' => $invoice->id,
                        'user_id' => $agreement->user_id,
                        'payment_method' => $data['payment_method'] ?? 'cash',
                        'amount' => $payment->amount,
                        'currency' => 'EGP',
                        'status' => 'completed',
                        'paid_at' => $data['paid_at'] ? Carbon::parse($data['paid_at']) : now(),
                        'processed_by' => auth()->id(),
                        'installment_payment_id' => $payment->id,
                        'notes' => 'دفعة قسط تقسيط رقم: ' . $payment->sequence_number . ($data['notes'] ? ' - ' . $data['notes'] : ''),
                    ]);

                    // إنشاء Transaction
                    $transactionNumber = 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT);
                    Transaction::create([
                        'transaction_number' => $transactionNumber,
                        'user_id' => $agreement->user_id,
                        'payment_id' => $paymentRecord->id,
                        'invoice_id' => $invoice->id,
                        'expense_id' => null,
                        'subscription_id' => null,
                        'type' => 'credit', // دائن (إيراد)
                        'category' => 'course_payment',
                        'amount' => $payment->amount,
                        'currency' => 'EGP',
                        'description' => 'دفعة قسط تقسيط - ' . $courseTitle . ' - قسط رقم: ' . $payment->sequence_number,
                        'status' => 'completed',
                        'metadata' => [
                            'installment_agreement_id' => $agreement->id,
                            'installment_payment_id' => $payment->id,
                            'sequence_number' => $payment->sequence_number,
                        ],
                        'created_by' => auth()->id(),
                    ]);

                    // ربط InstallmentPayment بالPayment
                    $payment->payment_id = $paymentRecord->id;
                }

                $payment->status = InstallmentPayment::STATUS_PAID;
                $payment->paid_at = $data['paid_at'] ? Carbon::parse($data['paid_at']) : now();
            } else {
                $payment->status = $data['status'];
                $payment->paid_at = null;
            }

            $payment->notes = $data['notes'] ?? null;
            $payment->save();

            // تحديث حالة الاتفاقية
            $payment->agreement->refreshStatus();

            DB::commit();

            return back()->with('success', 'تم تحديث حالة القسط بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث حالة القسط: ' . $e->getMessage());
        }
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'installment_plan_id' => ['required', 'exists:installment_plans,id'],
            'student_course_enrollment_id' => ['required', 'exists:student_course_enrollments,id'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'start_date' => ['required', 'date'],
            'status' => ['nullable', 'in:' . implode(',', array_keys($this->statusOptions()))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    protected function statusOptions(): array
    {
        return [
            InstallmentAgreement::STATUS_ACTIVE => 'نشط',
            InstallmentAgreement::STATUS_OVERDUE => 'متأخر',
            InstallmentAgreement::STATUS_COMPLETED => 'مكتمل',
            InstallmentAgreement::STATUS_CANCELLED => 'ملغى',
        ];
    }

    protected function paymentStatuses(): array
    {
        return [
            InstallmentPayment::STATUS_PENDING => 'قيد الانتظار',
            InstallmentPayment::STATUS_PAID => 'مدفوع',
            InstallmentPayment::STATUS_OVERDUE => 'متأخر',
            InstallmentPayment::STATUS_SKIPPED => 'متجاوز',
        ];
    }

    protected function frequencyUnits(): array
    {
        return [
            'month' => 'شهري',
            'week' => 'أسبوعي',
            'day' => 'يومي',
            'year' => 'سنوي',
        ];
    }
}
