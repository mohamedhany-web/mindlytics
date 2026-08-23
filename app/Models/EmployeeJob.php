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
}
