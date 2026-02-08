<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود جدول permissions
        if (!\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
            $this->command->warn('⚠️  جدول permissions غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        // إنشاء الصلاحيات الأساسية
        $permissions = [
            // إدارة المستخدمين
            ['name' => 'users.view', 'display_name' => 'عرض المستخدمين', 'group' => 'users'],
            ['name' => 'users.create', 'display_name' => 'إنشاء مستخدمين', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'تعديل المستخدمين', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'حذف المستخدمين', 'group' => 'users'],
            ['name' => 'users.permissions', 'display_name' => 'إدارة صلاحيات المستخدمين', 'group' => 'users'],
            
            // إدارة الكورسات
            ['name' => 'courses.view', 'display_name' => 'عرض الكورسات', 'group' => 'courses'],
            ['name' => 'courses.create', 'display_name' => 'إنشاء كورسات', 'group' => 'courses'],
            ['name' => 'courses.edit', 'display_name' => 'تعديل الكورسات', 'group' => 'courses'],
            ['name' => 'courses.delete', 'display_name' => 'حذف الكورسات', 'group' => 'courses'],
            ['name' => 'courses.manage_own', 'display_name' => 'إدارة كورساته الخاصة', 'group' => 'courses'],
            
            // إدارة المحاضرات
            ['name' => 'lectures.view', 'display_name' => 'عرض المحاضرات', 'group' => 'lectures'],
            ['name' => 'lectures.create', 'display_name' => 'إنشاء محاضرات', 'group' => 'lectures'],
            ['name' => 'lectures.edit', 'display_name' => 'تعديل المحاضرات', 'group' => 'lectures'],
            ['name' => 'lectures.delete', 'display_name' => 'حذف المحاضرات', 'group' => 'lectures'],
            ['name' => 'lectures.manage_own', 'display_name' => 'إدارة محاضراته الخاصة', 'group' => 'lectures'],
            
            // إدارة الواجبات
            ['name' => 'assignments.view', 'display_name' => 'عرض الواجبات', 'group' => 'assignments'],
            ['name' => 'assignments.create', 'display_name' => 'إنشاء واجبات', 'group' => 'assignments'],
            ['name' => 'assignments.grade', 'display_name' => 'تصحيح الواجبات', 'group' => 'assignments'],
            ['name' => 'assignments.delete', 'display_name' => 'حذف الواجبات', 'group' => 'assignments'],
            
            // إدارة الامتحانات
            ['name' => 'exams.view', 'display_name' => 'عرض الامتحانات', 'group' => 'exams'],
            ['name' => 'exams.create', 'display_name' => 'إنشاء امتحانات', 'group' => 'exams'],
            ['name' => 'exams.edit', 'display_name' => 'تعديل الامتحانات', 'group' => 'exams'],
            ['name' => 'exams.delete', 'display_name' => 'حذف الامتحانات', 'group' => 'exams'],
            ['name' => 'exams.take', 'display_name' => 'أداء الامتحانات', 'group' => 'exams'],
            
            // إدارة الفواتير والمدفوعات
            ['name' => 'invoices.view', 'display_name' => 'عرض الفواتير', 'group' => 'finance'],
            ['name' => 'invoices.create', 'display_name' => 'إنشاء فواتير', 'group' => 'finance'],
            ['name' => 'invoices.edit', 'display_name' => 'تعديل الفواتير', 'group' => 'finance'],
            ['name' => 'payments.view', 'display_name' => 'عرض المدفوعات', 'group' => 'finance'],
            ['name' => 'payments.create', 'display_name' => 'تسجيل مدفوعات', 'group' => 'finance'],
            ['name' => 'wallets.view', 'display_name' => 'عرض المحافظ', 'group' => 'finance'],
            ['name' => 'wallets.manage', 'display_name' => 'إدارة المحافظ', 'group' => 'finance'],
            
            // إدارة المحتوى (CMS)
            ['name' => 'blog.view', 'display_name' => 'عرض المدونة', 'group' => 'cms'],
            ['name' => 'blog.create', 'display_name' => 'إنشاء مقالات', 'group' => 'cms'],
            ['name' => 'blog.edit', 'display_name' => 'تعديل المقالات', 'group' => 'cms'],
            ['name' => 'blog.delete', 'display_name' => 'حذف المقالات', 'group' => 'cms'],
            ['name' => 'faq.manage', 'display_name' => 'إدارة الأسئلة الشائعة', 'group' => 'cms'],
            ['name' => 'pages.manage', 'display_name' => 'إدارة الصفحات العامة', 'group' => 'cms'],
            
            // إدارة المهام
            ['name' => 'tasks.view', 'display_name' => 'عرض المهام', 'group' => 'tasks'],
            ['name' => 'tasks.create', 'display_name' => 'إنشاء مهام', 'group' => 'tasks'],
            ['name' => 'tasks.edit', 'display_name' => 'تعديل المهام', 'group' => 'tasks'],
            ['name' => 'tasks.delete', 'display_name' => 'حذف المهام', 'group' => 'tasks'],
            
            // إدارة الإشعارات
            ['name' => 'notifications.view', 'display_name' => 'عرض الإشعارات', 'group' => 'notifications'],
            ['name' => 'notifications.send', 'display_name' => 'إرسال إشعارات', 'group' => 'notifications'],
            
            // إدارة الشهادات
            ['name' => 'certificates.view', 'display_name' => 'عرض الشهادات', 'group' => 'certificates'],
            ['name' => 'certificates.generate', 'display_name' => 'إنشاء شهادات', 'group' => 'certificates'],
            
            // إدارة التقارير
            ['name' => 'reports.view', 'display_name' => 'عرض التقارير', 'group' => 'reports'],
            ['name' => 'reports.financial', 'display_name' => 'التقارير المالية', 'group' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // التحقق من وجود جدول roles
        if (!\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $this->command->warn('⚠️  جدول roles غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        // إنشاء الأدوار الأساسية
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'المدير العام',
                'description' => 'تحكم شامل في كل شيء',
                'is_system' => true,
            ]
        );

        $instructorRole = Role::firstOrCreate(
            ['name' => 'instructor'],
            [
                'display_name' => 'المدرس',
                'description' => 'إدارة كورساته، إضافة محاضرات، متابعة الطلاب',
                'is_system' => true,
            ]
        );

        $studentRole = Role::firstOrCreate(
            ['name' => 'student'],
            [
                'display_name' => 'الطالب',
                'description' => 'مشاهدة الكورسات، درجات، واجبات، فواتير',
                'is_system' => true,
            ]
        );

        $accountantRole = Role::firstOrCreate(
            ['name' => 'accountant'],
            [
                'display_name' => 'المحاسب',
                'description' => 'الوصول للفواتير والمدفوعات فقط',
                'is_system' => true,
            ]
        );

        $callCenterRole = Role::firstOrCreate(
            ['name' => 'call_center'],
            [
                'display_name' => 'موظف الكول سنتر',
                'description' => 'رؤية بيانات الطلاب للتواصل فقط',
                'is_system' => true,
            ]
        );

        // إعطاء جميع الصلاحيات للمدير العام
        $adminRole->permissions()->sync(Permission::pluck('id'));

        // صلاحيات المدرس
        $instructorPermissions = Permission::whereIn('name', [
            'courses.view', 'courses.manage_own', 'courses.create', 'courses.edit',
            'lectures.view', 'lectures.manage_own', 'lectures.create', 'lectures.edit',
            'assignments.view', 'assignments.create', 'assignments.grade',
            'exams.view', 'exams.create', 'exams.edit',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'notifications.view',
            'certificates.view',
        ])->pluck('id');
        $instructorRole->permissions()->sync($instructorPermissions);

        // صلاحيات الطالب
        $studentPermissions = Permission::whereIn('name', [
            'courses.view',
            'lectures.view',
            'assignments.view',
            'exams.view', 'exams.take',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'invoices.view',
            'certificates.view',
            // صلاحيات الطالب الجديدة
            'student.view.courses',
            'student.view.my-courses',
            'student.view.orders',
            'student.view.invoices',
            'student.view.wallet',
            'student.view.certificates',
            'student.view.achievements',
            'student.view.exams',
            'student.view.calendar',
            'student.view.notifications',
            'student.view.profile',
            'student.view.settings',
        ])->pluck('id');
        $studentRole->permissions()->sync($studentPermissions);

        // صلاحيات المحاسب
        $accountantPermissions = Permission::whereIn('name', [
            'invoices.view', 'invoices.create', 'invoices.edit',
            'payments.view', 'payments.create',
            'wallets.view',
            'reports.view', 'reports.financial',
        ])->pluck('id');
        $accountantRole->permissions()->sync($accountantPermissions);

        // صلاحيات الكول سنتر
        $callCenterPermissions = Permission::whereIn('name', [
            'users.view',
            'courses.view',
            'invoices.view',
            'notifications.view', 'notifications.send',
        ])->pluck('id');
        $callCenterRole->permissions()->sync($callCenterPermissions);
    }
}
