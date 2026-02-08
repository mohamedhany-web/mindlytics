<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود الجداول
        if (!\Illuminate\Support\Facades\Schema::hasTable('message_templates')) {
            $this->command->warn('⚠️  جدول message_templates غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        // الحصول على أول مستخدم إداري أو إنشاء واحد
        $admin = User::where('role', 'admin')->orWhere('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrator',
                'email' => 'admin@platform.com',
                'phone' => '01000000000',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]);
        }

        $templates = [
            [
                'name' => 'welcome_new_student',
                'title' => 'رسالة ترحيب بالطالب الجديد',
                'content' => "🎓 مرحباً {student_name}!\n\nأهلاً وسهلاً بك في منصة مستر طارق الداجن للرياضيات 📐\n\nنحن سعداء لانضمامك إلينا ونتطلع لرحلة تعليمية مثمرة معك!\n\n📱 يمكنك الآن:\n• الوصول لجميع الكورسات المسجل بها\n• مشاهدة الفيديوهات التعليمية\n• أداء الامتحانات والاختبارات\n• تتبع تقدمك الدراسي\n\n📞 للدعم الفني: {support_phone}\n🌐 {platform_name}",
                'type' => 'welcome_message',
                'variables' => ['student_name', 'support_phone', 'platform_name'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'monthly_report_student',
                'title' => 'التقرير الشهري للطالب',
                'content' => "📊 تقريرك الشهري - {month_name}\n\n👤 عزيزي {student_name}،\n\nإليك ملخص أدائك هذا الشهر:\n\n📚 الكورسات المسجل بها: {courses_count}\n📈 متوسط درجاتك: {avg_score}%\n🎯 عدد الامتحانات: {total_exams}\n⭐ تقييمك العام: {overall_grade}\n\n💡 توصياتنا:\n{recommendation}\n\n🎓 استمر في التقدم ولا تستسلم!\n📱 منصة مستر طارق الداجن",
                'type' => 'student_report',
                'variables' => ['student_name', 'month_name', 'courses_count', 'avg_score', 'total_exams', 'overall_grade', 'recommendation'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'exam_result_notification',
                'title' => 'إشعار نتيجة امتحان',
                'content' => "🎯 نتيجة امتحان جديدة!\n\n👤 عزيزي {student_name}،\n\n📝 الامتحان: {exam_title}\n📊 نتيجتك: {score}/{total_marks}\n📈 النسبة المئوية: {percentage}%\n✅ الحالة: {status}\n📅 التاريخ: {date}\n\n" . ('{percentage}' >= '60' ? "🎉 مبروك! أداء رائع!" : "📖 استمر في المراجعة والتحسين") . "\n\n📱 منصة مستر طارق الداجن",
                'type' => 'exam_result',
                'variables' => ['student_name', 'exam_title', 'score', 'total_marks', 'percentage', 'status', 'date'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'parent_monthly_report',
                'title' => 'التقرير الشهري لولي الأمر',
                'content' => "📊 التقرير الشهري - {month_name}\n\n👨‍👩‍👧‍👦 عزيزي {parent_name}،\n\nإليك تقرير شامل عن {student_name}:\n\n📚 تقدم الكورسات:\n{courses_progress}\n\n🎯 نتائج الامتحانات:\n{exam_results}\n\n📈 الأداء العام:\n• متوسط الدرجات: {avg_score}%\n• التقييم: {overall_grade}\n• الأيام النشطة: {active_days}\n\n💡 ملاحظات وتوصيات:\n{recommendation}\n\nشكراً لثقتكم بنا\n📱 منصة مستر طارق الداجن\n📞 للاستفسارات: {support_phone}",
                'type' => 'parent_report',
                'variables' => ['parent_name', 'student_name', 'month_name', 'courses_progress', 'exam_results', 'avg_score', 'overall_grade', 'active_days', 'recommendation', 'support_phone'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'course_reminder',
                'title' => 'تذكير بالكورس',
                'content' => "📚 تذكير بالكورس\n\n👤 عزيزي {student_name}،\n\n🎓 لديك درس جديد في كورس: {course_title}\n📝 عنوان الدرس: {lesson_title}\n📅 متاح الآن للمشاهدة\n\n⏰ لا تنس مواكبة دروسك للحصول على أفضل النتائج!\n\n📱 منصة مستر طارق الداجن",
                'type' => 'course_reminder',
                'variables' => ['student_name', 'course_title', 'lesson_title'],
                'created_by' => $admin->id,
            ],
            [
                'name' => 'general_announcement',
                'title' => 'إعلان عام',
                'content' => "📢 إعلان مهم\n\n👤 عزيزي {student_name}،\n\n{announcement_content}\n\n📅 التاريخ: {date}\n\n📱 منصة مستر طارق الداجن\n📞 للاستفسارات: {support_phone}",
                'type' => 'general_announcement',
                'variables' => ['student_name', 'announcement_content', 'date', 'support_phone'],
                'created_by' => $admin->id,
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::create($template);
        }

        $this->command->info('تم إنشاء ' . count($templates) . ' قالب رسالة أساسي بنجاح');
    }
}