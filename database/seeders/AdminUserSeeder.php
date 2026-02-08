<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم إداري
        $adminUser = User::firstOrCreate(
            ['phone' => '0500000000'],
            [
                'name' => 'المدير العام',
                'email' => 'admin@learningplatform.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        if ($adminUser) {
            $installmentPermission = \App\Models\Permission::firstWhere('name', 'manage.installments');
            if ($installmentPermission && !$adminUser->directPermissions()->where('permission_id', $installmentPermission->id)->exists()) {
                $adminUser->directPermissions()->attach($installmentPermission->id);
            }
        }

        // إنشاء مدرس تجريبي
        User::firstOrCreate(
            ['phone' => '0500000001'],
            [
                'name' => 'أحمد محمد',
                'email' => 'teacher@learningplatform.com',
                'password' => Hash::make('password123'),
                'role' => 'teacher',
                'is_active' => true,
                'bio' => 'مدرس رياضيات مع خبرة 10 سنوات في التدريس',
            ]
        );

        // إنشاء طالب تجريبي
        User::firstOrCreate(
            ['phone' => '0500000002'],
            [
                'name' => 'فاطمة علي',
                'email' => 'student@learningplatform.com',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'is_active' => true,
            ]
        );

        // إنشاء ولي أمر تجريبي
        User::firstOrCreate(
            ['phone' => '0500000003'],
            [
                'name' => 'محمد أحمد',
                'email' => 'parent@learningplatform.com',
                'password' => Hash::make('password123'),
                'role' => 'parent',
                'is_active' => true,
            ]
        );

        // إنشاء مدرسة تجريبية
        School::firstOrCreate(
            ['name' => 'مدرسة النور الابتدائية'],
            [
                'description' => 'مدرسة ابتدائية متميزة تهتم بتطوير قدرات الطلاب',
                'address' => 'الرياض، المملكة العربية السعودية',
                'phone' => '0112345678',
                'email' => 'info@alnoor.edu.sa',
                'is_active' => true,
            ]
        );

        // إنشاء مواد دراسية تجريبية
        $subjects = [
            [
                'name' => 'الرياضيات',
                'description' => 'تعلم الأرقام والعمليات الحسابية والهندسة',
                'color' => '#3B82F6',
                'icon' => 'fas fa-calculator',
            ],
            [
                'name' => 'العلوم',
                'description' => 'استكشاف الطبيعة والكيمياء والفيزياء',
                'color' => '#10B981',
                'icon' => 'fas fa-flask',
            ],
            [
                'name' => 'اللغة العربية',
                'description' => 'تطوير مهارات القراءة والكتابة والتعبير',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-book',
            ],
            [
                'name' => 'اللغة الإنجليزية',
                'description' => 'تعلم اللغة الإنجليزية من الأساسيات إلى المستوى المتقدم',
                'color' => '#F59E0B',
                'icon' => 'fas fa-globe',
            ],
            [
                'name' => 'التاريخ',
                'description' => 'دراسة الأحداث التاريخية والحضارات',
                'color' => '#EF4444',
                'icon' => 'fas fa-landmark',
            ],
            [
                'name' => 'الجغرافيا',
                'description' => 'دراسة الأرض والبيئة والمناخ',
                'color' => '#06B6D4',
                'icon' => 'fas fa-map',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['name' => $subject['name']],
                $subject
            );
        }

        $this->command->info('تم إنشاء المستخدمين والبيانات التجريبية بنجاح!');
        $this->command->info('بيانات الدخول:');
        $this->command->info('المدير: 0500000000 / password123');
        $this->command->info('المدرس: 0500000001 / password123');
        $this->command->info('الطالب: 0500000002 / password123');
        $this->command->info('ولي الأمر: 0500000003 / password123');
    }
}
