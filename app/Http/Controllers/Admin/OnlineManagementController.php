<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseGroup;
use App\Models\User;
use App\Models\Wallet;
use App\Support\OfflineEnrollmentProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OnlineManagementController extends Controller
{
    /**
     * كورسات تظهر في «إدارة الأونلاين»: أونلاين فقط، أو لديها مجموعة مفعّل لها الأونلاين.
     */
    public function index(Request $request): View
    {
        $inScope = function ($q) {
            $q->where(function ($qq) {
                $qq->where('online_only', true)
                    ->orWhereHas('groups', function ($g) {
                        $g->where('online_booking_enabled', true)
                            ->where('is_active', true)
                            ->where('status', 'active');
                    });
            });
        };

        $statsBase = OfflineCourse::query()->where($inScope);

        $stats = [
            'total' => (clone $statsBase)->count(),
            'active' => (clone $statsBase)->where('status', 'active')->count(),
            'draft' => (clone $statsBase)->where('status', 'draft')->count(),
            'online_only' => (clone $statsBase)->where('online_only', true)->count(),
        ];

        $instructors = User::where('role', 'instructor')->where('is_active', true)->orderBy('name')->get();

        $query = OfflineCourse::query()
            ->with(['instructor'])
            ->where($inScope)
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . addcslashes((string) $request->input('search'), '%_\\') . '%';
                $q->where(function ($qq) use ($s) {
                    $qq->where('title', 'like', $s)
                        ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', $s));
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('instructor_id'), fn ($q) => $q->where('instructor_id', $request->integer('instructor_id')))
            ->orderByDesc('online_only')
            ->latest();

        $courses = $query->paginate(15)->withQueryString();

        $courses->load(['groups' => function ($q) {
            $q->orderBy('name');
        }]);

        return view('admin.online-management.index', compact('courses', 'stats', 'instructors'));
    }

    public function createCourse(): View
    {
        $instructors = User::where('role', 'instructor')->where('is_active', true)->orderBy('name')->get();

        return view('admin.online-management.create-course', compact('instructors'));
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructor_id' => ['required', 'integer', 'exists:users,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'max_students_online' => ['required', 'integer', 'min:1', 'max:500'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,active'],
        ], [
            'title.required' => 'أدخل عنوان الكورس.',
            'instructor_id.required' => 'اختر المدرب.',
            'instructor_id.exists' => 'المدرب المحدد غير موجود.',
            'max_students_online.required' => 'حدد سعة المجموعة الأونلاين.',
            'status.required' => 'حدد حالة الكورس.',
        ]);

        $instructor = User::query()->whereKey((int) $validated['instructor_id'])->first();
        if (! $instructor || $instructor->role !== 'instructor') {
            return back()->withInput()->withErrors([
                'instructor_id' => 'اختر مدرباً بدور instructor.',
            ]);
        }
        if (! $instructor->is_active) {
            return back()->withInput()->withErrors([
                'instructor_id' => 'المدرب غير نشط. فعّله أولاً ثم أعد المحاولة.',
            ]);
        }

        try {
            if (! Schema::hasColumn('offline_courses', 'online_only')) {
                return back()->withInput()->withErrors([
                    'error' => 'قاعدة البيانات غير محدّثة (عمود online_only مفقود). نفّذ: php artisan migrate',
                ]);
            }
            if (! Schema::hasColumn('offline_course_groups', 'online_slug')
                || ! Schema::hasColumn('offline_course_groups', 'online_booking_enabled')
                || ! Schema::hasColumn('offline_course_groups', 'max_students_online')) {
                return back()->withInput()->withErrors([
                    'error' => 'قاعدة البيانات غير محدّثة (أعمدة الحجز الأونلاين للمجموعات مفقودة). نفّذ: php artisan migrate',
                ]);
            }

            $branchId = null;
            if (Schema::hasColumn('offline_courses', 'branch_id')) {
                $branchId = $this->resolveBranchIdForInstructor((int) $instructor->id);
                if ($branchId === null) {
                    return back()->withInput()->withErrors([
                        'error' => 'لا يوجد فرع صالح في جدول branches. أنشئ فرعاً (مثل slug=main) ثم أعد المحاولة.',
                    ]);
                }
            }

            $courseId = null;

            DB::transaction(function () use ($validated, $instructor, $branchId, &$courseId) {
                $courseData = [
                    'title' => $validated['title'],
                    'description' => $validated['description'] ?? null,
                    'instructor_id' => (int) $instructor->id,
                    'price' => isset($validated['price']) ? (float) $validated['price'] : 0,
                    'max_students' => (int) $validated['max_students_online'],
                    'current_students' => 0,
                    'status' => $validated['status'],
                    'is_active' => $validated['status'] === 'active' ? 1 : 0,
                    'public_booking_enabled' => 0,
                    'student_online_portal_enabled' => 1,
                    'online_only' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($branchId !== null) {
                    $courseData['branch_id'] = $branchId;
                }

                // إدراج مباشر لتفادي مشاكل Casts/Observers على السيرفر
                $courseId = (int) DB::table('offline_courses')->insertGetId($courseData);

                $groupName = trim((string) ($validated['group_name'] ?? ''));
                if ($groupName === '') {
                    $groupName = 'مجموعة أونلاين — '.$validated['title'];
                }

                $onlineSlug = OfflineCourseGroup::generateUniqueOnlineSlug('online-'.$courseId.'-'.Str::lower(Str::random(6)));

                $groupData = [
                    'offline_course_id' => $courseId,
                    'instructor_id' => (int) $instructor->id,
                    'name' => $groupName,
                    'max_students' => 0,
                    'current_students' => 0,
                    'max_students_online' => (int) $validated['max_students_online'],
                    'current_students_online' => 0,
                    'location' => 'أونلاين',
                    'status' => 'active',
                    'is_active' => 1,
                    'public_booking_enabled' => 0,
                    'online_booking_enabled' => 1,
                    'online_slug' => $onlineSlug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('offline_course_groups', 'public_slug')) {
                    $groupData['public_slug'] = null;
                }

                DB::table('offline_course_groups')->insert($groupData);
            });
        } catch (\Throwable $e) {
            report($e);
            \Log::error('online-management.storeCourse failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile().':'.$e->getLine(),
                'title' => $validated['title'] ?? null,
                'instructor_id' => $validated['instructor_id'] ?? null,
            ]);

            // إظهار السبب الحقيقي للأدمن لتشخيص السيرفر
            $msg = trim($e->getMessage());
            $hint = 'تعذر إنشاء الكورس';
            if ($msg !== '') {
                $hint .= ': '.$msg;
            }

            return back()->withInput()->withErrors(['error' => $hint]);
        }

        return redirect()
            ->route('admin.offline-courses.show', $courseId)
            ->with('success', 'تم إنشاء كورس أونلاين فقط. إدارة الحجز والتسجيل عبر قناة الأونلاين فقط.');
    }

    private function resolveBranchIdForInstructor(int $instructorId): ?int
    {
        try {
            $instructorBranchId = User::query()->whereKey($instructorId)->value('branch_id');
            if ($instructorBranchId) {
                $exists = Branch::query()->whereKey($instructorBranchId)->exists();
                if ($exists) {
                    return (int) $instructorBranchId;
                }
            }

            return Branch::defaultAssignableId();
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    public function enrollForm(Request $request): View
    {
        $courses = OfflineCourse::query()
            ->where('status', 'active')
            ->where('is_active', true)
            ->whereHas('groups', function ($g) {
                $g->where('online_booking_enabled', true)
                    ->where('is_active', true)
                    ->where('status', 'active');
            })
            ->with(['groups' => fn ($q) => $q->orderBy('name')])
            ->orderBy('title')
            ->get();

        $selectedCourseId = $request->integer('offline_course_id');
        $selectedGroupId = $request->integer('group_id');
        $wallets = Wallet::academyWallets()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.online-management.enroll', compact('courses', 'selectedCourseId', 'selectedGroupId', 'wallets'));
    }

    public function enrollStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_email' => ['required', 'email'],
            'offline_course_id' => ['required', 'exists:offline_courses,id'],
            'group_id' => ['required', 'exists:offline_course_groups,id'],
            'mark_fully_paid' => ['sometimes', 'boolean'],
            'payment_method' => ['nullable', 'required_if:mark_fully_paid,1', 'in:cash,wallet'],
            'wallet_id' => ['nullable', 'required_if:payment_method,wallet', 'exists:wallets,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'student_email.required' => 'أدخل بريد الطالب المسجّل في المنصة.',
        ]);

        $user = User::where('email', $data['student_email'])->first();
        if (! $user) {
            return back()->withErrors(['student_email' => 'لا يوجد حساب بهذا البريد. أنشئ حساب الطالب أولاً.'])->withInput();
        }

        $course = OfflineCourse::findOrFail($data['offline_course_id']);
        $group = OfflineCourseGroup::where('id', $data['group_id'])
            ->where('offline_course_id', $course->id)
            ->first();

        if (! $group) {
            return back()->withErrors(['group_id' => 'المجموعة لا تنتمي لهذا الكورس.'])->withInput();
        }

        if (! $group->is_active || $group->status !== 'active') {
            return back()->withErrors(['group_id' => 'المجموعة غير نشطة.'])->withInput();
        }

        if (! $group->online_booking_enabled && ! $course->online_only) {
            return back()->withErrors(['group_id' => 'فعّل «الحجز الأونلاين» لهذه المجموعة من صفحة الكورس ثم أعد المحاولة.'])->withInput();
        }

        if (! $group->canEnroll('online')) {
            return back()->withErrors(['group_id' => 'لا توجد مقاعد أونلاين متاحة في هذه المجموعة.'])->withInput();
        }

        if (! $course->canEnroll()) {
            return back()->withErrors(['offline_course_id' => 'الكورس غير متاح أو وصل للحد الأقصى من الطلاب.'])->withInput();
        }

        if ($course->enrollments()
            ->where('user_id', $user->id)
            ->where('enrollment_channel', 'online')
            ->where('status', 'active')
            ->exists()) {
            return back()->withErrors(['student_email' => 'الطالب مسجّل بالفعل في هذا الكورس (قناة أونلاين).'])->withInput();
        }

        $coursePrice = (float) $course->price;
        $paidAmount = ! empty($data['mark_fully_paid']) && $coursePrice > 0 ? $coursePrice : 0.0;
        $paymentMethod = $data['payment_method'] ?? 'cash';
        $walletId = isset($data['wallet_id']) ? (int) $data['wallet_id'] : null;

        if ($paidAmount > 0 && $paymentMethod === 'wallet') {
            $wallet = Wallet::academyWallets()
                ->where('is_active', true)
                ->whereKey($walletId)
                ->first();

            if (! $wallet) {
                return back()->withInput()->withErrors(['wallet_id' => 'اختر محفظة أكاديمية نشطة وصحيحة.']);
            }
        } else {
            $walletId = null;
        }

        $paymentMeta = [
            'payment_method' => $paidAmount > 0 ? $paymentMethod : 'admin_enrollment',
            'payment_notes' => 'تسجيل يدوي من إدارة الأونلاين — ' . ($request->user()?->name ?? 'مسؤول'),
        ];
        if ($walletId) {
            $paymentMeta['wallet_id'] = $walletId;
            $paymentMeta['deposit_notes'] = 'إيداع من تسجيل أونلاين يدوي';
        }

        try {
            DB::beginTransaction();
            OfflineEnrollmentProvisioner::create(
                $course,
                $user->id,
                (int) $group->id,
                'active',
                $coursePrice,
                $paidAmount,
                $paymentMeta,
                $data['notes'] ?? null,
                'online'
            );
            DB::commit();
        } catch (\RuntimeException $e) {
            DB::rollBack();
            if ($e->getMessage() === 'DUPLICATE_ENROLLMENT') {
                return back()->withErrors(['student_email' => 'يوجد تسجيل أونلاين نشط مسبقاً لهذا الطالب.'])->withInput();
            }
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()->withErrors(['error' => 'تعذر إتمام التسجيل.']);
        }

        return redirect()
            ->route('admin.online-management.index')
            ->with('success', 'تم تفعيل الكورس (أونلاين) لحساب الطالب في المجموعة المختارة.');
    }
}
