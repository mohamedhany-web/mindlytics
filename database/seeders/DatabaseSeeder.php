<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 بدء عملية Seed للبيانات...');
        $this->command->newLine();

        // 1. إنشاء الأدوار والصلاحيات أولاً (إذا كانت الجداول موجودة)
        if (\Illuminate\Support\Facades\Schema::hasTable('permissions') && \Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $this->command->info('📋 1. إنشاء الأدوار والصلاحيات...');
            $this->call([
                PermissionsAndRolesSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء الأدوار والصلاحيات');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جداول permissions/roles غير موجودة. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 2. إنشاء السنوات الأكاديمية والمواد
        $this->command->info('📚 2. إنشاء السنوات الأكاديمية والمواد...');
        $this->call([
            AcademicYearSeeder::class,
            SubjectsSeeder::class,
        ]);
        $this->command->info('✅ تم إنشاء السنوات الأكاديمية والمواد');
        $this->command->newLine();

        // 3. إنشاء المستخدمين
        $this->command->info('👥 3. إنشاء المستخدمين...');
        $this->call([
            MindlyticsUserSeeder::class,
        ]);
        $this->command->info('✅ تم إنشاء المستخدمين');
        $this->command->newLine();

        // 4. إنشاء نظام المحاسبة (اختياري - يحتاج كورسات وطلاب)
        if (\Illuminate\Support\Facades\Schema::hasTable('wallets') && \Illuminate\Support\Facades\Schema::hasTable('orders')) {
            $this->command->info('💰 4. إنشاء نظام المحاسبة...');
            try {
                $this->call([
                    AccountingSystemSeeder::class,
                ]);
                $this->command->info('✅ تم إنشاء نظام المحاسبة');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  فشل إنشاء بيانات المحاسبة: ' . $e->getMessage());
            }
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جداول المحاسبة غير موجودة. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 5. إنشاء قوالب الرسائل
        if (\Illuminate\Support\Facades\Schema::hasTable('message_templates')) {
            $this->command->info('📧 5. إنشاء قوالب الرسائل...');
            $this->call([
                MessageTemplateSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء قوالب الرسائل');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جدول message_templates غير موجود. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 6. إنشاء برامج الإحالة
        if (\Illuminate\Support\Facades\Schema::hasTable('referral_programs')) {
            $this->command->info('🎁 6. إنشاء برامج الإحالة...');
            $this->call([
                ReferralProgramSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء برامج الإحالة');
            $this->command->newLine();
        } else {
            $this->command->warn('⚠️  جدول referral_programs غير موجود. سيتم تخطي هذا الخطوة.');
            $this->command->newLine();
        }

        // 6.5 قوالب واتساب مقترحة للسيلز (اختياري)
        if (\Illuminate\Support\Facades\Schema::hasTable('whatsapp_suggested_templates')) {
            $this->command->info('💬 6.5 إنشاء مكتبة قوالب واتساب المقترحة...');
            try {
                $this->call([
                    WhatsAppSuggestedTemplatesSeeder::class,
                ]);
                $this->command->info('✅ تم إنشاء قوالب واتساب المقترحة');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  فشل إنشاء قوالب واتساب المقترحة: ' . $e->getMessage());
            }
            $this->command->newLine();
        }

        // 7. إنشاء كورسات تجريبية (اختياري)
        if ($this->command->confirm('هل تريد إنشاء كورسات تجريبية؟', false)) {
            $this->command->info('📖 7. إنشاء كورسات تجريبية...');
            $this->call([
                CoursesSeeder::class,
            ]);
            $this->command->info('✅ تم إنشاء كورسات تجريبية');
            $this->command->newLine();
        }

        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('✨ تم إكمال عملية Seed بنجاح!');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->newLine();
        $this->command->info('📋 بيانات الدخول الافتراضية:');
        $this->command->info('   👨‍💼 المدير: 0500000000 / password123');
        $this->command->info('   👨‍🏫 المدرب: 0500000001 / password123');
        $this->command->info('   👨‍🎓 الطالب: 0500000002 / password123');
        $this->command->newLine();
    }
}
