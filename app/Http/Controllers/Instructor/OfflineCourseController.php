<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\OfflineAttendance;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\OfflineGroupSession;
use App\Models\OfflineActivity;
use App\Models\OfflineActivitySubmission;
use App\Models\AdvancedExam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class OfflineCourseController extends Controller
{
    public function onlineIndex(Request $request)
    {
        $request->merge(['channel' => 'online']);
        return $this->index($request);
    }

    public function onlineShow(Request $request, OfflineCourse $offlineCourse)
    {
        $request->merge(['channel' => 'online']);
        return $this->show($request, $offlineCourse);
    }

    public function index(Request $request)
    {
        $instructor = Auth::user();
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';

        $query = OfflineCourse::where('instructor_id', $instructor->id)
            ->with(['locationModel'])
            ->withCount('groups')
            ->withCount([
                'enrollments as enrollments_count' => function ($q) use ($channel) {
                    $q->where('enrollment_channel', $channel);
                },
            ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('start_date', 'desc')->paginate(12)->withQueryString();

        $stats = [
            'total' => OfflineCourse::where('instructor_id', $instructor->id)->count(),
            'active' => OfflineCourse::where('instructor_id', $instructor->id)->where('status', 'active')->count(),
            'draft' => OfflineCourse::where('instructor_id', $instructor->id)->where('status', 'draft')->count(),
            'completed' => OfflineCourse::where('instructor_id', $instructor->id)->where('status', 'completed')->count(),
            'total_students' => OfflineCourseEnrollment::whereHas('course', function ($q) use ($instructor) {
                $q->where('instructor_id', $instructor->id);
            })->where('enrollment_channel', $channel)->where('status', 'active')->count(),
        ];

        return view('instructor.offline-courses.index', compact('courses', 'stats', 'channel'));
    }

    public function show(Request $request, OfflineCourse $offlineCourse)
    {
        $instructor = Auth::user();
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        $offlineCourse->load([
            'locationModel',
            'instructor',
            'groups.sessions',
            'enrollments' => function ($q) use ($channel) {
                $q->where('enrollment_channel', $channel);
            },
            'enrollments.student',
        ]);

        $stats = [
            'total_students' => $offlineCourse->enrollments()->where('enrollment_channel', $channel)->count(),
            'active_students' => $offlineCourse->enrollments()->where('enrollment_channel', $channel)->where('status', 'active')->count(),
            'total_groups' => $offlineCourse->groups()->count(),
            'total_activities' => $offlineCourse->activities()->count(),
        ];

        return view('instructor.offline-courses.show', compact('offlineCourse', 'stats', 'channel'));
    }

    /**
     * تسجيل حضور/غياب مجموعة كاملة ليوم محدد (المصدر موحّد للأوفلاين/الأونلاين).
     */
    public function markAttendance(Request $request, OfflineCourse $offlineCourse)
    {
        $instructor = Auth::user();
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بإدارة هذا الكورس');
        }

        try {
            $validated = $request->validate([
                'group_id' => 'required|integer',
                'session_id' => 'required|integer|exists:offline_group_sessions,id',
                'date' => 'required|date',
                'records' => 'required|array|min:1',
                'records.*.student_id' => 'required|integer|exists:users,id',
                'records.*.status' => 'required|in:present,absent,late,excused',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Offline attendance validation failed', [
                'course_id' => $offlineCourse->id,
                'user_id' => $instructor->id,
                'errors' => $e->errors(),
            ]);
            throw $e;
        }

        try {
            $groupId = (int) $validated['group_id'];
            $sessionId = (int) $validated['session_id'];

            $exists = $offlineCourse->groups()->whereKey($groupId)->exists();
            if (! $exists) {
                abort(403, 'المجموعة لا تتبع هذا الكورس');
            }

            $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
            OfflineGroupSession::query()
                ->forOfflineCourse($offlineCourse, $channel)
                ->whereKey($sessionId)
                ->where('group_id', $groupId)
                ->firstOrFail();

            $date = date('Y-m-d', strtotime((string) $validated['date']));
            $records = $validated['records'];

            $requestedStudentIds = collect($records)->pluck('student_id')->map(fn ($v) => (int) $v)->unique()->values()->all();
            $allowedStudentIds = $offlineCourse->enrollments()
                ->whereIn('user_id', $requestedStudentIds)
                ->pluck('user_id')
                ->map(fn ($v) => (int) $v)
                ->all();
            $allowedStudentIds = array_flip($allowedStudentIds);

            DB::transaction(function () use ($offlineCourse, $groupId, $sessionId, $date, $records, $allowedStudentIds, $instructor) {
                foreach ($records as $r) {
                    $sid = (int) $r['student_id'];
                    if (! isset($allowedStudentIds[$sid])) {
                        continue;
                    }
                    OfflineAttendance::updateOrCreate(
                        [
                            'student_id' => $sid,
                            'offline_course_id' => $offlineCourse->id,
                            'offline_group_session_id' => $sessionId,
                        ],
                        [
                            'group_id' => $groupId,
                            'attendance_date' => $date,
                            'attendance_time' => now()->format('H:i:s'),
                            'status' => $r['status'],
                            'marked_by' => $instructor->id,
                        ]
                    );
                }
            });

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('Offline attendance save failed', [
                'course_id' => $offlineCourse->id,
                'user_id' => $instructor->id,
                'payload' => $request->all(),
                'exception' => $e,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'فشل حفظ الحضور: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function attendanceIndex(Request $request, OfflineCourse $offlineCourse)
    {
        $instructor = Auth::user();
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        $sessions = OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->with('group')
            ->orderBy('session_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('instructor.offline-courses.attendance.index', compact('offlineCourse', 'sessions', 'channel'));
    }

    public function attendanceSession(Request $request, OfflineCourse $offlineCourse, OfflineGroupSession $session)
    {
        $instructor = Auth::user();
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        OfflineGroupSession::query()
            ->forOfflineCourse($offlineCourse, $channel)
            ->whereKey($session->id)
            ->firstOrFail();

        $session->load('group');
        $groupId = (int) ($session->group_id ?? 0);
        $students = $offlineCourse->enrollments()
            ->where('enrollment_channel', $channel)
            ->where('group_id', $groupId)
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->values();

        $date = optional($session->session_date)->format('Y-m-d') ?? now()->toDateString();
        $attendanceRecords = $students->isEmpty()
            ? collect()
            : OfflineAttendance::query()
                ->where('offline_course_id', $offlineCourse->id)
                ->where('offline_group_session_id', $session->id)
                ->whereIn('student_id', $students->pluck('id')->all())
                ->get()
                ->keyBy('student_id');

        return view('instructor.offline-courses.attendance.session', compact(
            'offlineCourse',
            'session',
            'students',
            'attendanceRecords',
            'channel',
            'date'
        ));
    }

    public function studentReportsIndex(Request $request, OfflineCourse $offlineCourse)
    {
        $instructor = Auth::user();
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        $search = trim((string) $request->query('q', ''));

        $enrollments = $offlineCourse->enrollments()
            ->where('enrollment_channel', $channel)
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->whereHas('student', function ($uq) use ($like) {
                    $uq->where('name', 'like', $like)->orWhere('email', 'like', $like);
                });
            })
            ->with(['student', 'group'])
            ->orderBy('id', 'desc')
            ->get();

        $studentIds = $enrollments->pluck('user_id')->filter()->unique()->values()->all();

        // الحضور: إجمالي + غياب + حضور + تأخير + استئذان
        $attendanceAgg = empty($studentIds)
            ? collect()
            : OfflineAttendance::query()
                ->selectRaw('student_id, COUNT(*) as total, SUM(status="present") as present_cnt, SUM(status="absent") as absent_cnt, SUM(status="late") as late_cnt, SUM(status="excused") as excused_cnt')
                ->where('offline_course_id', $offlineCourse->id)
                ->whereIn('student_id', $studentIds)
                ->groupBy('student_id')
                ->get()
                ->keyBy('student_id');

        // الواجبات/الأنشطة الأوفلاين: submissions + avg score
        $activityAgg = empty($studentIds)
            ? collect()
            : OfflineActivitySubmission::query()
                ->selectRaw('offline_activity_submissions.student_id, COUNT(*) as submissions_cnt, AVG(offline_activity_submissions.score) as avg_score')
                ->join('offline_activities', 'offline_activities.id', '=', 'offline_activity_submissions.activity_id')
                ->where('offline_activities.offline_course_id', $offlineCourse->id)
                ->whereIn('offline_activity_submissions.student_id', $studentIds)
                ->groupBy('offline_activity_submissions.student_id')
                ->get()
                ->keyBy('student_id');

        // امتحانات الأكاديمية الخاصة بالكورس الأوفلاين (AdvancedExam) ومحاولات الطلاب
        $examAgg = collect();
        if (!empty($studentIds) && Schema::hasTable('advanced_exams')) {
            $examAgg = ExamAttempt::query()
                ->selectRaw('exam_attempts.user_id as student_id, COUNT(*) as attempts_cnt, AVG(exam_attempts.score) as avg_score')
                ->join('advanced_exams', 'advanced_exams.id', '=', 'exam_attempts.exam_id')
                ->where('advanced_exams.offline_course_id', $offlineCourse->id)
                ->whereIn('exam_attempts.user_id', $studentIds)
                ->groupBy('exam_attempts.user_id')
                ->get()
                ->keyBy('student_id');
        }

        return view('instructor.offline-courses.student-reports.index', compact(
            'offlineCourse',
            'channel',
            'enrollments',
            'attendanceAgg',
            'activityAgg',
            'examAgg',
            'search'
        ));
    }

    public function studentReportsShow(Request $request, OfflineCourse $offlineCourse, User $student)
    {
        $instructor = Auth::user();
        $channel = $request->query('channel') === 'online' ? 'online' : 'offline';
        if ($offlineCourse->instructor_id !== $instructor->id) {
            abort(403, 'غير مسموح لك بعرض هذا الكورس');
        }

        $enrollment = $offlineCourse->enrollments()
            ->where('enrollment_channel', $channel)
            ->where('user_id', $student->id)
            ->with('group')
            ->firstOrFail();

        $attendance = OfflineAttendance::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('student_id', $student->id)
            ->orderBy('attendance_date', 'desc')
            ->limit(200)
            ->get();

        $activitySubmissions = OfflineActivitySubmission::query()
            ->with('activity')
            ->where('student_id', $student->id)
            ->whereHas('activity', fn ($q) => $q->where('offline_course_id', $offlineCourse->id))
            ->orderBy('submitted_at', 'desc')
            ->limit(200)
            ->get();

        $examAttempts = ExamAttempt::query()
            ->with('exam')
            ->where('user_id', $student->id)
            ->when(Schema::hasTable('advanced_exams'), function ($q) use ($offlineCourse) {
                $q->whereHas('exam', fn ($eq) => $eq->where('offline_course_id', $offlineCourse->id));
            })
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get();

        return view('instructor.offline-courses.student-reports.show', compact(
            'offlineCourse',
            'channel',
            'student',
            'enrollment',
            'attendance',
            'activitySubmissions',
            'examAttempts'
        ));
    }

    /**
     * تقويم المدرب - جلسات الأوفلاين
     */
    public function calendar()
    {
        return view('instructor.calendar');
    }

    /**
     * API - جلب جلسات المدرب للتقويم
     */
    public function calendarEvents(Request $request)
    {
        $instructor = Auth::user();
        $start = $request->get('start');
        $end = $request->get('end');

        $query = OfflineGroupSession::where('instructor_id', $instructor->id)
            ->with(['group.course']);

        if ($start) {
            $query->where('session_date', '>=', $start);
        }
        if ($end) {
            $query->where('session_date', '<=', $end);
        }

        $sessions = $query->ordered()->get();

        $events = $sessions->map(function ($session) {
            $course = $session->group?->course;
            $colors = [
                'scheduled' => '#3B82F6',
                'completed' => '#10B981',
                'cancelled' => '#EF4444',
            ];

            return [
                'id' => $session->id,
                'title' => ($session->title ?: ($course?->title ?? 'جلسة')) . ' - ' . ($session->group?->name ?? ''),
                'start' => $session->session_date->format('Y-m-d') . 'T' . $session->start_time,
                'end' => $session->session_date->format('Y-m-d') . 'T' . $session->end_time,
                'color' => $colors[$session->status] ?? '#6B7280',
                'extendedProps' => [
                    'course' => $course?->title,
                    'group' => $session->group?->name,
                    'location' => $session->location ?? $session->group?->location,
                    'status' => $session->status,
                    'duration' => $session->duration_minutes . ' دقيقة',
                    'notes' => $session->notes,
                ],
            ];
        });

        return response()->json($events);
    }
}
