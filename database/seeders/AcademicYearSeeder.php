<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود الجداول
        if (!\Illuminate\Support\Facades\Schema::hasTable('academic_years')) {
            $this->command->warn('⚠️  جدول academic_years غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        // إنشاء سنوات دراسية تجريبية
        $years = [
            [
                'name' => 'الصف الأول الثانوي',
                'code' => 'G10',
                'description' => 'السنة الأولى من المرحلة الثانوية',
                'icon' => 'fa-graduation-cap',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => true,
                'subjects' => [
                    ['name' => 'البرمجة الأساسية', 'code' => 'PROG101', 'icon' => 'fa-code', 'color' => '#10B981', 'order' => 1],
                    ['name' => 'قواعد البيانات', 'code' => 'DB101', 'icon' => 'fa-database', 'color' => '#F59E0B', 'order' => 2],
                    ['name' => 'تطوير الويب', 'code' => 'WEB101', 'icon' => 'fa-globe', 'color' => '#8B5CF6', 'order' => 3],
                ],
            ],
            [
                'name' => 'الصف الثاني الثانوي',
                'code' => 'G11',
                'description' => 'السنة الثانية من المرحلة الثانوية',
                'icon' => 'fa-graduation-cap',
                'color' => '#10B981',
                'order' => 2,
                'is_active' => true,
                'subjects' => [
                    ['name' => 'البرمجة المتقدمة', 'code' => 'PROG201', 'icon' => 'fa-code', 'color' => '#EF4444', 'order' => 1],
                    ['name' => 'تطوير التطبيقات', 'code' => 'APP201', 'icon' => 'fa-mobile-alt', 'color' => '#06B6D4', 'order' => 2],
                ],
            ],
            [
                'name' => 'الصف الثالث الثانوي',
                'code' => 'G12',
                'description' => 'السنة الثالثة من المرحلة الثانوية',
                'icon' => 'fa-graduation-cap',
                'color' => '#8B5CF6',
                'order' => 3,
                'is_active' => true,
                'subjects' => [
                    ['name' => 'الذكاء الاصطناعي', 'code' => 'AI301', 'icon' => 'fa-robot', 'color' => '#EC4899', 'order' => 1],
                    ['name' => 'الأمن السيبراني', 'code' => 'SEC301', 'icon' => 'fa-shield-alt', 'color' => '#F59E0B', 'order' => 2],
                ],
            ],
        ];

        foreach ($years as $yearData) {
            $subjects = $yearData['subjects'] ?? [];
            unset($yearData['subjects']);
            
            $year = AcademicYear::firstOrCreate(
                ['code' => $yearData['code']],
                $yearData
            );
            
            foreach ($subjects as $subjectData) {
                AcademicSubject::firstOrCreate(
                    [
                        'code' => $subjectData['code'],
                        'academic_year_id' => $year->id
                    ],
                    array_merge($subjectData, [
                        'academic_year_id' => $year->id,
                        'is_active' => true,
                    ])
                );
            }
        }
        
        $this->command->info('تم إنشاء ' . count($years) . ' سنوات دراسية بنجاح');
    }
}

