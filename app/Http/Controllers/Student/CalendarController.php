<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Lecture;
use App\Models\Exam;
use App\Models\Assignment;
use App\Models\LectureAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * عرض التقويم
     */
    public function index()
    {
        $user = Auth::user();
        
        // جلب جميع الأحداث للطالب
        $events = $this->getStudentEvents($user);
        
        // إحصائيات
        $stats = [
            'total' => $events->count(),
            'exams' => $events->where('type', 'exam')->count(),
            'lectures' => $events->where('type', 'lecture')->count(),
            'assignments' => $events->where('type', 'assignment')->count(),
            'upcoming' => $events->where('start_date', '>=', now())->count(),
        ];

        return view('student.calendar.index', compact('events', 'stats'));
    }

    /**
     * API endpoint للحصول على الأحداث بصيغة JSON (لـ FullCalendar)
     */
    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $start = $request->get('start');
        $end = $request->get('end');

        $events = $this->getStudentEvents($user, $start, $end);
        
        // تحويل الأحداث إلى صيغة FullCalendar
        $calendarEvents = $events->map(function($event) {
            return [
                'id' => $event->id ?? $event->calendar_id,
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                'allDay' => $event->is_all_day ?? false,
                'color' => $event->color ?? $this->getEventColor($event->type),
                'type' => $event->type,
                'url' => $event->url ?? null,
                'description' => $event->description ?? null,
                'extendedProps' => [
                    'priority' => $event->priority ?? 'medium',
                    'location' => $event->location ?? null,
                ]
            ];
        });

        return response()->json($calendarEvents);
    }

    /**
     * جلب جميع الأحداث للطالب
     */
    private function getStudentEvents($user, $startDate = null, $endDate = null)
    {
        $events = collect();

        // 1. المحاضرات (Lectures)
        $lectures = Lecture::whereHas('course', function($q) use ($user) {
            $q->whereHas('enrollments', function($q2) use ($user) {
                $q2->where('user_id', $user->id)
                   ->where('status', 'active');
            });
        })
        ->where('status', 'scheduled')
        ->where('scheduled_at', '>=', $startDate ?? now()->subMonths(1))
        ->where('scheduled_at', '<=', $endDate ?? now()->addMonths(3))
        ->with(['course', 'instructor'])
        ->get();

        foreach ($lectures as $lecture) {
            $endTime = $lecture->scheduled_at->copy()->addMinutes($lecture->duration_minutes ?? 60);
            $events->push((object)[
                'calendar_id' => 'lecture_' . $lecture->id,
                'id' => $lecture->id,
                'title' => $lecture->title . ' - ' . ($lecture->course->title ?? ''),
                'description' => $lecture->description,
                'start_date' => $lecture->scheduled_at,
                'end_date' => $endTime,
                'is_all_day' => false,
                'type' => 'lecture',
                'color' => '#3B82F6',
                'priority' => 'medium',
                'url' => route('my-courses.show', $lecture->course_id) . '#lectures',
                'location' => $lecture->teams_meeting_link,
            ]);
        }

        // 2. الامتحانات (Exams)
        $exams = Exam::whereHas('course', function($q) use ($user) {
            $q->whereHas('enrollments', function($q2) use ($user) {
                $q2->where('user_id', $user->id)
                   ->where('status', 'active');
            });
        })
        ->where('is_active', true)
        ->where('is_published', true)
        ->where(function($q) use ($startDate, $endDate) {
            $q->where(function($q2) use ($startDate, $endDate) {
                if ($startDate) {
                    $q2->where('start_time', '>=', $startDate);
                }
                if ($endDate) {
                    $q2->where('start_time', '<=', $endDate);
                }
            })
            ->orWhere(function($q2) use ($startDate, $endDate) {
                if ($startDate) {
                    $q2->where('start_date', '>=', $startDate);
                }
                if ($endDate) {
                    $q2->where('start_date', '<=', $endDate);
                }
            });
        })
        ->with(['course'])
        ->get();

        foreach ($exams as $exam) {
            $startDate = $exam->start_time ?? ($exam->start_date ? Carbon::parse($exam->start_date) : now());
            $endDate = $exam->end_time ?? ($exam->end_date ? Carbon::parse($exam->end_date) : $startDate->copy()->addMinutes($exam->duration_minutes ?? 60));
            
            $events->push((object)[
                'calendar_id' => 'exam_' . $exam->id,
                'id' => $exam->id,
                'title' => 'امتحان: ' . $exam->title . ' - ' . ($exam->course->title ?? ''),
                'description' => $exam->description ?? $exam->instructions,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_all_day' => false,
                'type' => 'exam',
                'color' => '#EF4444',
                'priority' => 'high',
                'url' => route('student.exams.show', $exam->id),
            ]);
        }

        // 3. الواجبات (Assignments)
        $assignments = Assignment::whereHas('course', function($q) use ($user) {
            $q->whereHas('enrollments', function($q2) use ($user) {
                $q2->where('user_id', $user->id)
                   ->where('status', 'active');
            });
        })
        ->where('status', 'published')
        ->where('due_date', '>=', $startDate ?? now()->subMonths(1))
        ->where('due_date', '<=', $endDate ?? now()->addMonths(3))
        ->with(['course'])
        ->get();

        foreach ($assignments as $assignment) {
            $events->push((object)[
                'calendar_id' => 'assignment_' . $assignment->id,
                'id' => $assignment->id,
                'title' => 'واجب: ' . $assignment->title . ' - ' . ($assignment->course->title ?? ''),
                'description' => $assignment->description ?? $assignment->instructions,
                'start_date' => $assignment->due_date,
                'end_date' => $assignment->due_date,
                'is_all_day' => true,
                'type' => 'assignment',
                'color' => '#F59E0B',
                'priority' => 'high',
                'url' => route('my-courses.show', $assignment->advanced_course_id ?? $assignment->course_id) . '#assignments',
            ]);
        }

        // 4. واجبات المحاضرات (Lecture Assignments)
        $lectureAssignments = LectureAssignment::whereHas('lecture.course', function($q) use ($user) {
            $q->whereHas('enrollments', function($q2) use ($user) {
                $q2->where('user_id', $user->id)
                   ->where('status', 'active');
            });
        })
        ->where('status', 'published')
        ->where('due_date', '>=', $startDate ?? now()->subMonths(1))
        ->where('due_date', '<=', $endDate ?? now()->addMonths(3))
        ->with(['lecture.course'])
        ->get();

        foreach ($lectureAssignments as $assignment) {
            $events->push((object)[
                'calendar_id' => 'lecture_assignment_' . $assignment->id,
                'id' => $assignment->id,
                'title' => 'واجب محاضرة: ' . $assignment->title,
                'description' => $assignment->description ?? $assignment->instructions,
                'start_date' => $assignment->due_date,
                'end_date' => $assignment->due_date,
                'is_all_day' => true,
                'type' => 'assignment',
                'color' => '#F59E0B',
                'priority' => 'high',
                'url' => route('my-courses.show', $assignment->lecture->course_id) . '#lectures',
            ]);
        }

        // 5. أحداث التقويم المخصصة (Calendar Events)
        $calendarEvents = CalendarEvent::getStudentEvents(
            $user->id,
            $startDate ?? now()->subMonths(1),
            $endDate ?? now()->addMonths(3)
        );

        foreach ($calendarEvents as $event) {
            $events->push((object)[
                'calendar_id' => 'calendar_' . $event->id,
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date ?? $event->start_date,
                'is_all_day' => $event->is_all_day,
                'type' => $event->type,
                'color' => $event->color,
                'priority' => $event->priority,
                'location' => $event->location,
            ]);
        }

        // ترتيب الأحداث حسب التاريخ
        return $events->sortBy('start_date')->values();
    }

    /**
     * الحصول على لون الحدث حسب النوع
     */
    private function getEventColor($type)
    {
        return match($type) {
            'exam' => '#EF4444',
            'lecture' => '#3B82F6',
            'assignment' => '#F59E0B',
            'meeting' => '#8B5CF6',
            'deadline' => '#DC2626',
            'review' => '#10B981',
            'personal' => '#6366F1',
            default => '#6B7280',
        };
    }
}