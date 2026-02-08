<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Lecture;
use App\Models\Exam;
use App\Models\Assignment;
use App\Models\LectureAssignment;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalendarNotificationService
{
    /**
     * إنشاء إشعارات تلقائية للمحاضرات
     */
    public function createLectureNotifications(Lecture $lecture)
    {
        try {
            $course = $lecture->course;
            if (!$course) {
                return;
            }

            // جلب جميع الطلاب المسجلين في الكورس
            $students = $course->enrollments()
                ->where('status', 'active')
                ->with('student')
                ->get()
                ->pluck('student')
                ->filter();

            foreach ($students as $student) {
                // إشعار فوري عند إنشاء المحاضرة
                Notification::create([
                    'user_id' => $student->id,
                    'sender_id' => $lecture->instructor_id,
                    'title' => 'محاضرة جديدة: ' . $lecture->title,
                    'message' => 'تم جدولة محاضرة جديدة في كورس "' . $course->title . '" بتاريخ ' . $lecture->scheduled_at->format('d/m/Y h:i A'),
                    'type' => 'reminder',
                    'priority' => 'normal',
                    'audience' => 'student',
                    'action_url' => route('my-courses.show', $course->id) . '#lectures',
                    'action_text' => 'عرض المحاضرة',
                    'data' => [
                        'lecture_id' => $lecture->id,
                        'course_id' => $course->id,
                        'scheduled_at' => $lecture->scheduled_at->toIso8601String(),
                    ],
                ]);

                // تذكير قبل المحاضرة بـ 24 ساعة
                if ($lecture->scheduled_at->isFuture() && $lecture->scheduled_at->diffInHours(now()) >= 24) {
                    $reminderTime = $lecture->scheduled_at->copy()->subHours(24);
                    
                    // يمكن استخدام Queue أو Cron job لإرسال التذكير في الوقت المحدد
                    // هنا ننشئ إشعاراً مع expires_at
                    Notification::create([
                        'user_id' => $student->id,
                        'sender_id' => $lecture->instructor_id,
                        'title' => 'تذكير: محاضرة غداً - ' . $lecture->title,
                        'message' => 'تذكير: لديك محاضرة غداً في كورس "' . $course->title . '" في الساعة ' . $lecture->scheduled_at->format('h:i A'),
                        'type' => 'reminder',
                        'priority' => 'high',
                        'audience' => 'student',
                        'action_url' => route('my-courses.show', $course->id) . '#lectures',
                        'action_text' => 'عرض المحاضرة',
                        'expires_at' => $lecture->scheduled_at,
                        'data' => [
                            'lecture_id' => $lecture->id,
                            'course_id' => $course->id,
                            'scheduled_at' => $lecture->scheduled_at->toIso8601String(),
                            'reminder_type' => '24h_before',
                        ],
                    ]);
                }

                // تذكير قبل المحاضرة بساعة
                if ($lecture->scheduled_at->isFuture() && $lecture->scheduled_at->diffInHours(now()) >= 1) {
                    Notification::create([
                        'user_id' => $student->id,
                        'sender_id' => $lecture->instructor_id,
                        'title' => 'تذكير: محاضرة قريباً - ' . $lecture->title,
                        'message' => 'تذكير: لديك محاضرة خلال ساعة في كورس "' . $course->title . '"',
                        'type' => 'reminder',
                        'priority' => 'urgent',
                        'audience' => 'student',
                        'action_url' => $lecture->teams_meeting_link ?? route('my-courses.show', $course->id) . '#lectures',
                        'action_text' => 'انضم للمحاضرة',
                        'expires_at' => $lecture->scheduled_at,
                        'data' => [
                            'lecture_id' => $lecture->id,
                            'course_id' => $course->id,
                            'scheduled_at' => $lecture->scheduled_at->toIso8601String(),
                            'reminder_type' => '1h_before',
                            'teams_link' => $lecture->teams_meeting_link,
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error creating lecture notifications: ' . $e->getMessage());
        }
    }

    /**
     * إنشاء إشعارات تلقائية للامتحانات
     */
    public function createExamNotifications(Exam $exam)
    {
        try {
            $course = $exam->course;
            if (!$course) {
                return;
            }

            $students = $course->enrollments()
                ->where('status', 'active')
                ->with('student')
                ->get()
                ->pluck('student')
                ->filter();

            $examStart = $exam->start_time ?? ($exam->start_date ? Carbon::parse($exam->start_date) : null);
            if (!$examStart || !$examStart->isFuture()) {
                return;
            }

            foreach ($students as $student) {
                // إشعار فوري عند إنشاء الامتحان
                Notification::create([
                    'user_id' => $student->id,
                    'sender_id' => $exam->created_by,
                    'title' => 'امتحان جديد: ' . $exam->title,
                    'message' => 'تم جدولة امتحان جديد في كورس "' . $course->title . '" بتاريخ ' . $examStart->format('d/m/Y h:i A'),
                    'type' => 'exam',
                    'priority' => 'high',
                    'audience' => 'student',
                    'action_url' => route('student.exams.show', $exam->id),
                    'action_text' => 'عرض الامتحان',
                    'data' => [
                        'exam_id' => $exam->id,
                        'course_id' => $course->id,
                        'start_time' => $examStart->toIso8601String(),
                    ],
                ]);

                // تذكير قبل الامتحان بـ 3 أيام
                if ($examStart->diffInDays(now()) >= 3) {
                    Notification::create([
                        'user_id' => $student->id,
                        'sender_id' => $exam->created_by,
                        'title' => 'تذكير: امتحان بعد 3 أيام - ' . $exam->title,
                        'message' => 'تذكير: لديك امتحان في كورس "' . $course->title . '" بعد 3 أيام في ' . $examStart->format('d/m/Y h:i A'),
                        'type' => 'reminder',
                        'priority' => 'high',
                        'audience' => 'student',
                        'action_url' => route('student.exams.show', $exam->id),
                        'action_text' => 'مراجعة الامتحان',
                        'expires_at' => $examStart,
                        'data' => [
                            'exam_id' => $exam->id,
                            'course_id' => $course->id,
                            'start_time' => $examStart->toIso8601String(),
                            'reminder_type' => '3d_before',
                        ],
                    ]);
                }

                // تذكير قبل الامتحان بيوم
                if ($examStart->diffInDays(now()) >= 1) {
                    Notification::create([
                        'user_id' => $student->id,
                        'sender_id' => $exam->created_by,
                        'title' => 'تذكير: امتحان غداً - ' . $exam->title,
                        'message' => 'تذكير: لديك امتحان غداً في كورس "' . $course->title . '" في الساعة ' . $examStart->format('h:i A'),
                        'type' => 'reminder',
                        'priority' => 'urgent',
                        'audience' => 'student',
                        'action_url' => route('student.exams.show', $exam->id),
                        'action_text' => 'مراجعة الامتحان',
                        'expires_at' => $examStart,
                        'data' => [
                            'exam_id' => $exam->id,
                            'course_id' => $course->id,
                            'start_time' => $examStart->toIso8601String(),
                            'reminder_type' => '1d_before',
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error creating exam notifications: ' . $e->getMessage());
        }
    }

    /**
     * إنشاء إشعارات تلقائية للواجبات
     */
    public function createAssignmentNotifications($assignment)
    {
        try {
            $course = null;
            $students = collect();

            if ($assignment instanceof Assignment) {
                $course = $assignment->course;
                if ($course) {
                    $students = $course->enrollments()
                        ->where('status', 'active')
                        ->with('student')
                        ->get()
                        ->pluck('student')
                        ->filter();
                }
            } elseif ($assignment instanceof LectureAssignment) {
                $lecture = $assignment->lecture;
                if ($lecture) {
                    $course = $lecture->course;
                    if ($course) {
                        $students = $course->enrollments()
                            ->where('status', 'active')
                            ->with('student')
                            ->get()
                            ->pluck('student')
                            ->filter();
                    }
                }
            }

            if (!$course || $students->isEmpty()) {
                return;
            }

            $dueDate = $assignment->due_date;
            if (!$dueDate || !$dueDate->isFuture()) {
                return;
            }

            foreach ($students as $student) {
                // إشعار فوري عند إنشاء الواجب
                Notification::create([
                    'user_id' => $student->id,
                    'sender_id' => $assignment->teacher_id ?? ($assignment->lecture->instructor_id ?? null),
                    'title' => 'واجب جديد: ' . $assignment->title,
                    'message' => 'تم إضافة واجب جديد في كورس "' . $course->title . '" - موعد التسليم: ' . $dueDate->format('d/m/Y'),
                    'type' => 'assignment',
                    'priority' => 'high',
                    'audience' => 'student',
                    'action_url' => route('my-courses.show', $course->id) . '#assignments',
                    'action_text' => 'عرض الواجب',
                    'data' => [
                        'assignment_id' => $assignment->id,
                        'course_id' => $course->id,
                        'due_date' => $dueDate->toIso8601String(),
                    ],
                ]);

                // تذكير قبل موعد التسليم بـ 3 أيام
                if ($dueDate->diffInDays(now()) >= 3) {
                    Notification::create([
                        'user_id' => $student->id,
                        'sender_id' => $assignment->teacher_id ?? ($assignment->lecture->instructor_id ?? null),
                        'title' => 'تذكير: واجب - ' . $assignment->title,
                        'message' => 'تذكير: موعد تسليم واجب "' . $assignment->title . '" بعد 3 أيام في ' . $dueDate->format('d/m/Y'),
                        'type' => 'reminder',
                        'priority' => 'high',
                        'audience' => 'student',
                        'action_url' => route('my-courses.show', $course->id) . '#assignments',
                        'action_text' => 'عرض الواجب',
                        'expires_at' => $dueDate,
                        'data' => [
                            'assignment_id' => $assignment->id,
                            'course_id' => $course->id,
                            'due_date' => $dueDate->toIso8601String(),
                            'reminder_type' => '3d_before',
                        ],
                    ]);
                }

                // تذكير قبل موعد التسليم بيوم
                if ($dueDate->diffInDays(now()) >= 1) {
                    Notification::create([
                        'user_id' => $student->id,
                        'sender_id' => $assignment->teacher_id ?? ($assignment->lecture->instructor_id ?? null),
                        'title' => 'تذكير عاجل: واجب - ' . $assignment->title,
                        'message' => 'تذكير عاجل: موعد تسليم واجب "' . $assignment->title . '" غداً في ' . $dueDate->format('d/m/Y'),
                        'type' => 'reminder',
                        'priority' => 'urgent',
                        'audience' => 'student',
                        'action_url' => route('my-courses.show', $course->id) . '#assignments',
                        'action_text' => 'تسليم الواجب',
                        'expires_at' => $dueDate,
                        'data' => [
                            'assignment_id' => $assignment->id,
                            'course_id' => $course->id,
                            'due_date' => $dueDate->toIso8601String(),
                            'reminder_type' => '1d_before',
                        ],
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error creating assignment notifications: ' . $e->getMessage());
        }
    }

    /**
     * معالجة التذكيرات اليومية (يتم استدعاؤها من Cron Job)
     */
    public function processDailyReminders()
    {
        try {
            $now = now();
            
            // تذكيرات المحاضرات (قبل 24 ساعة)
            $lectures = Lecture::where('status', 'scheduled')
                ->whereBetween('scheduled_at', [
                    $now->copy()->addHours(24)->subMinutes(30),
                    $now->copy()->addHours(24)->addMinutes(30)
                ])
                ->with(['course.enrollments.student'])
                ->get();

            foreach ($lectures as $lecture) {
                $this->createLectureNotifications($lecture);
            }

            // تذكيرات الامتحانات (قبل 3 أيام و 1 يوم)
            $exams = Exam::where('is_active', true)
                ->where('is_published', true)
                ->where(function($q) use ($now) {
                    $q->whereBetween('start_time', [
                        $now->copy()->addDays(3)->startOfDay(),
                        $now->copy()->addDays(3)->endOfDay()
                    ])
                    ->orWhereBetween('start_time', [
                        $now->copy()->addDay()->startOfDay(),
                        $now->copy()->addDay()->endOfDay()
                    ]);
                })
                ->with(['course.enrollments.student'])
                ->get();

            foreach ($exams as $exam) {
                $this->createExamNotifications($exam);
            }

            // تذكيرات الواجبات (قبل 3 أيام و 1 يوم)
            $assignments = Assignment::where('status', 'published')
                ->whereBetween('due_date', [
                    $now->copy()->addDays(3)->startOfDay(),
                    $now->copy()->addDays(3)->endOfDay()
                ])
                ->orWhereBetween('due_date', [
                    $now->copy()->addDay()->startOfDay(),
                    $now->copy()->addDay()->endOfDay()
                ])
                ->with(['course.enrollments.student'])
                ->get();

            foreach ($assignments as $assignment) {
                $this->createAssignmentNotifications($assignment);
            }

            // تذكيرات واجبات المحاضرات
            $lectureAssignments = LectureAssignment::where('status', 'published')
                ->whereBetween('due_date', [
                    $now->copy()->addDays(3)->startOfDay(),
                    $now->copy()->addDays(3)->endOfDay()
                ])
                ->orWhereBetween('due_date', [
                    $now->copy()->addDay()->startOfDay(),
                    $now->copy()->addDay()->endOfDay()
                ])
                ->with(['lecture.course.enrollments.student'])
                ->get();

            foreach ($lectureAssignments as $assignment) {
                $this->createAssignmentNotifications($assignment);
            }

        } catch (\Exception $e) {
            Log::error('Error processing daily reminders: ' . $e->getMessage());
        }
    }
}
