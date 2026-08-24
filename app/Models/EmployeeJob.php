<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeJob extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'responsibilities',
        'permissions',
        'min_salary',
        'max_salary',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع الموظفين
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'employee_job_id');
    }

    /**
     * Scope للوظائف النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * قوالب وظائف جاهزة لصفحة الإنشاء (الرمز يُستخدم في النظام مثل sales ومهام المونتاج).
     *
     * @return array<string, array{name: string, code: string, description: string, responsibilities: string}>
     */
    public static function presetJobTemplates(): array
    {
        return [
            'sales' => [
                'name' => 'مبيعات',
                'code' => 'sales',
                'description' => 'متابعة العملاء المحتملين، العروض، وإغلاق الصفقات ضمن نظام المبيعات في المنصة.',
                'responsibilities' => 'تسجيل العملاء المحتملين والمتابعات؛ استخدام مركز المبيعات؛ التنسيق مع الإدارة.',
            ],
            'sales_manager' => [
                'name' => 'مدير مبيعات',
                'code' => 'sales_manager',
                'description' => 'إدارة فريق المبيعات، متابعة الأداء، تحويل العملاء بين أعضاء الفريق، ورفع التقارير للإدارة.',
                'responsibilities' => 'متابعة فريق السيلز؛ مراقبة المحادثات والحضور؛ تحويل Leads؛ مراجعة التقارير اليومية؛ رفع تقرير الفريق للإدارة.',
            ],
            'business_developer' => [
                'name' => 'Business Developer',
                'code' => 'business_developer',
                'description' => 'قيادة المبيعات والتسويق: صلاحيات مدير المبيعات على كل بيانات الفريق، ومتابعة خطط الماركتينغ وطلبات التصميم والفيديو والمهام.',
                'responsibilities' => 'متابعة كل المبيعات والفريق؛ خطط التسويق؛ طلبات التصميم والمونتاج؛ المهام والتقويم؛ التنسيق بين المبيعات والميديا.',
            ],
            'video_editing' => [
                'name' => 'محرر فيديو',
                'code' => 'video_editing',
                'description' => 'مونتاج وتسليم فيديوهات التسويق والدورات وفق طلبات مشرف المحتوى والمواعيد.',
                'responsibilities' => 'استلام طلبات المونتاج؛ التسليم برابط أو ملف؛ الالتزام بحد التسليم.',
            ],
            'support' => [
                'name' => 'دعم فني وخدمة عملاء',
                'code' => 'support',
                'description' => 'الرد على استفسارات الطلاب والموظفين ومساعدتهم في استخدام المنصة.',
                'responsibilities' => 'تذاكر الدعم؛ المتابعة حتى حل المشكلة؛ التصعيد عند الحاجة.',
            ],
            'content' => [
                'name' => 'محتوى وتسويق',
                'code' => 'content',
                'description' => 'إعداد المحتوى التعليمي أو الترويجي والنشر على القنوات.',
                'responsibilities' => 'النصوص والمرئيات؛ التنسيق مع الفريق؛ الالتزام بالهوية.',
            ],
            'hr' => [
                'name' => 'موارد بشرية',
                'code' => 'hr',
                'description' => 'إدارة ملفات الموظفين والإجازات والتوظيف ضمن سياسات الأكاديمية.',
                'responsibilities' => 'متابعة الحضور والإجازات؛ التواصل مع الموظفين؛ الوثائق.',
            ],
            'accounting' => [
                'name' => 'محاسبة',
                'code' => 'accounting',
                'description' => 'متابعة المستحقات والرواتب والمصروفات والتقارير المالية.',
                'responsibilities' => 'التحقق من البيانات؛ التنسيق مع الإدارة؛ السرية المهنية.',
            ],
            'general' => [
                'name' => 'مهام عامة',
                'code' => 'general',
                'description' => 'وظيفة إدارية أو تشغيلية عامة داخل الأكاديمية.',
                'responsibilities' => 'تنفيذ المهام المسندة من الإدارة؛ الالتزام بالسياسات.',
            ],
            'moderator' => [
                'name' => 'مشرف محتوى',
                'code' => 'moderator',
                'description' => 'قيادة قسم الميديا: تنسيق طلبات التصميم مع المصممين وطلبات الفيديو مع محرري الفيديو حتى التسليم النهائي.',
                'responsibilities' => 'إنشاء طلبات تصميم وفيديو؛ متابعة التسليمات؛ إنشاء مهمة التسليم النهائي؛ خطط التسويق.',
            ],
            'designer' => [
                'name' => 'مصمم',
                'code' => 'designer',
                'description' => 'تنفيذ التصاميم المطلوبة من مشرف المحتوى وفق المواصفات والمواعيد.',
                'responsibilities' => 'استلام مهام التصميم؛ الرفع عبر تسليمات المهام؛ الالتزام بالحد الأقصى للتسليم.',
            ],
        ];
    }

    /**
     * رموز الوظائف المعتمدة لمحرر الفيديو (للتوافق مع أسماء قديمة/خاطئة).
     *
     * @return list<string>
     */
    public static function videoEditingCodes(): array
    {
        return ['video_editing', 'video_editor', 'montage', 'video_montage'];
    }

    /**
     * التأكد من وجود وظائف الميديا الأساسية برموز النظام الصحيحة.
     *
     * @return array{created: list<string>, repaired: list<string>}
     */
    public static function ensureMediaJobs(): array
    {
        $created = [];
        $repaired = [];

        foreach (['moderator', 'designer', 'video_editing'] as $code) {
            $preset = self::presetJobTemplates()[$code] ?? null;
            if (! $preset) {
                continue;
            }

            $existing = self::query()
                ->whereRaw('LOWER(code) = ?', [strtolower($code)])
                ->first();

            if ($existing) {
                $dirty = false;
                if (! $existing->is_active) {
                    $existing->is_active = true;
                    $dirty = true;
                }
                if (trim((string) $existing->name) === '') {
                    $existing->name = $preset['name'];
                    $dirty = true;
                }
                if ($dirty) {
                    $existing->save();
                    $repaired[] = $code;
                }

                continue;
            }

            // إصلاح وظيفة بنفس الاسم العربي لكن برمز خاطئ (مثل إنشاء يدوي).
            $byName = self::query()
                ->where(function ($q) use ($preset, $code) {
                    $q->where('name', $preset['name']);
                    if ($code === 'video_editing') {
                        $q->orWhere('name', 'like', '%محرر فيديو%')
                            ->orWhere('name', 'like', '%مونتاج%')
                            ->orWhere('name', 'like', '%محرر بيانات%');
                    }
                })
                ->where(function ($q) use ($code) {
                    $q->whereNull('code')
                        ->orWhereRaw('LOWER(code) != ?', [strtolower($code)]);
                })
                ->whereNotIn('code', ['sales', 'sales_manager', 'business_developer', 'moderator', 'designer'])
                ->first();

            if ($byName && $code === 'video_editing') {
                $byName->update([
                    'name' => $preset['name'],
                    'code' => $preset['code'],
                    'description' => $byName->description ?: $preset['description'],
                    'responsibilities' => $byName->responsibilities ?: $preset['responsibilities'],
                    'is_active' => true,
                ]);
                $repaired[] = $code;

                continue;
            }

            self::create([
                'name' => $preset['name'],
                'code' => $preset['code'],
                'description' => $preset['description'],
                'responsibilities' => $preset['responsibilities'],
                'is_active' => true,
            ]);
            $created[] = $code;
        }

        return compact('created', 'repaired');
    }

    /**
     * @return array{created: list<string>, repaired: list<string>}
     */
    public static function ensurePresetJob(string $code): array
    {
        $created = [];
        $repaired = [];
        $preset = self::presetJobTemplates()[$code] ?? null;
        if (! $preset) {
            return compact('created', 'repaired');
        }

        $existing = self::query()
            ->whereRaw('LOWER(code) = ?', [strtolower($code)])
            ->first();

        if ($existing) {
            $dirty = false;
            if (! $existing->is_active) {
                $existing->is_active = true;
                $dirty = true;
            }
            if (trim((string) $existing->name) === '') {
                $existing->name = $preset['name'];
                $dirty = true;
            }
            if ($dirty) {
                $existing->save();
                $repaired[] = $code;
            }

            return compact('created', 'repaired');
        }

        self::create([
            'name' => $preset['name'],
            'code' => $preset['code'],
            'description' => $preset['description'],
            'responsibilities' => $preset['responsibilities'],
            'is_active' => true,
        ]);
        $created[] = $code;

        return compact('created', 'repaired');
    }

    public function isVideoEditingJob(): bool
    {
        $code = strtolower((string) $this->code);

        return in_array($code, self::videoEditingCodes(), true);
    }
}
