<?php

namespace App\Console\Commands;

use App\Models\AdvancedCourse;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyCourseDeleteCommand extends Command
{
    protected $signature = 'course:verify-delete {id? : معرّف الكورس (اختياري - يُستخدم أول كورس إن لم يُحدد)}';
    protected $description = 'حذف كورس والتحقق من عدم بقاء سجلات مرتبطة (للتأكد من عمل الحذف بشكل صحيح)';

    public function handle(): int
    {
        $id = $this->argument('id');
        $course = $id
            ? AdvancedCourse::find($id)
            : AdvancedCourse::first();

        if (! $course) {
            $this->error('لا يوجد كورس بهذا المعرّف أو لا توجد كورسات.');
            return self::FAILURE;
        }

        $courseId = $course->id;
        $title = $course->title;

        $this->info("جاري حذف الكورس: [{$courseId}] {$title}");

        try {
            AdvancedCourse::deleteRelatedRecords($courseId);
            $course->delete();
            $this->info('تم تنفيذ الحذف بنجاح.');
        } catch (\Throwable $e) {
            $this->error('فشل الحذف: ' . $e->getMessage());
            report($e);
            return self::FAILURE;
        }

        $this->info('التحقق من عدم بقاء سجلات مرتبطة بالكورس...');

        $tables = [
            'course_lessons' => 'advanced_course_id',
            'lectures' => 'course_id',
            'student_course_enrollments' => 'advanced_course_id',
            'exams' => 'advanced_course_id',
            'assignments' => 'advanced_course_id',
            'course_sections' => 'advanced_course_id',
            'learning_patterns' => 'advanced_course_id',
            'installment_plans' => 'advanced_course_id',
            'installment_agreements' => 'advanced_course_id',
            'package_course' => 'course_id',
            'course_reviews' => 'course_id',
            'academic_year_courses' => 'advanced_course_id',
            'calendar_events' => 'advanced_course_id',
            'attendance_statistics' => 'course_id',
        ];

        $failed = [];
        foreach ($tables as $table => $column) {
            if (! \Illuminate\Support\Facades\Schema::hasTable($table)) {
                continue;
            }
            $count = DB::table($table)->where($column, $courseId)->count();
            if ($count > 0) {
                $failed[] = "{$table}: {$count} سجل";
            }
        }

        $exists = DB::table('advanced_courses')->where('id', $courseId)->exists();
        if ($exists) {
            $failed[] = 'advanced_courses: السجل ما زال موجوداً';
        }

        if (! empty($failed)) {
            $this->error('فشل التحقق - توجد سجلات مرتبطة ما زالت موجودة:');
            foreach ($failed as $f) {
                $this->line('  - ' . $f);
            }
            return self::FAILURE;
        }

        $this->info('تم التحقق: لا توجد سجلات مرتبطة بالكورس. الحذف تم بشكل صحيح.');
        return self::SUCCESS;
    }
}
