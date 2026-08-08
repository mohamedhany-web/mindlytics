<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\StudentCourseEnrollment;
use App\Models\Wallet;
use App\Services\CourseProgressService;
use App\Services\InstructorCoursePercentageService;
use App\Services\OnlineEnrollmentsExcelExportService;
use App\Services\OnlineEnrollmentsPdfExportService;
use App\Services\ScholarshipCurriculumVisibilityService;
use App\Support\OnlineEnrollmentProvisioner;
use App\Mail\CourseEnrollmentActivatedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentEnrollmentController extends Controller
{
    /**
     * عرض صفحة إدارة تسجيل الطلاب (الأونلاين)
     */
    public function index(
        Request $request,
        CourseProgressService $progressService,
        ScholarshipCurriculumVisibilityService $visibility,
    ) {
        $query = $this->filteredEnrollmentsQuery($request);

        $enrollments = $query->with(['student', 'course.academicYear', 'course.academicSubject', 'activatedBy'])
            ->latest('enrolled_at')
            ->paginate(20);

        // النسبة الحقيقية من المنهج (وتصحيح المخزّن لو كان متضخّم/قديم)
        $progressService->hydrateEnrollmentsWithLiveProgress($enrollments->getCollection(), $visibility, true);

        // البيانات المساعدة للفلاتر - استخدام الكاش
        $courses = Cache::remember('active_courses_list', now()->addHours(1), function () {
            return AdvancedCourse::active()
                ->publicCatalog()
                ->with(['academicYear', 'academicSubject'])
                ->get();
        });
        
        // استخدام خدمة الكاش للإحصائيات
        $statsService = app(\App\Services\StatisticsCacheService::class);
        $statsService->clearStats('enrollment_stats');
        $stats = $statsService->getEnrollmentStats();

        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'balance']);

        return view('admin.online-enrollments.index', compact('enrollments', 'courses', 'stats', 'wallets'));
    }

    /**
     * إعادة حساب النسب الفعلية لكل التسجيلات المطابقة للفلتر الحالي.
     */
    public function resyncProgress(
        Request $request,
        CourseProgressService $progressService,
        ScholarshipCurriculumVisibilityService $visibility,
    ) {
        $query = $this->filteredEnrollmentsQuery($request);
        $updated = 0;

        $query->with(['student', 'course'])
            ->orderBy('id')
            ->chunkById(50, function ($rows) use ($progressService, $visibility, &$updated) {
                $progressService->hydrateEnrollmentsWithLiveProgress($rows, $visibility, true);
                $updated += $rows->count();
            });

        app(\App\Services\StatisticsCacheService::class)->clearStats('enrollment_stats');

        return redirect()
            ->route('admin.online-enrollments.index', $request->query())
            ->with('success', "تم تحديث النسبة الفعلية لـ {$updated} تسجيل من المنهج الحقيقي (مشاهدة الفيديوهات والعناصر).");
    }

    /**
     * تصدير تسجيلات الأونلاين إلى Excel (بنفس فلاتر الصفحة).
     */
    public function export(Request $request, OnlineEnrollmentsExcelExportService $excel): StreamedResponse
    {
        $query = $this->filteredEnrollmentsQuery($request);
        $filterParts = $this->filterSummaryParts($request);

        return $excel->streamDownload(
            $query,
            implode(' | ', $filterParts),
            'online-enrollments-'.now()->format('Y-m-d_His').'.xlsx'
        );
    }

    /**
     * طباعة PDF عربي واضح بنفس فلاتر الصفحة.
     */
    public function exportPdf(Request $request, OnlineEnrollmentsPdfExportService $pdf): StreamedResponse
    {
        $query = $this->filteredEnrollmentsQuery($request);
        $filterParts = $this->filterSummaryParts($request);

        return $pdf->streamDownload(
            $query,
            implode(' | ', $filterParts)
        );
    }

    /**
     * @return list<string>
     */
    private function filterSummaryParts(Request $request): array
    {
        $filterParts = [];

        if ($request->filled('search')) {
            $filterParts[] = 'بحث: '.$request->search;
        }
        if ($request->filled('status')) {
            $filterParts[] = 'الحالة: '.$request->status;
        }
        if ($request->filled('course_id')) {
            $courseTitle = AdvancedCourse::query()->whereKey($request->course_id)->value('title');
            $filterParts[] = 'الكورس: '.($courseTitle ?: '#'.$request->course_id);
        }
        if ($request->filled('completion')) {
            $filterParts[] = $request->completion === 'finished'
                ? 'أنهى المنهج (100%)'
                : 'لم يُنه المنهج بعد';
        }

        return $filterParts;
    }

    /**
     * Query موحّد للصفحة والتصدير.
     */
    private function filteredEnrollmentsQuery(Request $request)
    {
        $query = StudentCourseEnrollment::query()->nonScholarship();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('course_id')) {
            $query->where('advanced_course_id', (int) $request->course_id);
        }

        if ($request->filled('completion')) {
            if ($request->completion === 'finished') {
                $query->finishedCurriculum();
            } elseif ($request->completion === 'in_progress') {
                $query->whereNull('curriculum_completed_at')
                    ->where(function ($q) {
                        $q->whereNull('progress')->orWhere('progress', '<', 100);
                    });
            }
        }

        return $query;
    }

    /**
     * عرض صفحة إضافة تسجيل جديد
     */
    public function create()
    {
        // استخدام pagination للطلاب بدلاً من get() لتقليل استهلاك الذاكرة
        $students = User::where('role', 'student')
            ->where('is_active', true)
            ->orderBy('name')
            ->select('id', 'name', 'phone')
            ->paginate(50);
        
        // استخدام الكاش للكورسات
        $courses = Cache::remember('active_courses_list', now()->addHours(1), function () {
            return AdvancedCourse::active()
                ->publicCatalog()
                ->with(['academicYear', 'academicSubject'])
                ->orderBy('title')
                ->get();
        });

        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type', 'balance']);

        return view('admin.online-enrollments.create', compact('students', 'courses', 'wallets'));
    }

    /**
     * حفظ تسجيل جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'advanced_course_id' => 'required|exists:advanced_courses,id',
            'status' => 'required|in:pending,active',
            'final_price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,online,wallet,other,free',
            'wallet_id' => 'nullable|required_if:payment_method,wallet|exists:wallets,id',
            'activate_as_free' => 'nullable|boolean',
            'notes' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'الطالب مطلوب',
            'user_id.exists' => 'الطالب المحدد غير موجود',
            'advanced_course_id.required' => 'الكورس مطلوب',
            'advanced_course_id.exists' => 'الكورس المحدد غير موجود',
            'status.required' => 'حالة التسجيل مطلوبة',
            'status.in' => 'حالة التسجيل غير صحيحة',
        ]);

        // التحقق من عدم وجود تسجيل مسبق - استخدام exists() بدلاً من first() للأداء الأفضل
        $existingEnrollment = StudentCourseEnrollment::where('user_id', $request->user_id)
                                                  ->where('advanced_course_id', $request->advanced_course_id)
                                                  ->exists();

        if ($existingEnrollment) {
            return back()->withErrors(['error' => 'الطالب مسجل بالفعل في هذا الكورس']);
        }

        // مسح الكاش بعد إضافة تسجيل جديد
        app(\App\Services\StatisticsCacheService::class)->clearStats('enrollment_stats');

        $isComplimentary = $request->boolean('activate_as_free');
        $paymentMethod = 'cash';
        $wallet = null;
        $pricing = null;
        $course = null;
        $student = null;

        if ($request->status === 'active') {
            $course = AdvancedCourse::findOrFail($request->advanced_course_id);
            $student = User::findOrFail($request->user_id);
            $pricing = OnlineEnrollmentProvisioner::resolvePricing(
                $course,
                $isComplimentary ? 0 : ($request->filled('final_price') ? (float) $request->final_price : null),
                $isComplimentary ? null : ($request->filled('discount_amount') ? (float) $request->discount_amount : null),
                $request->filled('original_price') ? (float) $request->original_price : null,
            );
            if ($isComplimentary) {
                $original = $pricing['original_price'] > 0 ? $pricing['original_price'] : $course->originalPrice();
                $pricing = [
                    'original_price' => round($original, 2),
                    'discount_amount' => round($original, 2),
                    'final_price' => 0,
                ];
            }
            $paymentMethod = $isComplimentary ? 'free' : ($request->payment_method ?? 'cash');
            if (! $isComplimentary && $paymentMethod === 'wallet') {
                $wallet = $this->resolveAcademyWallet($request->filled('wallet_id') ? (int) $request->wallet_id : null);
                if (! $wallet) {
                    return back()->withErrors(['wallet_id' => 'اختر محفظة أكاديمية نشطة وصحيحة'])->withInput();
                }
            }
        }

        $enrollmentData = [
            'user_id' => $request->user_id,
            'advanced_course_id' => $request->advanced_course_id,
            'status' => $request->status,
            'notes' => $request->notes,
            'enrolled_at' => now(),
        ];

        if ($request->status === 'active') {
            $enrollmentData['activated_at'] = now();
            $enrollmentData['activated_by'] = Auth::id();
            $enrollmentData['original_price'] = $pricing['original_price'];
            $enrollmentData['discount_amount'] = $pricing['discount_amount'];
            $enrollmentData['final_price'] = $pricing['final_price'];
            $enrollmentData['payment_method'] = $paymentMethod;
            $enrollmentData['enrollment_type'] = $isComplimentary ? 'gift' : 'purchase';
            $enrollmentData['hide_from_instructor'] = $isComplimentary;
        } elseif ($request->filled('final_price') && is_numeric($request->final_price)) {
            $enrollmentData['final_price'] = (float) $request->final_price;
        }

        DB::beginTransaction();
        try {
        $enrollment = StudentCourseEnrollment::create($enrollmentData);

        if ($enrollment->status === 'active' && $course && $student && $pricing) {
            $freshEnrollment = $enrollment->fresh(['student', 'course']);

            OnlineEnrollmentProvisioner::attachFinancialRecords(
                $freshEnrollment,
                $course,
                $student,
                $pricing,
                [
                    'payment_method' => $paymentMethod,
                    'wallet_id' => $wallet?->id,
                    'payment_notes' => $isComplimentary
                        ? 'تفعيل مجاني — مخفي عن المدرب'
                        : 'تسجيل من لوحة التحكم',
                    'deposit_notes' => $wallet
                        ? 'إيداع تفعيل كورس أونلاين — ' . $course->title
                        : null,
                ],
            );

            $freshEnrollment = $freshEnrollment->fresh();

            if (! $isComplimentary) {
                InstructorCoursePercentageService::processEnrollmentActivation($freshEnrollment, rethrowOnFailure: true);
            }

            DB::commit();
        } else {
            DB::commit();
        }
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors(['error' => 'تعذر إتمام التسجيل: ' . $e->getMessage()])->withInput();
        }

        if ($enrollment->status === 'active') {
            $freshEnrollment = $enrollment->fresh(['student', 'course']);
            // إرسال بريد تفعيل الكورس للطالب
            try {
                if ($freshEnrollment->student && $freshEnrollment->student->email) {
                    Mail::to($freshEnrollment->student->email)
                        ->send(new CourseEnrollmentActivatedMail($freshEnrollment));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $successMessage = 'تم تسجيل الطالب في الكورس بنجاح';
        if ($request->status === 'active' && ! $isComplimentary && $paymentMethod === 'wallet' && $wallet) {
            $successMessage = 'تم التسجيل وإنشاء الفاتورة والدفعة وإيداع المبلغ في محفظة '
                . ($wallet->name ?: Wallet::typeLabel($wallet->type)) . ' وحساب نسبة المدرب.';
        } elseif ($request->status === 'active' && $isComplimentary) {
            $successMessage = 'تم تسجيل الطالب وتفعيل الكورس مجاناً ولن تظهر بياناته عند المدرب.';
        }

        return redirect()->route('admin.online-enrollments.index')
                        ->with('success', $successMessage);
    }

    /**
     * عرض تفاصيل التسجيل
     */
    public function show(
        StudentCourseEnrollment $enrollment,
        CourseProgressService $progressService,
        ScholarshipCurriculumVisibilityService $visibility,
    ) {
        $enrollment->load(['student', 'course.academicYear', 'course.academicSubject', 'activatedBy']);

        $course = $enrollment->course;
        $student = $enrollment->student;
        $progressBreakdown = null;

        if ($course && $student) {
            $sections = $course->activeSections()
                ->with([
                    'visibleStudents:id',
                    'visibleGroups.members:id',
                    'activeItems' => fn ($q) => $q->orderBy('order')->with([
                        'item',
                        'visibleStudents:id',
                        'visibleGroups.members:id',
                    ]),
                ])
                ->orderBy('order')
                ->get();

            $sections = $visibility->filterSectionsForStudent($sections, $student, $course);
            $progressBreakdown = $progressService->buildProgressBreakdown($student, $course, $sections);

            // مزامنة الحقيقة (مسموح بالتخفيض لو النسبة المخزّنة كانت خاطئة/متضخّمة)
            $progressService->syncEnrollmentProgress(
                (int) $student->id,
                (int) $course->id,
                (float) $progressBreakdown['progress'],
                true
            );
            $enrollment->refresh();
            app(\App\Services\StatisticsCacheService::class)->clearStats('enrollment_stats');
        }

        return view('admin.online-enrollments.show', compact('enrollment', 'progressBreakdown'));
    }

    /**
     * تفعيل سريع للتسجيل عن طريق البريد الإلكتروني ورمز الكورس
     */
    public function quickActivate(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'advanced_course_id' => 'required|exists:advanced_courses,id',
            'final_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,online,wallet,other,free',
            'wallet_id' => 'nullable|required_if:payment_method,wallet|exists:wallets,id',
            'activate_as_free' => 'nullable|boolean',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'advanced_course_id.required' => 'الكورس مطلوب',
            'advanced_course_id.exists' => 'الكورس المحدد غير موجود',
        ]);

        $student = User::where('role', 'student')
            ->where('email', $validated['email'])
            ->first();

        if (! $student) {
            return back()->withErrors([
                'quick_activate_email' => 'لا يوجد طالب مسجل بهذا البريد الإلكتروني.',
            ])->withInput();
        }

        $course = AdvancedCourse::findOrFail($validated['advanced_course_id']);
        $isComplimentary = $request->boolean('activate_as_free');
        $pricing = OnlineEnrollmentProvisioner::resolvePricing(
            $course,
            $isComplimentary ? 0 : (isset($validated['final_price']) ? (float) $validated['final_price'] : null),
            $isComplimentary ? null : (isset($validated['discount_amount']) ? (float) $validated['discount_amount'] : null),
        );

        if ($isComplimentary) {
            $original = $pricing['original_price'] > 0 ? $pricing['original_price'] : $course->originalPrice();
            $pricing = [
                'original_price' => round($original, 2),
                'discount_amount' => round($original, 2),
                'final_price' => 0,
            ];
        }

        $paymentMethod = $isComplimentary ? 'free' : ($validated['payment_method'] ?? 'cash');
        $wallet = null;
        if (! $isComplimentary && $paymentMethod === 'wallet') {
            $wallet = $this->resolveAcademyWallet(isset($validated['wallet_id']) ? (int) $validated['wallet_id'] : null);
            if (! $wallet) {
                return back()->withErrors(['wallet_id' => 'اختر محفظة أكاديمية نشطة وصحيحة'])->withInput();
            }
        }

        // مسح كاش الإحصائيات
        app(\App\Services\StatisticsCacheService::class)->clearStats('enrollment_stats');

        DB::beginTransaction();
        try {
        $enrollment = StudentCourseEnrollment::firstOrNew([
            'user_id' => $student->id,
            'advanced_course_id' => $validated['advanced_course_id'],
        ]);

        $enrollment->status = 'active';
        $enrollment->enrolled_at = $enrollment->enrolled_at ?? now();
        $enrollment->activated_at = now();
        $enrollment->activated_by = Auth::id();
        $enrollment->original_price = $pricing['original_price'];
        $enrollment->discount_amount = $pricing['discount_amount'];
        $enrollment->final_price = $pricing['final_price'];
        $enrollment->payment_method = $paymentMethod;
        $enrollment->enrollment_type = $isComplimentary ? 'gift' : ($enrollment->enrollment_type ?? 'purchase');
        $enrollment->hide_from_instructor = $isComplimentary;
        $enrollment->save();

        $freshEnrollment = $enrollment->fresh(['student', 'course']);

        OnlineEnrollmentProvisioner::attachFinancialRecords(
            $freshEnrollment,
            $course,
            $student,
            $pricing,
            [
                'payment_method' => $paymentMethod,
                'wallet_id' => $wallet?->id,
                'payment_notes' => $isComplimentary
                    ? 'تفعيل مجاني — مخفي عن المدرب'
                    : 'تفعيل سريع من لوحة التحكم',
                'deposit_notes' => $wallet
                    ? 'إيداع تفعيل كورس أونلاين — ' . $course->title
                    : null,
            ],
            replaceExisting: true,
        );

        $freshEnrollment = $freshEnrollment->fresh();

        if (! $isComplimentary) {
            InstructorCoursePercentageService::processEnrollmentActivation($freshEnrollment, rethrowOnFailure: true);
        }

        DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withErrors([
                'quick_activate_email' => 'تعذر إتمام التفعيل: ' . $e->getMessage(),
            ])->withInput();
        }

        // إرسال بريد التفعيل للطالب
        try {
            $freshEnrollment->loadMissing(['student', 'course']);
            if ($freshEnrollment->student && $freshEnrollment->student->email) {
                Mail::to($freshEnrollment->student->email)
                    ->send(new CourseEnrollmentActivatedMail($freshEnrollment));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('admin.online-enrollments.index')
            ->with('success', $isComplimentary
                ? 'تم تفعيل الكورس مجاناً للطالب ولن تظهر بياناته عند المدرب.'
                : ($paymentMethod === 'wallet' && $wallet
                    ? 'تم التفعيل وإنشاء الفاتورة والدفعة وإيداع المبلغ في محفظة ' . ($wallet->name ?: Wallet::typeLabel($wallet->type)) . ' وحساب نسبة المدرب.'
                    : 'تم تفعيل الكورس وإنشاء الفاتورة وحساب نسبة المدرب على المبلغ بعد الخصم.'));
    }

    /**
     * تفعيل تسجيل الطالب أو إعادة تفعيله
     */
    public function activate(StudentCourseEnrollment $enrollment)
    {
        if ($enrollment->status === 'active') {
            return back()->withErrors(['error' => 'التسجيل مفعل بالفعل']);
        }

        $wasSuspended = $enrollment->status === 'suspended';
        
        $enrollment->update([
            'status' => 'active',
            'activated_at' => now(),
            'activated_by' => Auth::id(),
        ]);

        $freshEnrollment = $enrollment->fresh();
        InstructorCoursePercentageService::processEnrollmentActivation($freshEnrollment, rethrowOnFailure: true);

        // إرسال بريد تفعيل الكورس عند التفعيل اليدوي
        try {
            $freshEnrollment->loadMissing(['student', 'course']);
            if ($freshEnrollment->student && $freshEnrollment->student->email) {
                Mail::to($freshEnrollment->student->email)
                    ->send(new CourseEnrollmentActivatedMail($freshEnrollment));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $message = $wasSuspended 
            ? 'تم إعادة تفعيل التسجيل وفتح الكورس للطالب بنجاح' 
            : 'تم تفعيل التسجيل بنجاح';

        return back()->with('success', $message);
    }

    /**
     * إلغاء تفعيل تسجيل الطالب
     */
    public function deactivate(StudentCourseEnrollment $enrollment)
    {
        if ($enrollment->status !== 'active') {
            return back()->withErrors(['error' => 'التسجيل غير مفعل']);
        }

        $enrollment->update([
            'status' => 'suspended',
        ]);

        return back()->with('success', 'تم إلغاء تفعيل التسجيل');
    }

    /**
     * تحديث تقدم الطالب
     */
    public function updateProgress(Request $request, StudentCourseEnrollment $enrollment)
    {
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
        ], [
            'progress.required' => 'نسبة التقدم مطلوبة',
            'progress.numeric' => 'نسبة التقدم يجب أن تكون رقم',
            'progress.min' => 'نسبة التقدم لا يمكن أن تكون أقل من صفر',
            'progress.max' => 'نسبة التقدم لا يمكن أن تزيد عن 100',
        ]);

        $enrollment->update([
            'progress' => $request->progress,
        ]);

        // إذا وصل التقدم إلى 100%، تغيير الحالة إلى مكتمل
        if ($request->progress == 100) {
            $enrollment->update([
                'status' => 'completed',
                'curriculum_completed_at' => $enrollment->curriculum_completed_at ?? now(),
            ]);
        }

        return back()->with('success', 'تم تحديث تقدم الطالب');
    }

    /**
     * تحديث ملاحظات التسجيل
     */
    public function updateNotes(Request $request, StudentCourseEnrollment $enrollment)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $enrollment->update([
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'تم تحديث الملاحظات');
    }

    /**
     * حذف التسجيل
     */
    public function destroy(StudentCourseEnrollment $enrollment)
    {
        $studentName = $enrollment->student->name;
        $courseName = $enrollment->course->title;
        
        $enrollment->delete();

        return redirect()->route('admin.online-enrollments.index')
                        ->with('success', "تم حذف تسجيل {$studentName} من كورس {$courseName}");
    }

    /**
     * البحث عن الطلاب بالهاتف (AJAX)
     */
    public function searchStudentByPhone(Request $request)
    {
        $phone = $request->get('phone');
        
        if (!$phone) {
            return response()->json(['error' => 'رقم الهاتف مطلوب'], 400);
        }

        $student = User::where('role', 'student')
                      ->where(function($query) use ($phone) {
                          $query->where('phone', $phone)
                                ->orWhere('parent_phone', $phone);
                      })
                      ->first();

        if (!$student) {
            return response()->json(['error' => 'لم يتم العثور على طالب بهذا الرقم'], 404);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'phone' => $student->phone,
                'parent_phone' => $student->parent_phone,
            ]
        ]);
    }

    private function resolveAcademyWallet(?int $walletId): ?Wallet
    {
        if (! $walletId) {
            return null;
        }

        return Wallet::academyWallets()
            ->where('is_active', true)
            ->whereKey($walletId)
            ->first();
    }
}