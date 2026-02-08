<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;

class SubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود الجداول
        if (!\Illuminate\Support\Facades\Schema::hasTable('academic_years') || !\Illuminate\Support\Facades\Schema::hasTable('academic_subjects')) {
            $this->command->warn('⚠️  جداول academic_years/academic_subjects غير موجودة. يرجى تشغيل migrations أولاً.');
            return;
        }

        // إذا كانت السنوات الأكاديمية موجودة بالفعل من AcademicYearSeeder، استخدمها
        // وإلا أنشئ سنة افتراضية
        $year = AcademicYear::first();
        
        if (!$year) {
            // إنشاء سنة دراسية إذا لم تكن موجودة
            $year = AcademicYear::create([
                'name' => 'الثالث الثانوي',
                'code' => 'G12',
                'description' => 'الصف الثالث الثانوي - القسم العلمي',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => true,
            ]);
        }

        $subjects = [
            [
                'name' => 'الرياضيات',
                'code' => 'MATH101',
                'description' => 'مادة الرياضيات الأساسية تشمل الجبر والهندسة والتفاضل والتكامل',
                'icon' => 'fas fa-calculator',
                'color' => '#3B82F6',
                'order' => 1,
            ],
            [
                'name' => 'الجبر',
                'code' => 'ALG101',
                'description' => 'مادة الجبر المتقدم تشمل المعادلات والمتباينات والمصفوفات',
                'icon' => 'fas fa-square-root-alt',
                'color' => '#10B981',
                'order' => 2,
            ],
            [
                'name' => 'الهندسة',
                'code' => 'GEOM101',
                'description' => 'مادة الهندسة التحليلية والفراغية والمثلثات',
                'icon' => 'fas fa-chart-line',
                'color' => '#F59E0B',
                'order' => 3,
            ],
            [
                'name' => 'التفاضل والتكامل',
                'code' => 'CALC101',
                'description' => 'مادة حساب التفاضل والتكامل والتطبيقات الرياضية',
                'icon' => 'fas fa-infinity',
                'color' => '#EF4444',
                'order' => 4,
            ],
            [
                'name' => 'الإحصاء والاحتمالات',
                'code' => 'STAT101',
                'description' => 'مادة الإحصاء الوصفي والاستنتاجي ونظرية الاحتمالات',
                'icon' => 'fas fa-chart-bar',
                'color' => '#8B5CF6',
                'order' => 5,
            ],
        ];

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

        $this->command->info('تم إنشاء ' . count($subjects) . ' مواد دراسية بنجاح');
    }
}
