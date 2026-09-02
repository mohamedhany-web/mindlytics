<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\CalendarEvent;
use App\Models\Exam;
use App\Models\Lecture;
use App\Models\LectureAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $events = $this->getStudentEvents($user);

        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        $todayEvents = $events
            ->filter(fn ($event) => $this->eventTouchesRange($event, $todayStart, $todayEnd))
            ->values();

        $upcomingEvents = $events
            ->filter(fn ($event) => $event->start_date && $event->start_date->gte($now))
            ->take(12)
            ->values();

        $stats = [
            'total' => $events->count(),
            'exams' => $events->where('type', 'exam')->count(),
            'lectures' => $events->where('type', 'lecture')->count(),
            'assignments' => $events->where('type', 'assignment')->count(),
            'upcoming' => $events->filter(fn ($event) => $event->start_date && $event->start_date->gte($now))->count(),
            'today' => $todayEvents->count(),
        ];

        return view('student.calendar.index', compact('events', 'stats', 'todayEvents', 'upcomingEvents'));
    }

    public function getEvents(Request $request)
    {
        $user = Auth::user();
        $start = $request->get('start');
        $end = $request->get('end');
        $type = $request->get('type');

        $events = $this->getStudentEvents($user, $start, $end);

        if ($type && $type !== 'all') {
            if ($type === 'other') {
                $events = $events
                    ->filter(fn ($event) => ! in_array($event->type, ['exam', 'lecture', 'assignment'], true))
                    ->values();
            } else {
                $events = $events->where('type', $type)->values();
            }
        }

        $calendarEvents = $events->map(function ($event) {
            return [
                'id' => $event->calendar_id ?? $event->id,
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                'allDay' => (bool) ($event->is_all_day ?? false),
                'backgroundColor' => $event->color ?? $this->getEventColor($event->type),
                'borderColor' => $event->color ?? $this->getEventColor($event->type),
                'textColor' => '#09244b',
                'url' => $event->url ?? null,
                'extendedProps' => [
                    'type' => $event->type,
                    'typeLabel' => $this->typeLabel($event->type),
                    'priority' => $event->priority ?? 'medium',
                    'location' => $event->location ?? null,
                    'description' => $event->description ?? null,
                    'rawTitle' => $event->raw_title ?? $event->title,
                    'courseTitle' => $event->course_title ?? null,
                ],
            ];
        });

        return response()->json($calendarEvents);
    }

    private function getStudentEvents($user, $startDate = null, $endDate = null)
    {
        $events = collect();
        $rangeStart = $startDate ? Carbon::parse($startDate) : now()->subMonths(1);
        $rangeEnd = $endDate ? Carbon::parse($endDate) : now()->addMonths(3);

        $lectures = Lecture::whereHas('course', function ($q) use ($user) {
            $q->whereHas('enrollments', function ($q2) use ($user) {
                $q2->where('user_id', $user->id)->where('status', 'active');
            });
        })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', $rangeStart)
            ->where('scheduled_at', '<=', $rangeEnd)
            ->with(['course', 'instructor'])
            ->get();

        foreach ($lectures as $lecture) {
            $endTime = $lecture->scheduled_at->copy()->addMinutes($lecture->duration_minutes ?? 60);
            $courseTitle = $lecture->course->title ?? '';
            $events->push((object) [
                'calendar_id' => 'lecture_'.$lecture->id,
                'id' => $lecture->id,
                'title' => trim($lecture->title.($courseTitle ? ' · '.$courseTitle : '')),
                'raw_title' => $lecture->title,
                'course_title' => $courseTitle ?: null,
                'description' => $lecture->description,
                'start_date' => $lecture->scheduled_at,
                'end_date' => $endTime,
                'is_all_day' => false,
                'type' => 'lecture',
                'color' => $this->getEventColor('lecture'),
                'priority' => 'medium',
                'url' => route('my-courses.learn', $lecture->course_id),
                'location' => $lecture->teams_meeting_link,
            ]);
        }

        $exams = Exam::whereHas('course', function ($q) use ($user) {
            $q->whereHas('enrollments', function ($q2) use ($user) {
                $q2->where('user_id', $user->id)->where('status', 'active');
            });
        })
            ->where('is_active', true)
            ->where('is_published', true)
            ->where(function ($q) use ($rangeStart, $rangeEnd) {
                $q->where(function ($q2) use ($rangeStart, $rangeEnd) {
                    $q2->where('start_time', '>=', $rangeStart)
                        ->where('start_time', '<=', $rangeEnd);
                })->orWhere(function ($q2) use ($rangeStart, $rangeEnd) {
                    $q2->where('start_date', '>=', $rangeStart)
                        ->where('start_date', '<=', $rangeEnd);
                });
            })
            ->with(['course'])
            ->get();

        foreach ($exams as $exam) {
            $examStart = $exam->start_time ?? ($exam->start_date ? Carbon::parse($exam->start_date) : now());
            $examEnd = $exam->end_time ?? ($exam->end_date ? Carbon::parse($exam->end_date) : $examStart->copy()->addMinutes($exam->duration_minutes ?? 60));
            $courseTitle = $exam->course->title ?? '';

            $events->push((object) [
                'calendar_id' => 'exam_'.$exam->id,
                'id' => $exam->id,
                'title' => trim(__('student.cal_prefix_exam').': '.$exam->title.($courseTitle ? ' · '.$courseTitle : '')),
                'raw_title' => $exam->title,
                'course_title' => $courseTitle ?: null,
                'description' => $exam->description ?? $exam->instructions,
                'start_date' => $examStart,
                'end_date' => $examEnd,
                'is_all_day' => false,
                'type' => 'exam',
                'color' => $this->getEventColor('exam'),
                'priority' => 'high',
                'url' => route('student.exams.show', $exam->id),
            ]);
        }

        $assignments = Assignment::whereHas('course', function ($q) use ($user) {
            $q->whereHas('enrollments', function ($q2) use ($user) {
                $q2->where('user_id', $user->id)->where('status', 'active');
            });
        })
            ->where('status', 'published')
            ->where('due_date', '>=', $rangeStart)
            ->where('due_date', '<=', $rangeEnd)
            ->with(['course'])
            ->get();

        foreach ($assignments as $assignment) {
            $courseTitle = $assignment->course->title ?? '';
            $courseId = $assignment->advanced_course_id ?? $assignment->course_id;

            $events->push((object) [
                'calendar_id' => 'assignment_'.$assignment->id,
                'id' => $assignment->id,
                'title' => trim(__('student.cal_prefix_assignment').': '.$assignment->title.($courseTitle ? ' · '.$courseTitle : '')),
                'raw_title' => $assignment->title,
                'course_title' => $courseTitle ?: null,
                'description' => $assignment->description ?? $assignment->instructions,
                'start_date' => $assignment->due_date,
                'end_date' => $assignment->due_date,
                'is_all_day' => true,
                'type' => 'assignment',
                'color' => $this->getEventColor('assignment'),
                'priority' => 'high',
                'url' => route('student.assignments.show', $assignment),
            ]);
        }

        $lectureAssignments = LectureAssignment::whereHas('lecture.course', function ($q) use ($user) {
            $q->whereHas('enrollments', function ($q2) use ($user) {
                $q2->where('user_id', $user->id)->where('status', 'active');
            });
        })
            ->where('status', 'published')
            ->where('due_date', '>=', $rangeStart)
            ->where('due_date', '<=', $rangeEnd)
            ->with(['lecture.course'])
            ->get();

        foreach ($lectureAssignments as $assignment) {
            $courseTitle = $assignment->lecture->course->title ?? '';

            $events->push((object) [
                'calendar_id' => 'lecture_assignment_'.$assignment->id,
                'id' => $assignment->id,
                'title' => trim(__('student.cal_prefix_lecture_assignment').': '.$assignment->title.($courseTitle ? ' · '.$courseTitle : '')),
                'raw_title' => $assignment->title,
                'course_title' => $courseTitle ?: null,
                'description' => $assignment->description ?? $assignment->instructions,
                'start_date' => $assignment->due_date,
                'end_date' => $assignment->due_date,
                'is_all_day' => true,
                'type' => 'assignment',
                'color' => $this->getEventColor('assignment'),
                'priority' => 'high',
                'url' => route('my-courses.learn', $assignment->lecture->course_id),
            ]);
        }

        $calendarEvents = CalendarEvent::getStudentEvents($user->id, $rangeStart, $rangeEnd);

        foreach ($calendarEvents as $event) {
            $events->push((object) [
                'calendar_id' => 'calendar_'.$event->id,
                'id' => $event->id,
                'title' => $event->title,
                'raw_title' => $event->title,
                'course_title' => null,
                'description' => $event->description,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date ?? $event->start_date,
                'is_all_day' => $event->is_all_day,
                'type' => $event->type ?: 'other',
                'color' => $event->color ?: $this->getEventColor($event->type ?: 'other'),
                'priority' => $event->priority,
                'location' => $event->location,
                'url' => null,
            ]);
        }

        return $events->sortBy('start_date')->values();
    }

    private function eventTouchesRange($event, Carbon $start, Carbon $end): bool
    {
        if (! $event->start_date) {
            return false;
        }

        $eventStart = $event->start_date->copy();
        $eventEnd = ($event->end_date ?? $event->start_date)->copy();

        return $eventStart->lte($end) && $eventEnd->gte($start);
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            'exam' => __('student.legend_exams'),
            'lecture' => __('student.legend_lectures'),
            'assignment' => __('student.legend_assignments'),
            default => __('student.other_events'),
        };
    }

    private function getEventColor($type)
    {
        return match ($type) {
            'exam' => '#f9e4d7',
            'lecture' => '#d7e8f9',
            'assignment' => '#f9f0d7',
            'meeting' => '#dcdef2',
            'deadline' => '#f9e4d7',
            'review' => '#d7eef5',
            'personal' => '#dcdef2',
            default => '#aed9ea',
        };
    }
}
