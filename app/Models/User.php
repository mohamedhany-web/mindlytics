<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Concerns\QueriesByBranch;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, QueriesByBranch;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'google_id',
        'role',
        'is_community_contributor',
        'community_contributor_type',
        'parent_id',
        'is_active',
        'profile_image',
        'profile_image_disk',
        'birth_date',
        'address',
        'bio',
        'headline',
        'skills',
        'academic_year_id',
        'last_login_at',
        'referral_code',
        'referred_by',
        'referred_at',
        'total_referrals',
        'completed_referrals',
        'employee_job_id',
        'sales_reports_to_id',
        'employee_code',
        'hire_date',
        'weekly_off_day',
        'work_schedule_id',
        'work_mode',
        'offline_attendance_type',
        'onsite_days',
        'work_week_plan',
        'termination_date',
        'salary',
        'employee_notes',
        'sales_commission_mode',
        'sales_commission_value',
        'sales_commission_tiers',
        'sales_commission_tier_period',
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_holder_name',
        'bank_iban',
        'is_employee',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'branch_id',
        'offline_location_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * ضمان ربط المستخدم بفرع افتراضي عند الإنشاء إن لم يُحدَّد branch_id.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user): void {
            if ($user->branch_id !== null) {
                return;
            }
            $branchId = Branch::defaultAssignableId();
            if ($branchId !== null) {
                $user->branch_id = $branchId;
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_community_contributor' => 'boolean',
            'birth_date' => 'date',
            'last_login_at' => 'datetime',
            'presence_last_seen_at' => 'datetime',
            'referred_at' => 'datetime',
            'hire_date' => 'date',
            'termination_date' => 'date',
            'salary' => 'decimal:2',
            'is_employee' => 'boolean',
            'sales_commission_value' => 'decimal:2',
            'sales_commission_tiers' => 'array',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'skills' => 'array',
            'onsite_days' => 'array',
            'work_week_plan' => 'array',
        ];
    }

    public const WORK_MODE_ONLINE = 'online';

    public const WORK_MODE_OFFLINE = 'offline';

    public const WORK_MODE_HYBRID = 'hybrid';

    public const OFFLINE_FULL_TIME = 'full_time';

    public const OFFLINE_SELECTED_DAYS = 'selected_days';

    public const DAY_MODE_ONLINE = 'online';

    public const DAY_MODE_OFFLINE = 'offline';

    public static function workModeLabels(): array
    {
        return [
            self::WORK_MODE_ONLINE => 'أونلاين (عن بُعد)',
            self::WORK_MODE_OFFLINE => 'أوفلاين (بالمكتب)',
            self::WORK_MODE_HYBRID => 'Hybrid (أيام أوفلاين + أيام أونلاين)',
        ];
    }

    public function isOfflineWorker(): bool
    {
        return ($this->work_mode ?? self::WORK_MODE_ONLINE) === self::WORK_MODE_OFFLINE;
    }

    public function isHybridWorker(): bool
    {
        return ($this->work_mode ?? self::WORK_MODE_ONLINE) === self::WORK_MODE_HYBRID;
    }

    public function isOnlineWorker(): bool
    {
        return ($this->work_mode ?? self::WORK_MODE_ONLINE) === self::WORK_MODE_ONLINE;
    }

    public function workModeLabel(): string
    {
        return self::workModeLabels()[$this->work_mode ?? self::WORK_MODE_ONLINE]
            ?? ($this->work_mode ?? self::WORK_MODE_ONLINE);
    }

    /**
     * أيام النزول للأوفلاين/الهجين (0=أحد … 6=سبت).
     *
     * @return list<int>
     */
    public function onsiteDayIndexes(): array
    {
        $fromPlan = [];
        foreach ($this->normalizedWorkWeekPlan() as $day => $row) {
            if (($row['active'] ?? false) && ($row['attendance_mode'] ?? '') === self::DAY_MODE_OFFLINE) {
                $fromPlan[] = (int) $day;
            }
        }
        if ($fromPlan !== []) {
            return array_values(array_unique($fromPlan));
        }

        $days = $this->onsite_days ?? [];
        if (! is_array($days)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $days)));
    }

    /**
     * جدول الأسبوع المنسّق: مفتاح 0..6.
     *
     * @return array<int, array{active:bool,attendance_mode:string,start_time:?string,end_time:?string,required_hours:?float}>
     */
    public function normalizedWorkWeekPlan(): array
    {
        $raw = $this->work_week_plan;
        if (! is_array($raw) || $raw === []) {
            return [];
        }

        $out = [];
        foreach (range(0, 6) as $day) {
            $row = $raw[$day] ?? $raw[(string) $day] ?? null;
            if (! is_array($row)) {
                continue;
            }
            $active = (bool) ($row['active'] ?? false);
            $mode = (string) ($row['attendance_mode'] ?? self::DAY_MODE_ONLINE);
            if (! in_array($mode, [self::DAY_MODE_ONLINE, self::DAY_MODE_OFFLINE], true)) {
                $mode = self::DAY_MODE_ONLINE;
            }
            $start = $this->normalizeHm($row['start_time'] ?? null);
            $end = $this->normalizeHm($row['end_time'] ?? null);
            $hours = $row['required_hours'] ?? null;
            $hours = $hours === null || $hours === '' ? null : (float) $hours;

            $out[$day] = [
                'active' => $active,
                'attendance_mode' => $mode,
                'start_time' => $start,
                'end_time' => $end,
                'required_hours' => $hours,
            ];
        }

        return $out;
    }

    public function hasWorkWeekPlan(): bool
    {
        return $this->normalizedWorkWeekPlan() !== [];
    }

    /**
     * @return array{active:bool,attendance_mode:string,start_time:?string,end_time:?string,required_hours:?float}|null
     */
    public function workPlanForDay(\Carbon\Carbon $date): ?array
    {
        $plan = $this->normalizedWorkWeekPlan();
        if ($plan === []) {
            return null;
        }

        return $plan[(int) $date->dayOfWeek] ?? [
            'active' => false,
            'attendance_mode' => self::DAY_MODE_ONLINE,
            'start_time' => null,
            'end_time' => null,
            'required_hours' => null,
        ];
    }

    /**
     * وضع الحضور المطلوب لهذا اليوم: online | offline.
     */
    public function attendanceModeFor(\Carbon\Carbon $date): string
    {
        if ($day = $this->workPlanForDay($date)) {
            if (! ($day['active'] ?? false)) {
                return self::DAY_MODE_ONLINE;
            }

            return ($day['attendance_mode'] ?? self::DAY_MODE_ONLINE) === self::DAY_MODE_OFFLINE
                ? self::DAY_MODE_OFFLINE
                : self::DAY_MODE_ONLINE;
        }

        $mode = $this->work_mode ?? self::WORK_MODE_ONLINE;

        if ($mode === self::WORK_MODE_ONLINE) {
            return self::DAY_MODE_ONLINE;
        }

        if ($mode === self::WORK_MODE_OFFLINE) {
            if (($this->offline_attendance_type ?? null) === self::OFFLINE_SELECTED_DAYS) {
                return in_array((int) $date->dayOfWeek, $this->onsiteDayIndexes(), true)
                    ? self::DAY_MODE_OFFLINE
                    : self::DAY_MODE_ONLINE;
            }

            return self::DAY_MODE_OFFLINE;
        }

        // hybrid بدون خطة مفصّلة: onsite_days = أوفلاين، الباقي أونلاين
        if ($mode === self::WORK_MODE_HYBRID) {
            return in_array((int) $date->dayOfWeek, $this->onsiteDayIndexes(), true)
                ? self::DAY_MODE_OFFLINE
                : self::DAY_MODE_ONLINE;
        }

        return self::DAY_MODE_ONLINE;
    }

    public function requiresManagerApprovalFor(\Carbon\Carbon $date): bool
    {
        if ($this->isAttendanceOffDay($date)) {
            return false;
        }

        return $this->attendanceModeFor($date) === self::DAY_MODE_OFFLINE;
    }

    private function normalizeHm(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $str = (string) $value;
        if (preg_match('/^\d{2}:\d{2}/', $str, $m)) {
            return substr($m[0], 0, 5);
        }

        return null;
    }

    public function salesCommissionLabel(): string
    {
        $mode = (string) ($this->sales_commission_mode ?? 'none');
        $val = (float) ($this->sales_commission_value ?? 0);

        return match ($mode) {
            'percent' => rtrim(rtrim(number_format($val, 2), '0'), '.').'%',
            'fixed' => number_format($val, 2).' ج.م',
            'tier' => 'Tier System',
            default => 'بدون',
        };
    }

    public function calculateSalesCommissionAmount(?float $baseAmount): float
    {
        $mode = (string) ($this->sales_commission_mode ?? 'none');

        if ($mode === 'tier') {
            return \App\Services\SalesCommissionTierService::quoteNextWin($this)['commission'];
        }

        $base = max(0, (float) ($baseAmount ?? 0));
        $val = (float) ($this->sales_commission_value ?? 0);

        return match ($mode) {
            'percent' => round($base * ($val / 100), 2),
            'fixed' => round(max(0, $val), 2),
            default => 0.0,
        };
    }

    /** مساهم في مجتمع البيانات فقط */
    public const COMMUNITY_CONTRIBUTOR_TYPE_DATA = 'data';
    /** مساهم في الذكاء الاصطناعي (Model Zoo، نماذج، إلخ) */
    public const COMMUNITY_CONTRIBUTOR_TYPE_AI = 'ai';

    public function isCommunityDataContributor(): bool
    {
        return $this->community_contributor_type === self::COMMUNITY_CONTRIBUTOR_TYPE_DATA;
    }

    public function isCommunityAiContributor(): bool
    {
        return $this->community_contributor_type === self::COMMUNITY_CONTRIBUTOR_TYPE_AI;
    }

    public function contributorProfile()
    {
        return $this->hasOne(ContributorProfile::class);
    }

    /**
     * رابط صورة الملف الشخصي.
     * قرص public محليًا عبر url()؛ R2/S3 الخاص عبر رابط موقّع مؤقتًا.
     * لا يُلحق معامل v= على الروابط الموقّعة حتى لا يُبطل التوقيع.
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (empty($this->profile_image)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($this->profile_image));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $base = $path;
        } else {
            $disk = $this->profile_image_disk ?: 'public';
            $base = storage_inline_media_url($disk, $path);
            if ($base === '') {
                $base = storage_inline_media_url('public', $path);
            }
            if ($base === '') {
                return null;
            }
        }
        $ts = $this->updated_at ? (string) $this->updated_at->timestamp : '';
        if ($ts !== '' && ! str_contains($base, 'X-Amz-')) {
            return $base.(str_contains($base, '?') ? '&' : '?').'v='.$ts;
        }

        return $base;
    }

    /**
     * هل هذا المستخدم مطلوب له تفعيل المصادقة الثنائية (أدمن ومدير عام والمدربين ومدير الفرع)
     */
    public function requiresTwoFactor(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'instructor', 'branch_manager'], true);
    }

    /**
     * هل يستخدم هذا المستخدم 2FA عبر البريد (بدون تطبيق TOTP)
     */
    public function usesEmailTwoFactor(): bool
    {
        return $this->requiresTwoFactor() && !$this->hasTwoFactorEnabled();
    }

    /**
     * هل المصادقة الثنائية مفعّلة للمستخدم
     */
    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
    }

    /**
     * علاقة مع الفرع (الأكاديمية / الدولة)
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * علاقة مع ولي الأمر
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * علاقة مع الأطفال (للوالدين)
     */
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    /**
     * علاقة مع السنة الدراسية
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * علاقة مع تسجيلات الكورسات
     */
    public function courseEnrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'user_id');
    }

    /**
     * علاقة مع عضوية المجموعات (group_members)
     */
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * المجموعات التي ينتمي إليها المستخدم (كطالب)
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * علاقة مع تسجيلات الكورسات الأوفلاين
     */
    public function offlineEnrollments()
    {
        return $this->hasMany(OfflineCourseEnrollment::class, 'user_id');
    }

    public function offlineCourseBookings()
    {
        return $this->hasMany(OfflineCourseBooking::class, 'user_id');
    }

    /**
     * مشاريع البورتفوليو (للطالب)
     */
    public function portfolioProjects()
    {
        return $this->hasMany(PortfolioProject::class, 'user_id');
    }

    /**
     * ملف رحلة التعلم العام (Mindlytics Journey)
     */
    public function journeyProfile()
    {
        return $this->hasOne(JourneyProfile::class, 'user_id');
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class, 'user_id');
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['earned_at', 'points_earned', 'progress', 'metadata', 'course_id'])
            ->withTimestamps();
    }

    /**
     * علاقة مع الكورسات الأوفلاين (كمدرب)
     */
    public function offlineCourses()
    {
        return $this->hasMany(OfflineCourse::class, 'instructor_id');
    }

    /**
     * علاقة مع اتفاقيات المدرب
     */
    public function instructorAgreements()
    {
        return $this->hasMany(InstructorAgreement::class, 'instructor_id');
    }

    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }

    public function agreementPayments()
    {
        return $this->hasMany(AgreementPayment::class, 'instructor_id');
    }

    public function payoutDetail()
    {
        return $this->hasOne(InstructorPayoutDetail::class);
    }

    /**
     * علاقة مع محاولات الامتحان
     */
    public function examAttempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * علاقة مع التقارير كطالب
     */
    public function studentReports()
    {
        return $this->hasMany(StudentReport::class, 'student_id');
    }

    /**
     * علاقة مع التقارير كولي أمر
     */
    public function parentReports()
    {
        return $this->hasMany(StudentReport::class, 'parent_id');
    }

    /**
     * علاقة مع رسائل الواتساب
     */
    public function whatsappMessages()
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    /**
     * علاقة مع الإشعارات المخصصة (تجاوز Laravel's built-in)
     */
    public function customNotifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * تجاوز علاقة notifications الافتراضية
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * علاقة مع محفظة المستخدم المالية
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * مدير فرع (لوحة branch-office)
     */
    public function isBranchManager(): bool
    {
        return $this->role === 'branch_manager';
    }

    /**
     * مدير مكان إداري (لوحة place-office)
     */
    public function isPlaceManager(): bool
    {
        return $this->role === 'place_manager';
    }

    public function offlineLocation()
    {
        return $this->belongsTo(OfflineLocation::class, 'offline_location_id');
    }

    /**
     * التحقق من كون المستخدم طالب
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * التحقق من كون المستخدم مدرب
     */
    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * التحقق من كون المستخدم مدير عام
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * التحقق من كون المستخدم إداري (للتوافق مع الكود القديم)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * التحقق من كون المستخدم مدرب (للتوافق مع الكود القديم)
     */
    public function isTeacher(): bool
    {
        return $this->role === 'instructor';
    }

    /**
     * التحقق من كون المستخدم ولي أمر (للتوافق مع الكود القديم - تم إزالة هذا الدور)
     * هذا method للتوافق فقط - سيُعيد دائماً false
     */
    public function isParent(): bool
    {
        return false; // تم إزالة دور ولي الأمر
    }

    /**
     * scope للطلاب
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * scope للمدربين
     */
    public function scopeInstructors($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * scope للمدربين (للتوافق مع الكود القديم)
     */
    public function scopeTeachers($query)
    {
        return $query->where('role', 'instructor');
    }

    /**
     * scope للمستخدمين النشطين
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * الحصول على الكورسات النشطة للطالب
     */
    public function activeCourses()
    {
        return $this->belongsToMany(AdvancedCourse::class, 'student_course_enrollments', 'user_id', 'advanced_course_id')
                    ->withPivot(['status', 'progress', 'enrolled_at', 'activated_at'])
                    ->where('student_course_enrollments.status', 'active')
                    ->orderByDesc('student_course_enrollments.activated_at')
                    ->orderByDesc('student_course_enrollments.created_at');
    }

    public function scholarshipRegistrations()
    {
        return $this->hasMany(ScholarshipRegistration::class);
    }

    public function hasScholarshipRegistration(): bool
    {
        return $this->scholarshipRegistrations()->exists();
    }

    /**
     * هل لدى الطالب وصول أكاديمي خارج منحته (أونلاين/أوفلاين/مسار/طلبات شراء).
     */
    public function hasNonScholarshipAcademyAccess(): bool
    {
        if ($this->courseEnrollments()->nonScholarship()
            ->whereIn('status', ['active', 'pending', 'completed', 'suspended'])
            ->exists()) {
            return true;
        }

        if ($this->offlineEnrollments()
            ->whereIn('status', ['active', 'pending'])
            ->exists()) {
            return true;
        }

        if ($this->learningPathEnrollments()
            ->whereIn('status', ['active', 'pending'])
            ->exists()) {
            return true;
        }

        return Order::where('user_id', $this->id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_APPROVED])
            ->exists();
    }

    /**
     * طالب مسجّل في منحة فقط — واجهة محدودة بدون كتالوج/أوفلاين/أونلاين الأكاديمية.
     */
    public function usesScholarshipOnlyPortal(): bool
    {
        if (! $this->hasScholarshipRegistration()) {
            return false;
        }

        return ! $this->hasNonScholarshipAcademyAccess();
    }

    /**
     * التحقق من التسجيل في كورس أونلاين
     */
    public function isEnrolledIn($courseId): bool
    {
        return $this->courseEnrollments()
                    ->where('advanced_course_id', $courseId)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * التحقق من التسجيل في كورس أوفلاين
     */
    public function isEnrolledInOfflineCourse($offlineCourseId): bool
    {
        return $this->offlineEnrollments()
                    ->where('offline_course_id', $offlineCourseId)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * الحصول على تسجيل الكورس
     */
    public function getCourseEnrollment($courseId)
    {
        return $this->courseEnrollments()
                    ->where('advanced_course_id', $courseId)
                    ->first();
    }

    /**
     * الحصول على آخر تقرير شهري
     */
    public function getLastMonthlyReport()
    {
        return $this->studentReports()
                    ->where('report_type', 'monthly')
                    ->latest()
                    ->first();
    }

    /**
     * الحصول على متوسط الدرجات
     */
    public function getAverageScore()
    {
        return $this->examAttempts()
                    ->where('status', 'completed')
                    ->avg('percentage') ?? 0;
    }

    /**
     * الحصول على عدد الامتحانات المكتملة
     */
    public function getCompletedExamsCount()
    {
        return $this->examAttempts()
                    ->where('status', 'completed')
                    ->count();
    }

    /**
     * تحديث آخر دخول بدون تفعيل Observers
     */
    public function updateLastLogin()
    {
        // استخدام DB مباشرة لتجنب أي مشاكل
        \DB::table('users')
            ->where('id', $this->id)
            ->update(['last_login_at' => now(), 'updated_at' => now()]);
    }

    /**
     * العلاقة مع الأدوار (نظام الصلاحيات المخصص)
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * الحصول على جميع الصلاحيات للمستخدم (من الأدوار)
     */
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    /**
     * التحقق من وجود صلاحية معينة (من الأدوار أو المباشرة)
     */
    public function hasPermission($permissionName)
    {
        // إذا كان admin، يعيد true دائماً
        if ($this->isAdmin()) {
            return true;
        }

        // التحقق من الصلاحيات المباشرة
        if ($this->directPermissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        // التحقق من الصلاحيات من الأدوار
        return $this->roles()->whereHas('permissions', function($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    /**
     * التحقق من وجود دور معين
     */
    public function hasRole($roleName)
    {
        // التحقق من الدور الأساسي
        if (strtolower($this->role) === strtolower($roleName)) {
            return true;
        }

        // التحقق من الأدوار المخصصة
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * إضافة دور للمستخدم
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        
        if ($role && !$this->hasRole($role->name)) {
            $this->roles()->attach($role->id);
        }
    }

    /**
     * إزالة دور من المستخدم
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }
        
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    /**
     * العلاقة المباشرة مع الصلاحيات (بدون أدوار)
     */
    public function directPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    /**
     * الحصول على جميع الصلاحيات (من الأدوار + المباشرة)
     */
    public function getAllPermissions()
    {
        $rolePermissions = $this->roles()->with('permissions')->get()
            ->pluck('permissions')->flatten()->unique('id');
        
        $directPermissions = $this->directPermissions;
        
        return $rolePermissions->merge($directPermissions)->unique('id');
    }

    /**
     * علاقة مع وظيفة الموظف
     */
    public function employeeJob()
    {
        return $this->belongsTo(EmployeeJob::class, 'employee_job_id');
    }

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class, 'work_schedule_id');
    }

    public function employeeAttendanceRecords()
    {
        return $this->hasMany(EmployeeAttendanceRecord::class);
    }

    public function todayAttendanceRecord()
    {
        return $this->hasOne(EmployeeAttendanceRecord::class)
            ->whereDate('work_date', today());
    }

    /**
     * علاقة مع مهام الموظف
     */
    public function employeeTasks()
    {
        return $this->hasMany(EmployeeTask::class, 'employee_id');
    }

    /**
     * علاقة مع اتفاقيات الموظف
     */
    public function employeeAgreements()
    {
        return $this->hasMany(EmployeeAgreement::class, 'employee_id');
    }

    /**
     * علاقة مع خصومات الراتب
     */
    public function salaryDeductions()
    {
        return $this->hasMany(EmployeeSalaryDeduction::class, 'employee_id');
    }

    /**
     * علاقة مع مدفوعات الراتب
     */
    public function salaryPayments()
    {
        return $this->hasMany(EmployeeSalaryPayment::class, 'employee_id');
    }

    public function salaryAdditions()
    {
        return $this->hasMany(EmployeeSalaryAddition::class, 'employee_id');
    }

    public function employeeDailyReports()
    {
        return $this->hasMany(EmployeeDailyReport::class, 'user_id');
    }

    /**
     * علاقة مع المهام المكلف بها
     */
    public function assignedTasks()
    {
        return $this->hasMany(EmployeeTask::class, 'assigned_by');
    }

    public function moderatedDesignCycles()
    {
        return $this->hasMany(DesignTaskCycle::class, 'moderator_id');
    }

    public function designerDesignCycles()
    {
        return $this->hasMany(DesignTaskCycle::class, 'designer_employee_id');
    }

    /**
     * علاقة مع طلبات الإجازة
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    /**
     * التحقق من كون المستخدم موظف
     */
    public function isEmployee(): bool
    {
        return $this->is_employee === true;
    }

    /**
     * موظف بوظيفة مبيعات (رمز الوظيفة sales).
     */
    public function isSalesEmployee(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }
        $this->loadMissing('employeeJob');

        return $this->employeeJob && strtolower((string) $this->employeeJob->code) === 'sales';
    }

    /**
     * موظف بوظيفة مدير مبيعات (رمز الوظيفة sales_manager).
     */
    public function isSalesManager(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }
        $this->loadMissing('employeeJob');

        return $this->employeeJob && strtolower((string) $this->employeeJob->code) === 'sales_manager';
    }

    /**
     * Business Developer — صلاحيات مدير مبيعات + الماركتينغ والمهام.
     */
    public function isBusinessDeveloper(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }
        $this->loadMissing('employeeJob');

        return $this->employeeJob && strtolower((string) $this->employeeJob->code) === 'business_developer';
    }

    public function hasSalesManagerPortalAccess(): bool
    {
        return $this->isSalesManager() || $this->isBusinessDeveloper();
    }

    public function hasModeratorPortalAccess(): bool
    {
        return $this->isModeratorEmployee() || $this->isBusinessDeveloper();
    }

    public function canManageModeratorResource(int $moderatorId): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($this->isBusinessDeveloper()) {
            return true;
        }

        return $this->isModeratorEmployee() && (int) $this->id === $moderatorId;
    }

    /**
     * موظف مبيعات أو مدير مبيعات أو Business Developer.
     */
    public function isSalesStaff(): bool
    {
        return $this->isSalesEmployee() || $this->isSalesManager() || $this->isBusinessDeveloper();
    }

    /**
     * الحملات الإعلانية المسندة لهذا الموظف (المبيعات).
     */
    public function advertisingCampaigns(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(AdvertisingCampaign::class, 'advertising_campaign_sales_user')
            ->withTimestamps();
    }

    /**
     * هل يخضع لنظام قفل الدوام (حضور/انصراف/خصومات)؟
     */
    public function isSubjectToWorkSchedule(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }

        if (config('employee_attendance.sales_employees_only', true)) {
            return $this->isSalesStaff();
        }

        return true;
    }

    /**
     * موظف بوظيفة مشرف محتوى (رمز الوظيفة moderator).
     */
    public function isModeratorEmployee(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }
        $this->loadMissing('employeeJob');

        return $this->employeeJob && strtolower((string) $this->employeeJob->code) === 'moderator';
    }

    /**
     * موظف بوظيفة مصمم (رمز الوظيفة designer).
     */
    public function isDesignerEmployee(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }
        $this->loadMissing('employeeJob');

        return $this->employeeJob && strtolower((string) $this->employeeJob->code) === 'designer';
    }

    /**
     * موظف مونتاج فيديو (رمز الوظيفة video_editing).
     */
    public function isVideoEditingEmployee(): bool
    {
        if (! $this->isEmployee()) {
            return false;
        }
        $this->loadMissing('employeeJob');

        return $this->employeeJob && $this->employeeJob->isVideoEditingJob();
    }

    /**
     * العملاء المحتملون المسندون لموظف المبيعات
     */
    public function assignedSalesLeads()
    {
        return $this->hasMany(SalesLead::class, 'assigned_to');
    }

    public function managedSalesTeam()
    {
        return $this->hasOne(SalesTeam::class, 'manager_id');
    }

    public function salesTeamMembership()
    {
        return $this->hasOne(SalesTeamMember::class, 'user_id');
    }

    public function salesInterestTypes()
    {
        return $this->belongsToMany(SalesInterestType::class, 'sales_user_specialties', 'user_id', 'interest_type_id')
            ->withTimestamps();
    }

    public function salesReportsTo()
    {
        return $this->belongsTo(User::class, 'sales_reports_to_id');
    }

    public function salesDirectReports()
    {
        return $this->hasMany(User::class, 'sales_reports_to_id');
    }

    public function salesCourseCommissionAgreements()
    {
        return $this->hasMany(SalesCourseCommissionAgreement::class, 'user_id');
    }

    /**
     * أيام الإجازة الأسبوعية (Carbon dayOfWeek: 0=أحد … 6=سبت).
     *
     * @return array<int, string>
     */
    /**
     * يوم الإجازة الأسبوعية (0=أحد … 6=سبت). null = افتراضي سبت/أحد.
     * لا نستخدم cast integer حتى لا تتحول القيمة الفارغة إلى 0 (الأحد).
     */
    protected function weeklyOffDay(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): ?int {
                if ($value === null || $value === '') {
                    return null;
                }

                return (int) $value;
            },
            set: function (mixed $value): ?int {
                if ($value === null || $value === '') {
                    return null;
                }

                return (int) $value;
            },
        );
    }

    public static function weeklyOffDayOptions(): array
    {
        return [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];
    }

    public function weeklyOffDayLabel(): ?string
    {
        if ($this->weekly_off_day === null) {
            return null;
        }

        return self::weeklyOffDayOptions()[(int) $this->weekly_off_day] ?? null;
    }

    /**
     * هل التاريخ يوافق يوم الإجازة الأسبوعية للموظف؟
     * المصدر الوحيد: users.weekly_off_day من ملف الموظف (تعديل الموظف في لوحة الإدارة).
     * إن لم يُحدَّد يوم، يُعتمد عطلة نهاية الأسبوع (سبت/أحد).
     */
    public function isWeeklyOff(\Carbon\Carbon $date): bool
    {
        $offDay = $this->weekly_off_day;
        if ($offDay !== null) {
            return (int) $offDay === (int) $date->dayOfWeek;
        }

        return $date->isWeekend();
    }

    /**
     * هل اليوم يوم راحة من ناحية الحضور؟
     * إن وُجد work_week_plan: اليوم غير النشط = راحة.
     * للأوفلاين بأيام محددة بدون خطة: أيام العمل = onsite_days فقط.
     * للهجين بدون خطة: يعتمد weekly_off_day (الأيام المحددة أوفلاين/أونلاين فقط ضمن أيام العمل).
     */
    public function isAttendanceOffDay(\Carbon\Carbon $date): bool
    {
        if ($plan = $this->workPlanForDay($date)) {
            return ! ($plan['active'] ?? false);
        }

        if (
            $this->isOfflineWorker()
            && ($this->offline_attendance_type ?? null) === self::OFFLINE_SELECTED_DAYS
        ) {
            $days = $this->onsiteDayIndexes();
            if ($days === []) {
                return true;
            }

            return ! in_array((int) $date->dayOfWeek, $days, true);
        }

        return $this->isWeeklyOff($date);
    }

    /**
     * أقرب يوم إجازة أسبوعية اعتباراً من تاريخ معيّن (شامل اليوم إن كان راحة).
     */
    public function nextWeeklyOffDate(?\Carbon\Carbon $from = null): \Carbon\Carbon
    {
        $from = ($from ?? now())->copy()->startOfDay();

        for ($i = 0; $i < 8; $i++) {
            $day = $from->copy()->addDays($i);
            if ($this->isWeeklyOff($day)) {
                return $day;
            }
        }

        return $from->copy()->next(\Carbon\Carbon::SATURDAY)->startOfDay();
    }

    /**
     * إجازة معتمدة (طلب إجازة) تغطي هذا التاريخ.
     */
    public function isOnApprovedLeave(\Carbon\Carbon $date): bool
    {
        return $this->leaveRequests()
            ->approved()
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }

    /**
     * إذن غياب يوم صادر من مدير المبيعات (للأوفلاين).
     */
    public function hasSalesDayAbsencePermission(\Carbon\Carbon $date): bool
    {
        return SalesAttendancePermission::hasDayAbsence((int) $this->id, $date);
    }

    /**
     * إذن انصراف مبكر صادر من مدير المبيعات.
     */
    public function hasSalesEarlyDeparturePermission(\Carbon\Carbon $date): bool
    {
        return SalesAttendancePermission::hasEarlyDeparture((int) $this->id, $date);
    }

    /**
     * معفي من الحضور لهذا اليوم (إجازة إدارية أو إذن غياب يوم).
     */
    public function isAttendanceExcused(\Carbon\Carbon $date): bool
    {
        return $this->isOnApprovedLeave($date) || $this->hasSalesDayAbsencePermission($date);
    }

    /**
     * هل كان الموظف على رأس العمل في هذا التاريخ؟
     * تمنع احتساب أي التزامات أو خصومات قبل تاريخ التعيين أو بعد إنهاء الخدمة.
     */
    public function isEmployedOn(\Carbon\Carbon $date): bool
    {
        $day = $date->copy()->startOfDay();

        $hireDate = $this->hire_date ? \Carbon\Carbon::parse($this->hire_date)->startOfDay() : null;
        if ($hireDate && $day->lt($hireDate)) {
            return false;
        }

        $terminationDate = $this->termination_date
            ? \Carbon\Carbon::parse($this->termination_date)->startOfDay()
            : null;
        if ($terminationDate && $day->gt($terminationDate)) {
            return false;
        }

        return true;
    }

    /**
     * هل يُطلَب من الموظف تقرير يومي في هذا التاريخ؟
     */
    public function requiresDailyReportOn(\Carbon\Carbon $date): bool
    {
        return $this->isEmployedOn($date)
            && ! $this->isWeeklyOff($date)
            && ! $this->isAttendanceExcused($date);
    }

    /**
     * Scope للموظفين
     */
    public function scopeEmployees($query)
    {
        return $query->where('is_employee', true);
    }

    /**
     * موظفون بوظيفة مبيعات
     */
    public function scopeSalesEmployees($query)
    {
        return $query->employees()->whereHas('employeeJob', function ($q) {
            $q->whereRaw('LOWER(code) = ?', ['sales']);
        });
    }

    public function scopeSalesManagers($query)
    {
        return $query->employees()->whereHas('employeeJob', function ($q) {
            $q->whereRaw('LOWER(code) = ?', ['sales_manager']);
        });
    }

    public function scopeSalesStaff($query)
    {
        return $query->employees()->whereHas('employeeJob', function ($q) {
            $q->where(function ($inner) {
                $inner->whereRaw('LOWER(code) = ?', ['sales'])
                    ->orWhereRaw('LOWER(code) = ?', ['sales_manager'])
                    ->orWhereRaw('LOWER(code) = ?', ['business_developer']);
            });
        });
    }

    public function scopeBusinessDevelopers($query)
    {
        return $query->employees()->whereHas('employeeJob', function ($q) {
            $q->whereRaw('LOWER(code) = ?', ['business_developer']);
        });
    }

    public function scopeModeratorEmployees($query)
    {
        return $query->employees()->whereHas('employeeJob', function ($q) {
            $q->whereRaw('LOWER(code) = ?', ['moderator']);
        });
    }

    public function scopeDesignerEmployees($query)
    {
        return $query->employees()->whereHas('employeeJob', function ($q) {
            $q->whereRaw('LOWER(code) = ?', ['designer']);
        });
    }

    public function scopeVideoEditingEmployees($query)
    {
        $codes = \App\Models\EmployeeJob::videoEditingCodes();

        return $query->employees()->whereHas('employeeJob', function ($q) use ($codes) {
            $q->where(function ($inner) use ($codes) {
                foreach ($codes as $code) {
                    $inner->orWhereRaw('LOWER(code) = ?', [strtolower($code)]);
                }
            });
        });
    }

    /**
     * التحقق من وجود صلاحية معينة (من الأدوار أو المباشرة)
     */
    public function hasPermissionDirect($permissionName)
    {
        // إذا كان admin، يعيد true دائماً
        if ($this->isAdmin()) {
            return true;
        }

        // التحقق من الصلاحيات المباشرة
        if ($this->directPermissions()->where('name', $permissionName)->exists()) {
            return true;
        }

        // التحقق من الصلاحيات من الأدوار
        return $this->roles()->whereHas('permissions', function($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->exists();
    }

    /**
     * علاقة مع الإحالات (كمحيل)
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * علاقة مع الإحالة (كمحال)
     */
    public function referral()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * علاقة مع المستخدم الذي أحاله
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * علاقة مع المستخدمين المحالين
     */
    public function referredUsers()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /**
     * علاقة مع تسجيلات المسارات التعليمية
     */
    public function learningPathEnrollments()
    {
        return $this->hasMany(LearningPathEnrollment::class, 'user_id');
    }

    /**
     * علاقة مع المسارات التعليمية التي يدرب فيها
     */
    public function teachingLearningPaths()
    {
        return $this->belongsToMany(AcademicYear::class, 'academic_year_instructors', 'instructor_id', 'academic_year_id')
            ->withPivot('assigned_courses', 'notes')
            ->withTimestamps();
    }
}