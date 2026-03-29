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
            'video_editing' => [
                'name' => 'مونتاج فيديو',
                'code' => 'video_editing',
                'description' => 'إعداد وتسليم فيديوهات الدورات والمحتوى وفق مهام المونتاج في النظام.',
                'responsibilities' => 'استلام المهام؛ تسليم الروابط والمدة قبل/بعد؛ الالتزام بالمواعيد.',
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
        ];
    }
}
