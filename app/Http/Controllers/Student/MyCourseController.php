<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LectureMaterial;
use App\Models\LectureWatchProgress;
use App\Models\LectureVideoQuestion;
use App\Models\LectureVideoQuestionAnswer;
use App\Models\CourseLesson;
use App\Models\Lecture;
use App\Services\CourseProgressService;
use App\Services\ScholarshipCurriculumVisibilityService;
use App\Support\LectureRecordingResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyCourseController extends Controller
{
    public function __construct(
        private ScholarshipCurriculumVisibilityService $scholarshipVisibility,
        private CourseProgressService $courseProgress,
    ) {
    }

    /**
     * عرض الكورسات المفعلة للطالب
     */
    public function index()
    {
        $user = Auth::user();
        
        // الكورسات المفعلة للطالب
        $activeCourses = $user->activeCourses()
            ->with(['academicYear', 'academicSubject', 'teacher', 'lessons'])
            ->paginate(12);

        // إضافة نقاط الطالب من أسئلة الفيديو لكل كورس
        $activeCourses->getCollection()->transform(function ($course) use ($user) {
            $course->student_points = LectureVideoQuestionAnswer::totalScoreForUserInCourse($user->id, $course->id);
            return $course;
        });

        // إحصائيات
        $stats = [
            'total_active' => $user->activeCourses()->count(),
            'total_completed' => $user->courseEnrollments()->where('status', 'completed')->count(),
            'total_hours' => $user->activeCourses()->sum('duration_hours'),
            'avg_progress' => $this->calculateAverageProgress($user),
        ];

        return view('student.my-courses.index', compact('activeCourses', 'stats'));
    }

    /**
     * عرض تفاصيل الكورس المفعل
     */
    public function show($courseId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()
            ->with([
                'academicYear', 
                'academicSubject', 
                'teacher', 
                'lessons.progress' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'sections.items.item' => function($query) use ($user) {
                    // جلب تقدم الدروس والأنماط في العناصر
                    if ($query->getModel() instanceof \App\Models\CourseLesson) {
                        $query->with(['progress' => function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        }]);
                    } elseif ($query->getModel() instanceof \App\Models\LearningPattern) {
                        $query->with(['attempts' => function($q) use ($user) {
                            $q->where('user_id', $user->id)->latest();
                        }]);
                    }
                }
            ])
            ->findOrFail($courseId);

        // جلب الأقسام مع العناصر مرتبة
        $sections = $course->activeSections()
            ->with([
                'visibleStudents:id', 'visibleGroups.members:id',
                'activeItems' => function ($query) {
                    $query->orderBy('order')->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']);
                },
            ])
            ->orderBy('order')
            ->get();

        $sections = $this->scholarshipVisibility->filterSectionsForStudent($sections, $user, $course);

        $this->courseProgress->loadCurriculumProgressForUser($sections, $user);
        list($progress, $totalLessons, $completedLessons) = $this->courseProgress->calculateFromSections($user, $course, $sections);
        $this->courseProgress->syncEnrollmentProgress((int) $user->id, (int) $course->id, (float) $progress);

        $coursePoints = LectureVideoQuestionAnswer::totalScoreForUserInCourse($user->id, $course->id);

        return view('student.my-courses.show', compact(
            'course', 
            'progress', 
            'totalLessons', 
            'completedLessons', 
            'coursePoints',
            'sections'
        ));
    }

    /**
     * صفحة عرض المحتوى (Focus Mode)
     */
    public function learn($courseId, Request $request)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()
            ->with([
                'academicYear', 
                'academicSubject', 
                'teacher', 
                'lessons.progress' => function($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'lectures' => function($query) {
                    $query->orderBy('scheduled_at', 'desc');
                },
                'lectures.lesson',
                'lectures.instructor',
                'sections.items.item' => function($query) use ($user) {
                    // جلب تقدم الدروس والأنماط في العناصر
                    if ($query->getModel() instanceof \App\Models\CourseLesson) {
                        $query->with(['progress' => function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        }]);
                    } elseif ($query->getModel() instanceof \App\Models\LearningPattern) {
                        $query->with(['attempts' => function($q) use ($user) {
                            $q->where('user_id', $user->id)->latest();
                        }]);
                    }
                }
            ])
            ->findOrFail($courseId);
        
        // جلب الدرس إذا تم تمرير lesson_id
        $lesson = null;
        if ($request->has('lesson')) {
            $lessonId = $request->input('lesson');
            $lesson = $course->lessons()->find($lessonId);
            
            // التحقق من أن الدرس نشط
            if ($lesson && !$lesson->is_active) {
                $lesson = null;
            }
            
            // التحقق من ترتيب الدروس (لا يمكن مشاهدة درس قبل إكمال السابق)
            if ($lesson) {
                $previousLessons = $course->lessons()
                    ->where('order', '<', $lesson->order)
                    ->where('is_active', true)
                    ->get();
                    
                foreach ($previousLessons as $prevLesson) {
                    $prevProgress = \App\Models\LessonProgress::where('user_id', $user->id)
                        ->where('course_lesson_id', $prevLesson->id)
                        ->first();
                        
                    if (!$prevProgress || !$prevProgress->is_completed) {
                        $lesson = null;
                        break;
                    }
                }
            }
        }

        // جلب كل الأقسام (مسطحة) مع العناصر لاحتساب التقدم وبناء الشجرة
        $allSections = $course->activeSections()
            ->with([
                'visibleStudents:id', 'visibleGroups.members:id',
                'activeItems' => function ($query) {
                    $query->orderBy('order')->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']);
                },
            ])
            ->orderBy('order')
            ->get();

        $allSections = $this->scholarshipVisibility->filterSectionsForStudent($allSections, $user, $course);

        $this->courseProgress->loadCurriculumProgressForUser($allSections, $user);
        list($progress, $totalLessons, $completedLessons) = $this->courseProgress->calculateFromSections($user, $course, $allSections);
        $this->courseProgress->syncEnrollmentProgress((int) $user->id, (int) $course->id, (float) $progress);

        // بناء شجرة الأقسام (جذور + أطفال) للعرض في السايدبار
        foreach ($allSections as $section) {
            $section->setRelation('children', $allSections->where('parent_id', $section->id)->values());
        }
        $sections = $allSections->whereNull('parent_id')->values();
        $this->computeSectionLockState($user, $allSections, (bool) ($course->admin_unlock_all_videos ?? false));
        $sectionDescriptions = $allSections->pluck('description', 'id')->map(fn ($d) => $d ?? '')->toArray();

        // بناء خريطة "العنصر التالي" لكل محاضرة (للفتح التلقائي عند بلوغ النسبة)
        $nextItemByLectureId = $this->buildNextItemMapByLectureId($sections);

        // الدرس التالي ضمن ترتيب دروس الكورس (فيديوهات الدروس — غير معروضة في السايدبار)
        $nextItemByLessonId = $this->buildNextLessonMap($course);

        // تجميع المحاضرات حسب الدرس (للتوافق مع الكود القديم)
        $lecturesByLesson = $course->lectures->groupBy('course_lesson_id');

        // جلب الاختبارات المرتبة حسب الموضع في السايدبار
        $sidebarExams = \App\Models\AdvancedExam::where('advanced_course_id', $course->id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('show_in_sidebar', true)
                  ->orWhereNull('show_in_sidebar'); // للتوافق مع البيانات القديمة
            })
            ->orderByRaw('CASE WHEN sidebar_position IS NULL THEN 999 ELSE sidebar_position END ASC')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('student.my-courses.learn', compact(
            'course',
            'progress',
            'totalLessons',
            'completedLessons',
            'lecturesByLesson',
            'sections',
            'sectionDescriptions',
            'sidebarExams',
            'lesson',
            'nextItemByLectureId',
            'nextItemByLessonId'
        ));
    }

    /**
     * خريطة الدرس التالي حسب ترتيب الدروس النشطة في الكورس.
     *
     * @return array<int, array{type: string, id: int}|null>
     */
    private function buildNextLessonMap($course): array
    {
        $lessons = $course->lessons()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        $map = [];
        $count = $lessons->count();
        for ($i = 0; $i < $count; $i++) {
            $current = $lessons[$i];
            $next = ($i + 1 < $count) ? $lessons[$i + 1] : null;
            $map[$current->id] = $next
                ? ['type' => 'lesson', 'id' => $next->id]
                : null;
        }

        return $map;
    }

    /**
     * بناء خريطة: لكل محاضرة، العنصر التالي في المنهج (للفتح التلقائي عند بلوغ نسبة المشاهدة).
     */
    private function buildNextItemMapByLectureId($sections): array
    {
        $flat = $this->flattenCurriculumItems($sections);
        $nextMap = [];
        for ($i = 0; $i < count($flat); $i++) {
            if (($flat[$i]['type'] ?? '') === 'lecture' && isset($flat[$i]['id'])) {
                $next = $flat[$i + 1] ?? null;
                $nextMap[$flat[$i]['id']] = $next;
            }
        }
        return $nextMap;
    }

    /**
     * تسطيح عناصر المنهج حسب ترتيب العرض (أقسام ثم أطفال).
     */
    private function flattenCurriculumItems($sections): array
    {
        $flat = [];
        foreach ($sections as $section) {
            $items = $section->activeItems->sortBy('order')->values();
            foreach ($items as $curriculumItem) {
                $item = $curriculumItem->item;
                if (!$item || $item instanceof \App\Models\CourseLesson) {
                    continue;
                }
                if ($item instanceof \App\Models\Lecture) {
                    $flat[] = ['type' => 'lecture', 'id' => $item->id];
                } elseif ($item instanceof \App\Models\Assignment) {
                    $flat[] = ['type' => 'assignment', 'id' => $item->id];
                } elseif ($item instanceof \App\Models\AdvancedExam || $item instanceof \App\Models\Exam) {
                    $flat[] = ['type' => 'exam', 'id' => $item->id];
                } elseif ($item instanceof \App\Models\LearningPattern) {
                    $flat[] = ['type' => 'pattern', 'id' => $item->id];
                }
            }
            if ($section->children && $section->children->isNotEmpty()) {
                $flat = array_merge($flat, $this->flattenCurriculumItems($section->children));
            }
        }
        return $flat;
    }

    /**
     * إرجاع بيانات محاضرة واحدة كـ JSON (لصفحة التعلم - جلب الفيديو عند الحاجة)
     */
    public function getLectureData($courseId, $lectureId)
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);
        $lecture = $course->lectures()->findOrFail($lectureId);

        if (! $this->scholarshipVisibility->contentVisibleToStudent($course, Lecture::class, (int) $lectureId, $user)) {
            abort(403, 'هذه المحاضرة غير متاحة لك');
        }

        $resolved = LectureRecordingResolver::resolve($lecture->recording_url, $lecture->video_platform);
        $recordingUrl = $resolved['recording_url'];
        $videoPlatform = $resolved['video_platform'];

        $materials = $lecture->materials()
            ->where('is_visible_to_student', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($m) use ($courseId, $lectureId) {
                return [
                    'id' => $m->id,
                    'title' => $m->title ?: $m->file_name,
                    'file_name' => $m->file_name,
                    'download_url' => route('my-courses.lectures.material.download', [$courseId, $lectureId, $m->id]),
                ];
            });

        $videoQuestions = $lecture->videoQuestions()->with('question')->orderBy('timestamp_seconds')->get()->filter(function ($vq) use ($user) {
            $showCount = $vq->show_count;
            if ($showCount === null || $showCount == 0) {
                return true;
            }
            $answered = LectureVideoQuestionAnswer::where('lecture_video_question_id', $vq->id)->where('user_id', $user->id)->count();
            return $answered < $showCount;
        })->map(function ($vq) {
            $payload = $vq->getPayloadForStudent();
            $showEveryTime = $vq->show_count === null || $vq->show_count == 0;
            return [
                'id' => $vq->id,
                'timestamp_seconds' => $vq->timestamp_seconds,
                'show_at_end' => (bool) $vq->show_at_end,
                'text' => $payload['text'] ?? '',
                'options' => $payload['options'] ?? [],
                'type' => $payload['type'] ?? 'multiple_choice',
                'points' => $vq->points,
                'on_wrong' => $vq->on_wrong,
                'rewind_seconds' => $vq->rewind_seconds,
                'show_every_time' => $showEveryTime,
            ];
        })->values()->all();

        $watchProgress = LectureWatchProgress::where('lecture_id', $lecture->id)
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'id' => $lecture->id,
            'title' => $lecture->title,
            'description' => $lecture->description,
            'scheduled_at' => $lecture->scheduled_at ? $lecture->scheduled_at->toIso8601String() : null,
            'scheduled_at_formatted' => $lecture->scheduled_at ? $lecture->scheduled_at->format('Y/m/d H:i') : null,
            'duration_minutes' => $lecture->duration_minutes ?? 60,
            'min_watch_percent_to_unlock_next' => $lecture->min_watch_percent_to_unlock_next,
            'recording_url' => $recordingUrl,
            'video_platform' => $videoPlatform,
            'teams_meeting_link' => $lecture->teams_meeting_link ?? null,
            'teams_registration_link' => $lecture->teams_registration_link ?? null,
            'notes' => $lecture->notes ?? null,
            'materials' => $materials,
            'video_questions' => $videoQuestions,
            'progress' => $watchProgress ? [
                'progress_percent' => (int) $watchProgress->progress_percent,
                'is_completed' => (bool) $watchProgress->is_completed,
                'watch_time_seconds' => (int) $watchProgress->watch_time_seconds,
                'video_duration_seconds' => (int) $watchProgress->video_duration_seconds,
            ] : null,
        ]);
    }

    /**
     * تحميل مادة محاضرة (للطالب - المواد الظاهرة فقط)
     */
    public function downloadLectureMaterial($courseId, $lectureId, $materialId)
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);
        $lecture = $course->lectures()->findOrFail($lectureId);
        $material = LectureMaterial::where('lecture_id', $lecture->id)
            ->where('id', $materialId)
            ->where('is_visible_to_student', true)
            ->firstOrFail();

        $path = Storage::disk('public')->path($material->file_path);
        if (!is_file($path)) {
            abort(404, 'الملف غير موجود');
        }

        return response()->download($path, $material->file_name);
    }

    /**
     * حالة الأقفال في السايدبار (AJAX) — لتحديث الواجهة بدون Refresh بعد إكمال فيديو.
     */
    public function curriculumLocks($courseId): JsonResponse
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);

        $allSections = $course->activeSections()
            ->with([
                'visibleStudents:id', 'visibleGroups.members:id',
                'activeItems' => function ($q) {
                    $q->orderBy('order')->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']);
                },
            ])
            ->orderBy('order')
            ->get();

        $allSections = $this->scholarshipVisibility->filterSectionsForStudent($allSections, $user, $course);

        $this->courseProgress->loadCurriculumProgressForUser($allSections, $user);

        // نفس قفل الأقسام المستخدم في learn()
        $this->computeSectionLockState($user, $allSections, (bool) ($course->admin_unlock_all_videos ?? false));

        // سياسة الأدمن: فتح كل فيديوهات الكورس بدون قيود التسلسل
        $unlockAll = (bool) ($course->admin_unlock_all_videos ?? false);

        $itemLocks = [];
        foreach ($allSections as $section) {
            $isSectionLocked = $unlockAll ? false : (bool) ($section->is_locked ?? false);
            $items = $section->activeItems->sortBy('order')->values();

            for ($i = 0; $i < $items->count(); $i++) {
                $ci = $items[$i];
                $item = $ci->item;
                if (! $item) continue;
                if ($item instanceof CourseLesson) continue; // الدروس ليست في السايدبار (حالياً)

                $isLocked = $isSectionLocked;

                if ($unlockAll) {
                    $isLocked = false;
                } elseif ($item instanceof Lecture) {
                    // قفل المحاضرة يعتمد على آخر محاضرة سابقة في نفس القسم
                    $prevLecture = null;
                    for ($j = $i - 1; $j >= 0; $j--) {
                        $prev = $items[$j]->item;
                        if ($prev instanceof Lecture) { $prevLecture = $prev; break; }
                    }
                    if ($prevLecture) {
                        $prevWp = $prevLecture->watchProgress->firstWhere('user_id', $user->id);
                        $prevMin = $prevLecture->min_watch_percent_to_unlock_next;
                        $prevThreshold = $prevMin !== null ? (int) $prevMin : 90;
                        if (! $prevWp || (! $prevWp->is_completed && (int) $prevWp->progress_percent < $prevThreshold)) {
                            $isLocked = true;
                        }
                    }
                } elseif ($item instanceof \App\Models\LearningPattern) {
                    $isLocked = $isSectionLocked;
                }

                $key = ($item instanceof Lecture) ? ('lecture:' . $item->id)
                    : ($item instanceof \App\Models\LearningPattern ? ('pattern:' . $item->id) : null);

                if ($key) {
                    $itemLocks[$key] = $isLocked ? 1 : 0;
                }
            }
        }

        return response()->json([
            'success' => true,
            'locks' => $itemLocks,
        ]);
    }

    /**
     * تحديث تقدم مشاهدة محاضرة (نسبة المشاهدة من الفيديو).
     */
    public function updateLectureProgress(Request $request, $courseId, $lectureId): JsonResponse
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);
        $lecture = $course->lectures()->findOrFail($lectureId);

        if (! $this->scholarshipVisibility->contentVisibleToStudent($course, Lecture::class, (int) $lectureId, $user)) {
            return response()->json(['success' => false, 'message' => 'هذه المحاضرة غير متاحة لك'], 403);
        }

        $data = $request->validate([
            'current_sec' => 'required|numeric|min:0',
            'duration_sec' => 'required|numeric|min:1',
        ]);

        $currentSec = (int) round($data['current_sec']);
        $durationSec = (int) round($data['duration_sec']);

        /** @var LectureWatchProgress $progress */
        $progress = LectureWatchProgress::firstOrNew([
            'lecture_id' => $lecture->id,
            'user_id' => $user->id,
        ]);
        $minPercent = $lecture->min_watch_percent_to_unlock_next;
        $expectedDurationSec = $lecture->duration_minutes
            ? (int) round(((float) $lecture->duration_minutes) * 60)
            : null;
        $progress->updateFromSample($currentSec, $durationSec, $minPercent, $expectedDurationSec);

        $sectionsForProgress = $course->activeSections()
            ->with([
                'visibleStudents:id', 'visibleGroups.members:id',
                'activeItems' => fn ($q) => $q->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']),
            ])
            ->orderBy('order')
            ->get();
        $sectionsForProgress = $this->scholarshipVisibility->filterSectionsForStudent($sectionsForProgress, $user, $course);
        list($courseProgressPct, $totalItems, $completedItems) = $this->courseProgress->recalculateAndSync(
            $user,
            $course,
            $sectionsForProgress
        );

        return response()->json([
            'success' => true,
            'progress_percent' => $progress->progress_percent,
            'is_completed' => $progress->is_completed,
            'course_progress' => $courseProgressPct,
            'total_items' => $totalItems,
            'completed_items' => $completedItems,
        ]);
    }

    /**
     * تسجيل إجابة الطالب على سؤال فيديو في المحاضرة (يُحسب كدرجة للتخرج).
     */
    public function submitLectureVideoQuestionAnswer(Request $request, $courseId, $lectureId, $videoQuestionId): JsonResponse
    {
        $user = Auth::user();
        $course = $user->activeCourses()->findOrFail($courseId);
        $lecture = $course->lectures()->findOrFail($lectureId);
        $videoQuestion = LectureVideoQuestion::where('id', $videoQuestionId)
            ->where('lecture_id', $lecture->id)
            ->firstOrFail();

        $answer = $request->input('answer', '');
        $isCorrect = $videoQuestion->checkAnswer($answer);
        $scoreEarned = $isCorrect ? (float) $videoQuestion->points : 0.0;

        LectureVideoQuestionAnswer::create([
            'user_id' => $user->id,
            'lecture_video_question_id' => $videoQuestion->id,
            'answer' => $answer,
            'is_correct' => $isCorrect,
            'score_earned' => $scoreEarned,
            'answered_at' => now(),
        ]);

        // إجابة أسئلة الفيديو قد تُكمل المحاضرة ضمن حساب تقدّم الكورس
        $this->updateCourseProgress($user->id, (int) $courseId);
        $courseProgressPct = $this->getCourseProgress($user->id, (int) $courseId);

        return response()->json([
            'correct' => $isCorrect,
            'score_earned' => $scoreEarned,
            'on_wrong' => $videoQuestion->on_wrong,
            'rewind_seconds' => $videoQuestion->rewind_seconds,
            'course_progress' => $courseProgressPct,
        ]);
    }

    /**
     * عرض الدرس في واجهة محمية
     */
    public function watchLesson($courseId, $lessonId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);
        
        // التحقق من أن الدرس نشط
        if (!$lesson->is_active) {
            return redirect()->route('my-courses.show', $course)
                ->with('error', 'هذا الدرس غير متاح حالياً');
        }
        
        // التحقق من ترتيب الدروس (لا يمكن مشاهدة درس قبل إكمال السابق)
        $previousLessons = $course->lessons()
            ->where('order', '<', $lesson->order)
            ->where('is_active', true)
            ->get();
            
        foreach ($previousLessons as $prevLesson) {
            $prevProgress = \App\Models\LessonProgress::where('user_id', $user->id)
                ->where('course_lesson_id', $prevLesson->id)
                ->first();
                
            if (!$prevProgress || !$prevProgress->is_completed) {
                return redirect()->route('my-courses.show', $course)
                    ->with('error', 'يجب إكمال الدروس السابقة أولاً');
            }
        }
        
        return view('student.my-courses.lesson-viewer', compact('course', 'lesson'));
    }

    /**
     * حساب متوسط التقدم
     */
    private function calculateAverageProgress($user)
    {
        $enrollments = $user->courseEnrollments()->where('status', 'active')->get();
        if ($enrollments->isEmpty()) return 0;
        
        $totalProgress = $enrollments->sum('progress');
        return round($totalProgress / $enrollments->count(), 1);
    }

    /**
     * تحديث تقدم الدرس
     */
    public function updateLessonProgress(Request $request, $courseId, $lessonId)
    {
        $user = Auth::user();
        
        // التحقق من أن الطالب مسجل في الكورس
        $course = $user->activeCourses()->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);

        $watchTime = (int) $request->input('watch_time', 0);
        $clientPercent = (int) min(100, max(0, $request->input('progress_percent', 0)));

        // احتساب النسبة من الثواني المشاهدة فعلياً (منع الغش: لا نعتمد على currentTime فقط)
        $totalSeconds = $lesson->duration_minutes ? (int) ($lesson->duration_minutes * 60) : 0;
        if ($totalSeconds > 0) {
            $progressPercent = (int) min(100, round(($watchTime / $totalSeconds) * 100));
        } else {
            $progressPercent = $clientPercent;
        }
        $existingLessonProgress = \App\Models\LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('course_lesson_id', $lessonId)
            ->first();
        if ($existingLessonProgress) {
            $progressPercent = max((int) $existingLessonProgress->progress_percent, $progressPercent);
            $watchTime = max((int) $existingLessonProgress->watch_time, $watchTime);
            $isCompleted = (bool) $existingLessonProgress->is_completed
                || $request->boolean('completed')
                || $progressPercent >= 90;
        } else {
            $isCompleted = $request->boolean('completed') || $progressPercent >= 90;
        }

        // تحديث أو إنشاء تقدم الدرس
        $progress = \App\Models\LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_lesson_id' => $lessonId
            ],
            [
                'is_completed' => $isCompleted,
                'completed_at' => $isCompleted ? ($existingLessonProgress?->completed_at ?? now()) : null,
                'watch_time' => $watchTime,
                'progress_percent' => $progressPercent,
            ]
        );

        // تحديث التقدم الإجمالي للكورس
        $sections = $course->activeSections()
            ->with([
                'visibleStudents:id', 'visibleGroups.members:id',
                'activeItems' => fn ($q) => $q->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']),
            ])
            ->orderBy('order')
            ->get();
        $sections = $this->scholarshipVisibility->filterSectionsForStudent($sections, $user, $course);
        list($progressPct, $totalItems, $completedItems) = $this->courseProgress->recalculateAndSync(
            $user,
            $course,
            $sections
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التقدم بنجاح',
            'progress' => $progress,
            'course_progress' => $progressPct,
            'total_items' => $totalItems,
            'completed_items' => $completedItems,
        ]);
    }

    /**
     * حساب قفل كل قسم بناءً على إعدادات فتح القسم (unlock_rule) وتقدم القسم السابق
     */
    private function computeSectionLockState($user, $allSections, bool $unlockAll = false)
    {
        foreach ($allSections as $section) {
            $total = 0;
            $completed = 0;
            foreach ($section->activeItems as $item) {
                $entity = $item->item;
                if (!$entity) continue;
                $total++;
                if ($this->courseProgress->isItemCompletedForUser($entity, $user)) {
                    $completed++;
                }
            }
            $section->progress_percent = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
            $section->all_items_completed = $total > 0 && $completed >= $total;
        }
        foreach ($allSections as $section) {
            if ($unlockAll) {
                $section->is_locked = false;
                continue;
            }
            $prev = $allSections->where('parent_id', $section->parent_id)->where('order', '<', $section->order)->sortByDesc('order')->first();
            if (!$prev) {
                $section->is_locked = false;
                continue;
            }
            $rule = $section->unlock_rule ?? 'previous_all_items';
            if ($rule === 'always') {
                $section->is_locked = false;
                continue;
            }
            if ($rule === 'previous_all_items') {
                $section->is_locked = !($prev->all_items_completed ?? false);
                continue;
            }
            if ($rule === 'previous_percent') {
                $required = (int) ($section->unlock_percent ?? 100);
                $section->is_locked = ($prev->progress_percent ?? 0) < $required;
                continue;
            }
            $section->is_locked = false;
        }
    }

    /**
     * الحصول على تقدم الكورس (نسبة مئوية)
     */
    private function getCourseProgress($userId, $courseId)
    {
        $course = \App\Models\AdvancedCourse::findOrFail($courseId);
        $user = \App\Models\User::findOrFail($userId);
        $sections = $course->activeSections()
            ->with([
                'visibleStudents:id', 'visibleGroups.members:id',
                'activeItems' => fn ($q) => $q->with(['item', 'visibleStudents:id', 'visibleGroups.members:id']),
            ])
            ->orderBy('order')
            ->get();

        $sections = $this->scholarshipVisibility->filterSectionsForStudent($sections, $user, $course);

        return $this->courseProgress->getCourseProgress($user, $course, $sections);
    }

    /**
     * تحديث التقدم الإجمالي للكورس في جدول التسجيلات (يُستدعى أيضاً بعد تسليم الامتحان/الواجب/النمط)
     */
    public function updateCourseProgress($userId, $courseId)
    {
        $progress = $this->getCourseProgress($userId, $courseId);
        $this->courseProgress->syncEnrollmentProgress((int) $userId, (int) $courseId, (float) $progress);
    }
}
