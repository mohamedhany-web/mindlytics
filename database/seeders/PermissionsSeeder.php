<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // إدارة النظام
            ['name' => 'view.dashboard', 'display_name' => 'عرض لوحة التحكم', 'description' => 'إمكانية الوصول إلى لوحة التحكم', 'group' => 'إدارة النظام'],
            ['name' => 'manage.users', 'display_name' => 'إدارة المستخدمين', 'description' => 'إدارة المستخدمين (عرض، إضافة، تعديل، حذف)', 'group' => 'إدارة النظام'],
            ['name' => 'manage.orders', 'display_name' => 'إدارة الطلبات', 'description' => 'إدارة طلبات التسجيل في الكورسات', 'group' => 'إدارة النظام'],
            ['name' => 'manage.notifications', 'display_name' => 'إدارة الإشعارات', 'description' => 'إرسال وإدارة الإشعارات', 'group' => 'إدارة النظام'],
            ['name' => 'view.activity-log', 'display_name' => 'عرض سجل النشاطات', 'description' => 'عرض سجل نشاطات المستخدمين', 'group' => 'إدارة النظام'],
            ['name' => 'view.statistics', 'display_name' => 'عرض الإحصائيات', 'description' => 'عرض إحصائيات المنصة', 'group' => 'إدارة النظام'],
            ['name' => 'manage.roles', 'display_name' => 'إدارة الأدوار', 'description' => 'إدارة الأدوار والصلاحيات', 'group' => 'إدارة النظام'],
            ['name' => 'manage.permissions', 'display_name' => 'إدارة الصلاحيات', 'description' => 'إدارة الصلاحيات', 'group' => 'إدارة النظام'],
            ['name' => 'manage.user-permissions', 'display_name' => 'إدارة صلاحيات المستخدمين', 'description' => 'إدارة صلاحيات المستخدمين مباشرة', 'group' => 'إدارة النظام'],

            // إدارة المحاسبة
            ['name' => 'manage.invoices', 'display_name' => 'إدارة الفواتير', 'description' => 'إدارة الفواتير', 'group' => 'إدارة المحاسبة'],
            ['name' => 'manage.payments', 'display_name' => 'إدارة المدفوعات', 'description' => 'إدارة المدفوعات', 'group' => 'إدارة المحاسبة'],
            ['name' => 'manage.transactions', 'display_name' => 'إدارة المعاملات المالية', 'description' => 'إدارة المعاملات المالية', 'group' => 'إدارة المحاسبة'],
            ['name' => 'manage.wallets', 'display_name' => 'إدارة المحافظ', 'description' => 'إدارة محافظ المستخدمين', 'group' => 'إدارة المحاسبة'],
            ['name' => 'manage.subscriptions', 'display_name' => 'إدارة الاشتراكات', 'description' => 'إدارة الاشتراكات', 'group' => 'إدارة المحاسبة'],
            ['name' => 'manage.installments', 'display_name' => 'إدارة خطط التقسيط', 'description' => 'إدارة خطط التقسيط والاشتراكات الذكية', 'group' => 'إدارة المحاسبة'],

            // إدارة التسويق
            ['name' => 'manage.coupons', 'display_name' => 'إدارة الكوبونات', 'description' => 'إدارة الكوبونات والخصومات', 'group' => 'إدارة التسويق'],
            ['name' => 'manage.referrals', 'display_name' => 'إدارة برنامج الإحالات', 'description' => 'إدارة برنامج الإحالات', 'group' => 'إدارة التسويق'],
            ['name' => 'manage.loyalty', 'display_name' => 'إدارة برامج الولاء', 'description' => 'إدارة برامج الولاء', 'group' => 'إدارة التسويق'],

            // الشهادات والإنجازات
            ['name' => 'manage.certificates', 'display_name' => 'إدارة الشهادات', 'description' => 'إدارة الشهادات', 'group' => 'الشهادات والإنجازات'],
            ['name' => 'manage.achievements', 'display_name' => 'إدارة الإنجازات', 'description' => 'إدارة الإنجازات', 'group' => 'الشهادات والإنجازات'],
            ['name' => 'manage.badges', 'display_name' => 'إدارة الشارات', 'description' => 'إدارة الشارات', 'group' => 'الشهادات والإنجازات'],
            ['name' => 'manage.reviews', 'display_name' => 'إدارة التقييمات', 'description' => 'إدارة التقييمات والمراجعات', 'group' => 'الشهادات والإنجازات'],

            // إدارة المحتوى
            ['name' => 'manage.academic-years', 'display_name' => 'إدارة السنوات الدراسية', 'description' => 'إدارة السنوات الدراسية', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.academic-subjects', 'display_name' => 'إدارة المواد الدراسية', 'description' => 'إدارة المواد الدراسية', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.courses', 'display_name' => 'إدارة الكورسات', 'description' => 'إدارة الكورسات والدروس', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.enrollments', 'display_name' => 'إدارة تسجيل الطلاب', 'description' => 'إدارة تسجيل الطلاب في الكورسات', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.lectures', 'display_name' => 'إدارة المحاضرات', 'description' => 'إدارة المحاضرات', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.groups', 'display_name' => 'إدارة المجموعات', 'description' => 'إدارة المجموعات الدراسية', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.assignments', 'display_name' => 'إدارة الواجبات', 'description' => 'إدارة الواجبات والمشاريع', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.exams', 'display_name' => 'إدارة الامتحانات', 'description' => 'إدارة الامتحانات', 'group' => 'إدارة المحتوى'],
            ['name' => 'manage.question-bank', 'display_name' => 'إدارة بنك الأسئلة', 'description' => 'إدارة بنك الأسئلة', 'group' => 'إدارة المحتوى'],

            // إدارة الصفحات الخارجية
            ['name' => 'manage.blog', 'display_name' => 'إدارة المدونة', 'description' => 'إدارة مقالات المدونة', 'group' => 'إدارة الصفحات الخارجية'],
            ['name' => 'manage.faq', 'display_name' => 'إدارة الأسئلة الشائعة', 'description' => 'إدارة الأسئلة الشائعة', 'group' => 'إدارة الصفحات الخارجية'],
            ['name' => 'manage.contact-messages', 'display_name' => 'إدارة رسائل التواصل', 'description' => 'إدارة رسائل التواصل', 'group' => 'إدارة الصفحات الخارجية'],
            ['name' => 'manage.media', 'display_name' => 'إدارة معرض الصور', 'description' => 'إدارة معرض الصور والفيديوهات', 'group' => 'إدارة الصفحات الخارجية'],

            // المهام
            ['name' => 'manage.tasks', 'display_name' => 'إدارة المهام', 'description' => 'إدارة المهام', 'group' => 'المهام'],
            ['name' => 'view.tasks', 'display_name' => 'عرض المهام', 'description' => 'عرض المهام المخصصة', 'group' => 'المهام'],

            // المحافظ الذكية
            ['name' => 'view.wallets', 'display_name' => 'عرض المحافظ', 'description' => 'عرض المحافظ الذكية', 'group' => 'المحافظ الذكية'],

            // الرسائل والتقارير
            ['name' => 'manage.messages', 'display_name' => 'إدارة الرسائل', 'description' => 'إدارة الرسائل والتقارير', 'group' => 'الرسائل والتقارير'],

            // التقويم
            ['name' => 'view.calendar', 'display_name' => 'عرض التقويم', 'description' => 'عرض التقويم', 'group' => 'التقويم'],

            // صلاحيات المدرب
            ['name' => 'instructor.view.courses', 'display_name' => 'عرض كورساتي', 'description' => 'عرض الكورسات الخاصة بالمدرب', 'group' => 'صلاحيات المدرب'],
            ['name' => 'instructor.manage.lectures', 'display_name' => 'إدارة محاضراتي', 'description' => 'إدارة المحاضرات الخاصة بالمدرب', 'group' => 'صلاحيات المدرب'],
            ['name' => 'instructor.manage.groups', 'display_name' => 'إدارة مجموعاتي', 'description' => 'إدارة المجموعات الخاصة بالمدرب', 'group' => 'صلاحيات المدرب'],
            ['name' => 'instructor.manage.assignments', 'display_name' => 'إدارة واجباتي', 'description' => 'إدارة الواجبات الخاصة بالمدرب', 'group' => 'صلاحيات المدرب'],
            ['name' => 'instructor.manage.exams', 'display_name' => 'إدارة اختباراتي', 'description' => 'إدارة الامتحانات الخاصة بالمدرب', 'group' => 'صلاحيات المدرب'],
            ['name' => 'instructor.manage.attendance', 'display_name' => 'إدارة الحضور', 'description' => 'إدارة الحضور والانصراف', 'group' => 'صلاحيات المدرب'],
            ['name' => 'instructor.view.tasks', 'display_name' => 'عرض مهامي', 'description' => 'عرض المهام الخاصة بالمدرب', 'group' => 'صلاحيات المدرب'],

            // صلاحيات الطالب
            ['name' => 'student.view.courses', 'display_name' => 'تصفح الكورسات', 'description' => 'تصفح الكورسات المتاحة', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.my-courses', 'display_name' => 'عرض كورساتي', 'description' => 'عرض الكورسات المسجل فيها', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.orders', 'display_name' => 'عرض طلباتي', 'description' => 'عرض طلبات التسجيل', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.invoices', 'display_name' => 'عرض فواتيري', 'description' => 'عرض الفواتير', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.wallet', 'display_name' => 'عرض محفظتي', 'description' => 'عرض المحفظة', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.certificates', 'display_name' => 'عرض شهاداتي', 'description' => 'عرض الشهادات', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.achievements', 'display_name' => 'عرض إنجازاتي', 'description' => 'عرض الإنجازات', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.exams', 'display_name' => 'عرض الامتحانات', 'description' => 'عرض الامتحانات المتاحة', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.calendar', 'display_name' => 'عرض التقويم', 'description' => 'عرض التقويم الأكاديمي', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.notifications', 'display_name' => 'عرض الإشعارات', 'description' => 'عرض الإشعارات', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.profile', 'display_name' => 'عرض البروفايل', 'description' => 'عرض وتعديل البروفايل', 'group' => 'صلاحيات الطالب'],
            ['name' => 'student.view.settings', 'display_name' => 'عرض الإعدادات', 'description' => 'عرض الإعدادات', 'group' => 'صلاحيات الطالب'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('تم إنشاء ' . count($permissions) . ' صلاحية بنجاح!');
    }
}
