<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Helpers\VideoHelper;
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
use App\Support\LectureRecordingResolver;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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

        $enrollment->load(['group.locationModel']);
        if ($enrollment->group) {
            $enrollment->group->loadCount('sessions');
        }

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

        $groupFilter = function ($q) use ($enrollment) {
            if ($enrollment->group_id) {
                $q->where(function ($x) use ($enrollment) {
                    $x->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
                });
            }
        };

        $hubStats = [
            'resources' => $offlineCourse->resources()->active()->where($groupFilter)->count(),
            'lectures' => $offlineCourse->offlineLectures()->active()->where($groupFilter)->count(),
            'activities' => $offlineCourse->activities()->where('status', 'published')->where($groupFilter)->count(),
            'exams' => \App\Models\AdvancedExam::query()
                ->where('offline_course_id', $offlineCourse->id)
                ->where('is_active', true)
                ->where('is_published', true)
                ->count(),
            'pending_activities' => $pendingActivities->count(),
        ];

        $nextSession = $enrollment->group
            ? $enrollment->group->sessions()->where('session_date', '>=', now()->startOfDay())->ordered()->first()
            : null;

        return view('student.offline-courses.show', compact(
            'offlineCourse',
            'enrollment',
            'pendingActivities',
            'completedActivities',
            'channel',
            'studentRouteGroup',
            'hubStats',
            'nextSession'
        ));
    }

    /**
     * منهج الكورس مع التوصيف الكامل ونبذة المدرب
     */
    public function curriculum(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        $offlineCourse->load(['instructor', 'locationModel']);

        $curriculumRoots = $this->curriculumSectionsForStudent($offlineCourse, $enrollment);
        $curriculumStats = $this->curriculumStatsForStudent($curriculumRoots);

        return view('student.offline-courses.curriculum', compact(
            'offlineCourse',
            'enrollment',
            'curriculumRoots',
            'curriculumStats',
            'channel',
            'studentRouteGroup'
        ));
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Models\OfflineCourseSection> $roots
     * @return array{sections: int, items: int}
     */
    private function curriculumStatsForStudent($roots): array
    {
        $sections = 0;
        $items = 0;
        $walk = function ($collection) use (&$sections, &$items, &$walk) {
            foreach ($collection as $sec) {
                $sections++;
                $items += $sec->items->count();
                if ($sec->relationLoaded('children') && $sec->children->isNotEmpty()) {
                    $walk($sec->children);
                }
            }
        };
        $walk($roots);

        return ['sections' => $sections, 'items' => $items];
    }

    /**
     * تقويم الجلسات والمواعيد (أنشطة بتاريخ تسليم، اختبارات بتاريخ بدء)
     */
    public function schedule(OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        $enrollment->load(['group.sessions' => fn ($q) => $q->ordered(), 'group.locationModel']);

        $offlineCourse->load('locationModel');

        $sessions = $enrollment->group
            ? $enrollment->group->sessions()->ordered()->get()
            : collect();

        $activitiesQuery = $offlineCourse->activities()
            ->where('status', 'published')
            ->where('is_active', true)
            ->orderBy('due_date')
            ->orderBy('id');

        if ($enrollment->group_id) {
            $activitiesQuery->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }

        $activities = $activitiesQuery->get();
        $activitiesNoDue = $activities->filter(fn (OfflineActivity $a) => $a->due_date === null)->values();

        $exams = AdvancedExam::query()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('is_active', true)
            ->where('is_published', true)
            ->whereNotNull('start_date')
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        $timelineRows = collect();

        foreach ($sessions as $session) {
            $sort = $session->session_date->format('Y-m-d').' ';
            try {
                $sort .= $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('H:i:s') : '00:00:00';
            } catch (\Throwable $e) {
                $sort .= '00:00:00';
            }
            $timelineRows->push([
                'type' => 'session',
                'sort' => $sort,
                'date' => $session->session_date,
                'session' => $session,
            ]);
        }

        foreach ($activities->filter(fn (OfflineActivity $a) => $a->due_date !== null) as $activity) {
            $timelineRows->push([
                'type' => 'activity',
                'sort' => $activity->due_date->format('Y-m-d').' 23:59:00',
                'date' => $activity->due_date,
                'activity' => $activity,
            ]);
        }

        foreach ($exams as $exam) {
            $timelineRows->push([
                'type' => 'exam',
                'sort' => $exam->start_date->format('Y-m-d').' 12:00:00',
                'date' => $exam->start_date,
                'exam' => $exam,
            ]);
        }

        $timelineRows = $timelineRows->sortBy('sort')->values();

        $timelineByMonth = $timelineRows->groupBy(function (array $row) {
            return $row['date']->translatedFormat('F Y');
        });

        return view('student.offline-courses.schedule', compact(
            'offlineCourse',
            'enrollment',
            'sessions',
            'activitiesNoDue',
            'timelineByMonth',
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
                $q->where('is_active', true)->orderBy('order')->orderBy('id')
                    ->with(['item' => function (MorphTo $morph) {
                        $morph->morphWith([
                            OfflineLecture::class => ['groupSession.group'],
                        ]);
                    }]);
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
    public function resources(Request $request, OfflineCourse $offlineCourse)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();
        $studentRouteGroup = $this->studentRouteGroup();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        $search = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min(25, max(5, $perPage));

        $resourceVisibility = function ($q) use ($enrollment) {
            if ($enrollment->group_id) {
                $q->where(function ($qq) use ($enrollment) {
                    $qq->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
                });
            } else {
                $q->whereNull('group_id');
            }
        };

        // موارد عامة (غير مرتبطة بمحاضرة)
        $generalResourcesQuery = $offlineCourse->resources()
            ->active()
            ->ordered()
            ->whereDoesntHave('lectures')
            ->where($resourceVisibility);
        if ($search !== '') {
            $generalResourcesQuery->where(function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('file_name', 'like', $like);
            });
        }
        $generalResources = $generalResourcesQuery->get();

        // محاضرات + مواردها (مقسمة حسب الجلسات/اليوم)
        $lecturesQuery = $offlineCourse->offlineLectures()
            ->active()
            ->ordered()
            ->with(['groupSession.group'])
            ->with(['resources' => function ($q) use ($resourceVisibility, $search) {
                $q->active()->ordered()->where($resourceVisibility);
                if ($search !== '') {
                    $q->where(function ($qq) use ($search) {
                        $like = '%' . $search . '%';
                        $qq->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like)
                            ->orWhere('file_name', 'like', $like);
                    });
                }
            }]);
        if ($enrollment->group_id) {
            $lecturesQuery->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }
        if ($search !== '') {
            $lecturesQuery->where(function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('resources', function ($rq) use ($like) {
                        $rq->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like)
                            ->orWhere('file_name', 'like', $like);
                    });
            });
        }

        $lectures = $lecturesQuery->paginate($perPage)->withQueryString();

        return view('student.offline-courses.resources', compact(
            'offlineCourse',
            'enrollment',
            'generalResources',
            'lectures',
            'channel',
            'studentRouteGroup',
            'search',
            'perPage'
        ));
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

        $query = $offlineCourse->offlineLectures()->active()->ordered()->with(['groupSession.group']);
        if ($enrollment->group_id) {
            $query->where(function ($q) use ($enrollment) {
                $q->whereNull('group_id')->orWhere('group_id', $enrollment->group_id);
            });
        }
        $lectures = $query->get();

        return view('student.offline-courses.lectures', compact('offlineCourse', 'enrollment', 'lectures', 'channel', 'studentRouteGroup'));
    }

    /**
     * مشاهدة تسجيل محاضرة داخل المنصة (بدل فتح رابط التسجيل خارجياً).
     */
    public function watchLectureRecording(OfflineCourse $offlineCourse, OfflineLecture $lecture)
    {
        $user = Auth::user();
        $channel = $this->studentLearningChannel();

        $enrollment = $user->offlineEnrollments()
            ->where('offline_course_id', $offlineCourse->id)
            ->where('enrollment_channel', $channel)
            ->where('status', 'active')
            ->firstOrFail();

        if ($lecture->offline_course_id !== $offlineCourse->id) {
            abort(404);
        }
        if (! $lecture->is_active) {
            abort(404);
        }
        if ($lecture->group_id && (int) $lecture->group_id !== (int) $enrollment->group_id) {
            abort(403, 'هذا التسجيل غير متاح لمجموعتك');
        }

        $raw = $lecture->playbackUrl() ?: ($lecture->recording_url ? trim((string) $lecture->recording_url) : '');
        if ($raw === '') {
            abort(404, 'لا يوجد تسجيل لهذه المحاضرة');
        }

        // فيديو مخزّن على R2/المنصة → HTML5 داخل المنصة
        if ($lecture->hasStoredRecording()) {
            return response()
                ->view('video.protected-embed', [
                    'type' => 'html5',
                    'src' => $raw,
                    'mime' => $lecture->recording_mime ?: 'video/mp4',
                    'title' => $lecture->title ?: 'التسجيل',
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        $source = VideoHelper::getVideoSource($raw);
        $embed = VideoHelper::getEmbedUrl($raw) ?: $raw;

        if ($source === 'bunny') {
            $resolved = LectureRecordingResolver::resolve($raw, 'bunny');
            $embed = $resolved['recording_url'] ?: $embed;

            return response()
                ->view('video.protected-embed', [
                    'type' => 'iframe',
                    'src' => $embed,
                    'title' => $lecture->title ?: 'التسجيل',
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        if ($source === 'direct') {
            return response()
                ->view('video.protected-embed', [
                    'type' => 'html5',
                    'src' => $embed,
                    'mime' => 'video/mp4',
                    'title' => $lecture->title ?: 'التسجيل',
                ])
                ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
                ->header('Pragma', 'no-cache');
        }

        // YouTube/Vimeo/Drive/Other embeds: عرض داخل صفحة داخلية بدل فتح خارج المنصة
        return response()
            ->view('video.protected-embed', [
                'type' => 'iframe',
                'src' => $embed,
                'title' => $lecture->title ?: 'التسجيل',
            ])
            ->header('Cache-Control', 'private, no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
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
            ->with('grader')
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
                if ($file instanceof UploadedFile) {
                    $newAttachments[] = $this->storeOfflineActivitySubmissionFile($file, $activity->id);
                }
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

    /**
     * @return array{path: string, name: string, disk: string}
     */
    private function storeOfflineActivitySubmissionFile(UploadedFile $file, int $activityId): array
    {
        $directory = 'offline-activity-submissions/'.$activityId;
        $preferred = offline_activity_submissions_disk();
        try {
            $path = $file->store($directory, $preferred);

            return [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'disk' => $preferred,
            ];
        } catch (\Throwable $e) {
            $path = $file->store($directory, 'public');

            return [
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'disk' => 'public',
            ];
        }
    }
}
