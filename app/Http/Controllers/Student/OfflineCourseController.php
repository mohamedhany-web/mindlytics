<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use App\Models\OfflineCourseEnrollment;
use App\Models\OfflineActivity;
use App\Models\OfflineActivitySubmission;
use App\Models\OfflineCourseResource;
use App\Models\OfflineLecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OfflineCourseController extends Controller
{
    /**
     * عرض قائمة الكورسات الأوفلاين للطالب
     */
    public function index()
    {
        $user = Auth::user();
        
        $enrollments = $user->offlineEnrollments()
            ->with(['course.instructor', 'course.locationModel', 'group'])
            ->where('status', 'active')
            ->latest('enrolled_at')
            ->paginate(12);

        $stats = [
            'total_offline' => $user->offlineEnrollments()->where('status', 'active')->count(),
            'total_activities' => OfflineActivity::whereHas('course.enrollments', function($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'active');
            })->count(),
        ];

        return view('student.offline-courses.index', compact('enrollments', 'stats'));
    }

    /**
     * عرض تفاصيل كورس أوفلاين
     */
    public function show(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('status', 'active')
            ->firstOrFail();

        $enrollment->load('group');

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

        // الأنشطة المعلقة
        $pendingActivities = $offlineCourse->activities()
            ->where('status', 'published')
            ->whereDoesntHave('submissions', function($q) use ($user) {
                $q->where('student_id', $user->id)->where('status', 'submitted');
            })
            ->get();

        // الأنشطة المكتملة
        $completedActivities = $offlineCourse->activities()
            ->whereHas('submissions', function($q) use ($user) {
                $q->where('student_id', $user->id)->where('status', 'graded');
            })
            ->get();

        return view('student.offline-courses.show', compact(
            'offlineCourse',
            'enrollment',
            'pendingActivities',
            'completedActivities'
        ));
    }

    /**
     * موارد الكورس الأوفلاين (للطالب)
     */
    public function resources(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('status', 'active')
            ->firstOrFail();

        $query = $offlineCourse->resources()->active()->ordered();
        if ($enrollment->group_id) {
            $query->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }
        $resources = $query->get();

        return view('student.offline-courses.resources', compact('offlineCourse', 'enrollment', 'resources'));
    }

    /**
     * محاضرات الكورس الأوفلاين (للطالب)
     */
    public function lectures(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('status', 'active')
            ->firstOrFail();

        $query = $offlineCourse->offlineLectures()->active()->ordered();
        if ($enrollment->group_id) {
            $query->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }
        $lectures = $query->get();

        return view('student.offline-courses.lectures', compact('offlineCourse', 'enrollment', 'lectures'));
    }

    /**
     * عرض نشاط (واجب/اختبار) وتقديمه
     */
    public function activityShow(OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $user = Auth::user();
        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
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

        return view('student.offline-courses.activity-show', compact('offlineCourse', 'enrollment', 'activity', 'submission'));
    }

    /**
     * تسليم النشاط
     */
    public function activitySubmit(Request $request, OfflineCourse $offlineCourse, OfflineActivity $activity)
    {
        $user = Auth::user();
        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
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
            ->route('student.offline-courses.activities.show', [$offlineCourse, $activity])
            ->with('success', 'تم تسليم النشاط بنجاح');
    }
}
