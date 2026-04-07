<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\OfflineActivity;
use App\Models\OfflineActivitySubmission;
use App\Models\OfflineCourseResource;
use App\Models\OfflineCurriculumItem;
use App\Models\OfflineCurriculumNote;
use App\Models\OfflineLecture;
use App\Models\OfflineCourseBooking;
use App\Models\AdvancedExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OfflineCourseController extends Controller
{
    private function studentLearningChannel(): string
    {
        return request()->routeIs('student.online-courses.*') ? 'online' : 'offline';
    }

    private function studentRouteGroup(): string
    {
        return $this->studentLearningChannel() === 'online'
            ? 'student.online-courses'
            : 'student.offline-courses';
    }

    /**
     * عرض قائمة الكورسات (أوفلاين أو أونلاين حسب المسار)
     */
    public function index()
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollmentsQuery = $user->offlineEnrollments()
            ->with(['course.instructor', 'course.locationModel', 'group'])
            ->where('enrollment_channel', $channel)
            ->where('status', 'active');

        $enrollments = $enrollmentsQuery
            ->latest('enrolled_at')
            ->paginate(12)
            ->withQueryString();

        $activeEnrolledCourseIds = $user->offlineEnrollments()
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->pluck('offline_course_id');

        $bookingsQuery = $user->offlineCourseBookings()
            ->with(['course.instructor', 'course.locationModel', 'requestedGroup', 'assignedGroup'])
            ->where('booking_channel', $channel)
            ->whereIn('status', [OfflineCourseBooking::STATUS_PENDING, OfflineCourseBooking::STATUS_APPROVED])
            ->whereNotIn('offline_course_id', $activeEnrolledCourseIds);

        $bookings = $bookingsQuery
            ->latest('created_at')
            ->get();

        $visibleCoursesCount = $enrollments->total() + $bookings->count();

        $stats = [
            'total_offline' => $visibleCoursesCount,
            'total_activities' => OfflineActivity::whereHas('course.enrollments', function ($q) use ($user, $channel) {
                $q->where('user_id', $user->id)
                    ->where('status', 'active')
                    ->where('enrollment_channel', $channel);
            })->count(),
        ];

        return view('student.offline-courses.index', compact('enrollments', 'bookings', 'stats', 'channel', 'studentRouteGroup'));
    }

    /**
     * عرض تفاصيل كورس أوفلاين
     */
    public function show(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        $enrollment->load(['group.sessions' => fn($q) => $q->ordered(), 'group.locationModel']);

        $offlineCourse->load([
            'instructor',
            'locationModel',
            'groups',
            'activities' => function($q) use ($enrollment) {
                if ($enrollment->group_id) {
                    $q->where(function($query) use ($enrollment) {
                        $query->whereNull('group_id')
                              ->orWhere('group_id', $enrollment->group_id);
                    });
                }
            },
            'activities.submissions' => function($q) use ($user) {
                $q->where('student_id', $user->id);
            }
        ]);

        $pendingActivities = $offlineCourse->activities()
            ->where('status', 'published')
            ->whereDoesntHave('submissions', function($q) use ($user) {
                $q->where('student_id', $user->id)->where('status', 'submitted');
            })
            ->get();

        $completedActivities = $offlineCourse->activities()
            ->whereHas('submissions', function($q) use ($user) {
                $q->where('student_id', $user->id)->where('status', 'graded');
            })
            ->get();

        $upcomingSessions = $enrollment->group
            ? $enrollment->group->sessions()->upcoming()->ordered()->take(10)->get()
            : collect();

        $curriculumRoots = $this->curriculumSectionsForStudent($offlineCourse, $enrollment);

        return view('student.offline-courses.show', compact(
            'offlineCourse',
            'enrollment',
            'pendingActivities',
            'completedActivities',
            'upcomingSessions',
            'curriculumRoots',
            'channel',
            'studentRouteGroup'
        ));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\OfflineCourseSection>
     */
    private function curriculumSectionsForStudent(OfflineCourse $course, OfflineCourseEnrollment $enrollment)
    {
        $all = $course->offlineCourseSections()
            ->where('is_active', true)
            ->with(['items' => function ($q) {
                $q->where('is_active', true)->orderBy('order')->orderBy('id')->with('item');
            }])
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        foreach ($all as $section) {
            $section->setRelation(
                'items',
                $section->items->filter(fn (OfflineCurriculumItem $ci) => $this->offlineCurriculumItemVisible($ci, $enrollment))->values()
            );
        }

        foreach ($all as $section) {
            $section->setRelation('children', $all->where('parent_id', $section->id)->values());
        }

        return $all->whereNull('parent_id')->values();
    }

    private function offlineCurriculumItemVisible(OfflineCurriculumItem $ci, OfflineCourseEnrollment $enrollment): bool
    {
        $m = $ci->item;
        if (! $m) {
            return false;
        }

        $gid = $enrollment->group_id ? (int) $enrollment->group_id : null;

        if ($m instanceof OfflineLecture) {
            if (! $m->is_active) {
                return false;
            }
            if ($gid && $m->group_id && (int) $m->group_id !== $gid) {
                return false;
            }

            return true;
        }

        if ($m instanceof OfflineCourseResource) {
            if (! $m->is_active) {
                return false;
            }
            if ($gid && $m->group_id && (int) $m->group_id !== $gid) {
                return false;
            }

            return true;
        }

        if ($m instanceof OfflineActivity) {
            if ($m->status !== 'published' || ! $m->is_active) {
                return false;
            }
            if ($gid && $m->group_id && (int) $m->group_id !== $gid) {
                return false;
            }

            return true;
        }

        if ($m instanceof AdvancedExam) {
            if (! $m->is_active || ! $m->is_published) {
                return false;
            }
            if ((int) ($m->offline_course_id ?? 0) !== (int) $enrollment->offline_course_id) {
                return false;
            }

            return true;
        }

        if ($m instanceof OfflineCurriculumNote) {
            return true;
        }

        return false;
    }

    /**
     * موارد الكورس الأوفلاين (للطالب)
     */
    public function resources(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        $query = $offlineCourse->resources()->active()->ordered();
        if ($enrollment->group_id) {
            $query->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }
        $resources = $query->get();

        return view('student.offline-courses.resources', compact('offlineCourse', 'enrollment', 'resources', 'channel', 'studentRouteGroup'));
    }

    /**
     * محاضرات الكورس الأوفلاين (للطالب)
     */
    public function lectures(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        $query = $offlineCourse->offlineLectures()->active()->ordered();
        if ($enrollment->group_id) {
            $query->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }
        $lectures = $query->get();

        return view('student.offline-courses.lectures', compact('offlineCourse', 'enrollment', 'lectures', 'channel', 'studentRouteGroup'));
    }

    /**
     * عرض نشاط (واجب/اختبار) وتقديمه
     */
    public function activityShow(OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        if ($activity->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }
        if ($activity->group_id && $activity->group_id != $enrollment->group_id) {
            abort(403, 'هذا النشاط غير متاح لمجموعتك');
        }

        $submission = OfflineActivitySubmission::where('activity_id', $activity->id)
            ->where('student_id', $user->id)
            ->first();

        return view('student.offline-courses.activity-show', compact('offlineCourse', 'enrollment', 'activity', 'submission', 'channel', 'studentRouteGroup'));
    }

    /**
     * تسليم النشاط
     */
    public function activitySubmit(Request $request, OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        if ($activity->offline_course_id !== $offlineCourse->id || $activity->status !== 'published') {
            abort(404);
        }
        if ($activity->group_id && $activity->group_id != $enrollment->group_id) {
            abort(403, 'هذا النشاط غير متاح لمجموعتك');
        }

        $request->validate([
            'submission_content' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:20480',
        ]);

        $submission = OfflineActivitySubmission::firstOrNew(
            ['activity_id' => $activity->id, 'student_id' => $user->id]
        );

        $newAttachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('offline-activity-submissions/' . $activity->id, 'public');
                $newAttachments[] = ['path' => $path, 'name' => $file->getClientOriginalName()];
            }
        }
        $submission->submission_content = $request->input('submission_content');
        $submission->attachments = array_merge($submission->attachments ?? [], $newAttachments);
        $submission->submitted_at = now();
        $submission->status = 'submitted';
        $submission->save();

        return redirect()
            ->route($studentRouteGroup . '.activities.show', [$offlineCourse, $activity])
            ->with('success', 'تم تسليم النشاط بنجاح');
    }
}
