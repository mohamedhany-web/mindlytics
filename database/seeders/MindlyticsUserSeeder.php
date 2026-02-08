<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MindlyticsUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود جدول users
        if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
            $this->command->warn('⚠️  جدول users غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        // إنشاء مستخدم إداري (Admin)
        User::firstOrCreate(
            ['phone' => '0500000000'],
            [
                'name' => 'المدير العام',
                'email' => 'admin@mindlytics.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // إنشاء مدرب (Teacher/Instructor)
        User::firstOrCreate(
            ['phone' => '0500000001'],
            [
                'name' => 'أحمد المدرب',
                'email' => 'instructor@mindlytics.com',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'is_active' => true,
                'bio' => 'مدرب برمجة محترف مع أكثر من 10 سنوات من الخبرة',
            ]
        );

        // إنشاء طالب (Student)
        User::firstOrCreate(
            ['phone' => '0500000002'],
            [
                'name' => 'فاطمة الطالبة',
                'email' => 'student@mindlytics.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ تم إنشاء المستخدمين بنجاح!');
        $this->command->info('');
        $this->command->info('📋 بيانات الدخول:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('👨‍💼 المدير العام (Admin):');
        $this->command->info('   📱 رقم الهاتف: 0500000000');
        $this->command->info('   📧 البريد: admin@mindlytics.com');
        $this->command->info('   🔑 كلمة المرور: password123');
        $this->command->info('');
        $this->command->info('👨‍🏫 المدرب (Teacher):');
        $this->command->info('   📱 رقم الهاتف: 0500000001');
        $this->command->info('   📧 البريد: instructor@mindlytics.com');
        $this->command->info('   🔑 كلمة المرور: password123');
        $this->command->info('');
        $this->command->info('👨‍🎓 الطالب (Student):');
        $this->command->info('   📱 رقم الهاتف: 0500000002');
        $this->command->info('   📧 البريد: student@mindlytics.com');
        $this->command->info('   🔑 كلمة المرور: password123');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}

