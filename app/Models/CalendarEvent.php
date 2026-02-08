<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'is_all_day',
        'type',
        'priority',
        'color',
        'location',
        'notes',
        'created_by',
        'visibility',
        'academic_year_id',
        'academic_subject_id',
        'advanced_course_id',
        'has_reminder',
        'reminder_minutes',
        'email_reminder',
        'status',
        'is_recurring',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_end_date',
        'has_grade',
        'max_grade',
        'grading_criteria',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_all_day' => 'boolean',
        'has_reminder' => 'boolean',
        'email_reminder' => 'boolean',
        'is_recurring' => 'boolean',
        'has_grade' => 'boolean',
        'recurrence_end_date' => 'date',
        'max_grade' => 'decimal:2',
    ];

    /**
     * علاقة مع المستخدم المنشئ
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة مع السنة الأكاديمية
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * علاقة مع المادة
     */
    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    /**
     * علاقة مع الكورس
     */
    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    /**
     * الحصول على الأحداث للموظف
     */
    public static function getEmployeeEvents($userId, $startDate = null, $endDate = null)
    {
        $query = self::where(function($q) use ($userId) {
            // الأحداث العامة
            $q->where('visibility', 'public')
              // أو الأحداث الخاصة بالمنشئ
              ->orWhere('created_by', $userId)
              // أو الأحداث المرئية للموظفين
              ->orWhere('visibility', 'employees');
        });

        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where(function($q) use ($endDate) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '<=', $endDate);
            });
        }

        return $query->where('status', 'active')
                    ->orderBy('start_date')
                    ->get();
    }

    /**
     * الحصول على الأحداث للطالب
     */
    public static function getStudentEvents($userId, $startDate = null, $endDate = null)
    {
        // جلب IDs الكورسات المسجل فيها الطالب
        $enrolledCourseIds = \App\Models\StudentCourseEnrollment::where('user_id', $userId)
            ->where('status', 'active')
            ->pluck('advanced_course_id')
            ->filter()
            ->unique()
            ->toArray();

        // جلب IDs السنوات والمواد الدراسية للكورسات المسجل فيها
        $yearIds = [];
        $subjectIds = [];
        if (!empty($enrolledCourseIds)) {
            $courses = \App\Models\AdvancedCourse::whereIn('id', $enrolledCourseIds)
                ->select('academic_year_id', 'academic_subject_id')
                ->get();
            
            $yearIds = $courses->pluck('academic_year_id')->filter()->unique()->toArray();
            $subjectIds = $courses->pluck('academic_subject_id')->filter()->unique()->toArray();
        }

        $query = self::where(function($q) use ($userId, $enrolledCourseIds, $yearIds, $subjectIds) {
            // أحداث خاصة بالطالب
            $q->where(function($q1) use ($userId) {
                $q1->where('created_by', $userId)
                   ->where('visibility', 'private');
            })
            // أحداث عامة
            ->orWhere('visibility', 'public')
            // أحداث الكورسات المسجل فيها الطالب
            ->orWhere(function($q2) use ($enrolledCourseIds) {
                if (!empty($enrolledCourseIds)) {
                    $q2->whereIn('advanced_course_id', $enrolledCourseIds);
                } else {
                    $q2->where('id', '<', 0); // query فارغ
                }
            })
            // أحداث المواد الدراسية للكورسات المسجل فيها
            ->orWhere(function($q2) use ($subjectIds) {
                if (!empty($subjectIds)) {
                    $q2->whereIn('academic_subject_id', $subjectIds);
                } else {
                    $q2->where('id', '<', 0);
                }
            })
            // أحداث السنوات الأكاديمية للكورسات المسجل فيها
            ->orWhere(function($q2) use ($yearIds) {
                if (!empty($yearIds)) {
                    $q2->whereIn('academic_year_id', $yearIds);
                } else {
                    $q2->where('id', '<', 0);
                }
            });
        })
        ->where('status', 'scheduled');

        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('start_date', '<=', $endDate);
        }

        return $query->orderBy('start_date', 'asc')->get();
    }
}
